<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Katalog;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller Manajemen Laporan Eksekutif (ICONIX Robustness: LaporanController)
 * 
 * Bertanggung jawab atas:
 * - Laporan kinerja operasional sesi pemotretan & status reservasi
 * - Laporan keuangan & omzet pendapatan Midtrans / transfer bank
 * - Ekspor dokumen cetak PDF berstandar editorial luxury
 * - Ekspor berkas spreadsheet Excel (.xlsx) rapi dan terstruktur
 */
class LaporanController extends Controller
{
    /**
     * Menampilkan laporan ringkasan bisnis terpadu
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.reports.pendapatan');
    }

    /**
     * Helper: Menyiapkan query dan kalkulasi data pendapatan
     */
    private function getPendapatanData(Request $request): array
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $kategori = $request->query('kategori'); // all, foto, kursus
        $metode = $request->query('metode');

        $query = Pembayaran::with(['user', 'booking.layanan', 'booking.paket', 'kursus', 'invoice'])
            ->where('status', 'Dibayar');

        if ($startDate) {
            $query->whereDate('tanggal_pembayaran', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_pembayaran', '<=', $endDate);
        }

        if ($kategori === 'foto') {
            $query->whereNotNull('booking_id');
        } elseif ($kategori === 'kursus') {
            $query->whereNotNull('kursus_id');
        }

        if ($metode) {
            if ($metode === 'gateway') {
                $query->where(function($q) {
                    $q->where('metode_pembayaran', 'like', '%Midtrans%')
                      ->orWhereNotNull('snap_token');
                });
            } elseif ($metode === 'manual') {
                $query->where(function($q) {
                    $q->where('metode_pembayaran', 'not like', '%Midtrans%')
                      ->whereNull('snap_token');
                });
            }
        }

        // Ringkasan Statistik Pendapatan pada periode terpilih
        $baseQuery = Pembayaran::where('status', 'Dibayar')
            ->whereDate('tanggal_pembayaran', '>=', $startDate)
            ->whereDate('tanggal_pembayaran', '<=', $endDate);

        if ($kategori === 'foto') {
            $baseQuery->whereNotNull('booking_id');
        } elseif ($kategori === 'kursus') {
            $baseQuery->whereNotNull('kursus_id');
        }

        if ($metode) {
            if ($metode === 'gateway') {
                $baseQuery->where(function($q) {
                    $q->where('metode_pembayaran', 'like', '%Midtrans%')
                      ->orWhereNotNull('snap_token');
                });
            } elseif ($metode === 'manual') {
                $baseQuery->where(function($q) {
                    $q->where('metode_pembayaran', 'not like', '%Midtrans%')
                      ->whereNull('snap_token');
                });
            }
        }

        $totalPendapatan = (clone $baseQuery)->sum('jumlah');
        $pendapatanFoto = (clone $baseQuery)->whereNotNull('booking_id')->sum('jumlah');
        $pendapatanKursus = (clone $baseQuery)->whereNotNull('kursus_id')->sum('jumlah');
        $transaksiCount = (clone $baseQuery)->count();

        // Tren Bulanan Pendapatan (Database agnostic: SQLite vs MySQL)
        $driver = DB::connection()->getDriverName();
        $dateExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', tanggal_pembayaran)"
            : 'DATE_FORMAT(tanggal_pembayaran, "%Y-%m")';

        $monthlyRevenue = Pembayaran::where('status', 'Dibayar')
            ->select(DB::raw("{$dateExpression} as month"), DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        return compact(
            'query',
            'startDate',
            'endDate',
            'kategori',
            'metode',
            'totalPendapatan',
            'pendapatanFoto',
            'pendapatanKursus',
            'transaksiCount',
            'monthlyRevenue'
        );
    }

    /**
     * Submenu 1: Tampilan Web Laporan Pendapatan (Keuangan Studio & Kursus)
     */
    public function pendapatan(Request $request)
    {
        $data = $this->getPendapatanData($request);
        $pembayarans = (clone $data['query'])->latest('tanggal_pembayaran')->latest('id')->paginate(15)->withQueryString();

        return view('laporan.pendapatan', array_merge($data, [
            'pembayarans' => $pembayarans,
        ]));
    }

    /**
     * Cetak PDF Laporan Pendapatan (A4 Landscape, Luxury Editorial)
     */
    public function pendapatanPdf(Request $request)
    {
        $data = $this->getPendapatanData($request);
        $pembayarans = (clone $data['query'])->latest('tanggal_pembayaran')->latest('id')->get();

        $pdf = Pdf::loadView('laporan.pendapatan_pdf', array_merge($data, [
            'pembayarans' => $pembayarans,
        ]))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Pendapatan_' . $data['startDate'] . '_sd_' . $data['endDate'] . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Export Excel Laporan Pendapatan (.xlsx Terstruktur & Berdesain)
     */
    public function pendapatanExcel(Request $request): StreamedResponse
    {
        $data = $this->getPendapatanData($request);
        $pembayarans = (clone $data['query'])->latest('tanggal_pembayaran')->latest('id')->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('RanggaMotret.id')
            ->setLastModifiedBy('Owner RanggaMotret')
            ->setTitle('Laporan Pendapatan Keuangan')
            ->setSubject('Rekapitulasi Keuangan')
            ->setDescription('Laporan Pendapatan Sesi Fotografi dan Kursus Online RanggaMotret.id');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pendapatan');
        $sheet->setShowGridLines(true);

        // 1. Header Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'RANGGAMOTRET.ID — CREATIVE STUDIO & PHOTOGRAPHY ACADEMY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'LAPORAN PENDAPATAN & OMZET KEUANGAN RESMI');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFF8FAFC'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getRowDimension(2)->setRowHeight(24);

        // 2. Metadata Filter
        $sheet->setCellValue('A4', 'Periode Laporan:');
        $sheet->setCellValue('B4', date('d/m/Y', strtotime($data['startDate'])) . ' s/d ' . date('d/m/Y', strtotime($data['endDate'])));
        $sheet->setCellValue('D4', 'Waktu Unduh:');
        $sheet->setCellValue('E4', now()->translatedFormat('d F Y, H:i') . ' WIB');

        $kategoriLabel = $data['kategori'] ? ($data['kategori'] === 'foto' ? 'Sesi Fotografi' : 'Akademi Kursus') : 'Semua Pemasukan (Foto & Kursus)';
        $sheet->setCellValue('A5', 'Kategori:');
        $sheet->setCellValue('B5', $kategoriLabel);
        $sheet->setCellValue('D5', 'Dicetak Oleh:');
        $sheet->setCellValue('E5', auth()->user()->name ?? 'Owner / Administrator');

        $metodeLabel = $data['metode'] ? ($data['metode'] === 'gateway' ? 'Midtrans Snap' : 'Transfer Bank') : 'Semua Metode';
        $sheet->setCellValue('A6', 'Metode Bayar:');
        $sheet->setCellValue('B6', $metodeLabel);
        $sheet->setCellValue('D6', 'Total Transaksi:');
        $sheet->setCellValue('E6', count($pembayarans) . ' Transaksi Lunas');

        $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('D4:D6')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // 3. KPI Cards Summary
        // Box 1: Total Pendapatan
        $sheet->mergeCells('A8:B8');
        $sheet->setCellValue('A8', 'TOTAL OMZET');
        $sheet->mergeCells('A9:B9');
        $sheet->setCellValue('A9', (float)$data['totalPendapatan']);
        
        // Box 2: Pendapatan Foto
        $sheet->mergeCells('C8:D8');
        $sheet->setCellValue('C8', 'SESI FOTOGRAFI');
        $sheet->mergeCells('C9:D9');
        $sheet->setCellValue('C9', (float)$data['pendapatanFoto']);

        // Box 3: Pendapatan Kursus
        $sheet->mergeCells('E8:F8');
        $sheet->setCellValue('E8', 'AKADEMI KURSUS');
        $sheet->mergeCells('E9:F9');
        $sheet->setCellValue('E9', (float)$data['pendapatanKursus']);

        // Box 4: Transaksi Count
        $sheet->mergeCells('G8:H8');
        $sheet->setCellValue('G8', 'TRANSAKSI LUNAS');
        $sheet->mergeCells('G9:H9');
        $sheet->setCellValue('G9', $data['transaksiCount'] . ' Transaksi');

        $sheet->getStyle('A8:H8')->getFont()->setBold(true)->setSize(8)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A8:H8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8:H9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
        $sheet->getStyle('A8:H9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFE2E8F0'));

        $sheet->getStyle('A9:F9')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('A9:H9')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A9:H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 4. Table Header
        $headers = [
            'A11' => 'NO',
            'B11' => 'NO. TRANSAKSI',
            'C11' => 'NO. INVOICE',
            'D11' => 'TIPE',
            'E11' => 'NAMA PELANGGAN',
            'F11' => 'KONTAK',
            'G11' => 'LAYANAN / KURSUS',
            'H11' => 'METODE PEMBAYARAN',
            'I11' => 'TANGGAL PEMBAYARAN',
            'J11' => 'NOMINAL (RP)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A11:J11')->getFont()->setBold(true)->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A11:J11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getStyle('A11:J11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(11)->setRowHeight(26);

        // 5. Data Rows
        $row = 12;
        foreach ($pembayarans as $index => $p) {
            $isCourse = !empty($p->kursus_id);
            $isGateway = str_contains($p->metode_pembayaran ?? '', 'Midtrans') || !empty($p->snap_token);
            $layananName = '-';
            if ($p->booking) {
                $layananName = ($p->booking->layanan->nama ?? 'Sesi Pemotretan') . ' - ' . ($p->booking->paket->nama_paket ?? '-');
            } elseif ($p->kursus) {
                $layananName = $p->kursus->nama . ' (' . ucfirst($p->kursus->level ?? 'Semua') . ')';
            }

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $p->nomor_transaksi);
            $sheet->setCellValue('C' . $row, $p->invoice ? $p->invoice->nomor_invoice : '-');
            $sheet->setCellValue('D' . $row, $isCourse ? 'Kursus' : 'Foto');
            $sheet->setCellValue('E' . $row, $p->user->name ?? 'Pelanggan');
            $sheet->setCellValue('F' . $row, $p->user->phone ?? ($p->user->email ?? '-'));
            $sheet->setCellValue('G' . $row, $layananName);
            $sheet->setCellValue('H' . $row, $isGateway ? 'Midtrans Snap' : ($p->metode_pembayaran ?: 'Transfer Bank'));
            $sheet->setCellValue('I' . $row, date('d/m/Y H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)));
            $sheet->setCellValue('J' . $row, (float)$p->jumlah);

            // Styling baris data
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('J' . $row)->getFont()->setBold(true);

            // Zebra background
            if ($row % 2 === 1) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFE2E8F0'));
            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;
        }

        // 6. Summary Total Row
        $sheet->mergeCells('A' . $row . ':I' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL KESELURUHAN PENDAPATAN :');
        $sheet->setCellValue('J' . $row, '=SUM(J12:J' . ($row - 1) . ')');

        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getRowDimension($row)->setRowHeight(26);

        // 7. Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Pendapatan_' . $data['startDate'] . '_sd_' . $data['endDate'] . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Helper: Menyiapkan data agregasi reservasi fotografi dan kursus
     */
    private function getReservasiData(Request $request): array
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $tipe = $request->query('tipe', 'semua'); // semua, foto, kursus
        $status = $request->query('status');

        // 1. Data Reservasi Fotografi
        $queryFoto = Reservasi::with(['customer', 'layanan', 'paket', 'pembayaran'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $queryFoto->where('status', $status);
        }

        $fotoBookings = $queryFoto->latest()->get();

        // 2. Data Reservasi Kursus (dari tabel Pembayaran yang memiliki kursus_id)
        $queryKursus = Pembayaran::whereNotNull('kursus_id')
            ->with(['user', 'kursus.instruktur', 'invoice'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status) {
            $queryKursus->where('status', $status);
        }

        $kursusBookings = $queryKursus->latest()->get();

        // Gabungkan dalam format koleksi terpadu untuk tabel laporan
        $combined = collect();

        if ($tipe === 'semua' || $tipe === 'foto') {
            foreach ($fotoBookings as $fb) {
                $combined->push((object)[
                    'id' => $fb->id,
                    'kode' => $fb->nomor_booking,
                    'tipe' => 'Fotografi',
                    'nama_pelanggan' => $fb->customer->name ?? 'Tamu',
                    'kontak' => $fb->customer->phone ?? '-',
                    'item' => ($fb->layanan->nama ?? 'Sesi Foto') . ' (' . ($fb->paket->nama_paket ?? '-') . ')',
                    'jadwal_event' => date('d M Y', strtotime($fb->tanggal)) . ' ' . substr($fb->waktu, 0, 5) . ' WIB',
                    'biaya' => $fb->paket->harga ?? ($fb->pembayaran->jumlah ?? 0),
                    'status' => $fb->status,
                    'tanggal_daftar' => $fb->created_at,
                    'detail_url' => route('admin.bookings.show', $fb->id),
                ]);
            }
        }

        if ($tipe === 'semua' || $tipe === 'kursus') {
            foreach ($kursusBookings as $kb) {
                $combined->push((object)[
                    'id' => $kb->id,
                    'kode' => $kb->nomor_transaksi,
                    'tipe' => 'Kursus',
                    'nama_pelanggan' => $kb->user->name ?? 'Siswa',
                    'kontak' => $kb->user->phone ?? ($kb->user->email ?? '-'),
                    'item' => $kb->kursus->nama ?? 'Program Pelatihan',
                    'jadwal_event' => 'Akademi LMS (Online)',
                    'biaya' => $kb->jumlah,
                    'status' => $kb->status,
                    'tanggal_daftar' => $kb->created_at,
                    'detail_url' => route('admin.courses.reservations.index', ['search' => $kb->nomor_transaksi]),
                ]);
            }
        }

        // Urutkan berdasarkan tanggal pendaftaran terbaru
        $allReservations = $combined->sortByDesc('tanggal_daftar')->values();

        // Metrik Statistik
        $totalFotoCount = $fotoBookings->count();
        $totalKursusCount = $kursusBookings->count();
        $totalReservasiCount = $totalFotoCount + $totalKursusCount;

        $totalSelesaiFoto = $fotoBookings->whereIn('status', ['Selesai', 'Terjadwal', 'Dibayar', 'Disetujui'])->count();
        $totalLunasKursus = $kursusBookings->where('status', 'Dibayar')->count();
        $totalNilai = $allReservations->sum('biaya');

        return compact(
            'allReservations',
            'startDate',
            'endDate',
            'tipe',
            'status',
            'totalReservasiCount',
            'totalFotoCount',
            'totalKursusCount',
            'totalSelesaiFoto',
            'totalLunasKursus',
            'totalNilai'
        );
    }

    /**
     * Submenu 2: Tampilan Web Laporan Reservasi Fotografi dan Kursus
     */
    public function reservasi(Request $request)
    {
        $data = $this->getReservasiData($request);
        return view('laporan.reservasi', $data);
    }

    /**
     * Cetak PDF Laporan Reservasi Fotografi & Kursus (A4 Landscape, Luxury Editorial)
     */
    public function reservasiPdf(Request $request)
    {
        $data = $this->getReservasiData($request);

        $pdf = Pdf::loadView('laporan.reservasi_pdf', $data)->setPaper('a4', 'landscape');
        $filename = 'Laporan_Reservasi_' . $data['startDate'] . '_sd_' . $data['endDate'] . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Export Excel Laporan Reservasi (.xlsx Terstruktur & Berdesain)
     */
    public function reservasiExcel(Request $request): StreamedResponse
    {
        $data = $this->getReservasiData($request);
        $allReservations = $data['allReservations'];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('RanggaMotret.id')
            ->setLastModifiedBy('Owner RanggaMotret')
            ->setTitle('Laporan Reservasi Fotografi & Kursus')
            ->setSubject('Rekapitulasi Pemesanan')
            ->setDescription('Laporan Pemesanan Sesi Foto dan Pendaftaran Kursus Akademi RanggaMotret.id');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Reservasi');
        $sheet->setShowGridLines(true);

        // 1. Header Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'RANGGAMOTRET.ID — CREATIVE STUDIO & PHOTOGRAPHY ACADEMY');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'LAPORAN REKAPITULASI RESERVASI FOTOGRAFI & KURSUS');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFF8FAFC'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getRowDimension(2)->setRowHeight(24);

        // 2. Metadata Filter
        $sheet->setCellValue('A4', 'Periode Laporan:');
        $sheet->setCellValue('B4', date('d/m/Y', strtotime($data['startDate'])) . ' s/d ' . date('d/m/Y', strtotime($data['endDate'])));
        $sheet->setCellValue('D4', 'Waktu Unduh:');
        $sheet->setCellValue('E4', now()->translatedFormat('d F Y, H:i') . ' WIB');

        $tipeLabel = $data['tipe'] === 'foto' ? 'Sesi Fotografi' : ($data['tipe'] === 'kursus' ? 'Akademi Kursus' : 'Semua Layanan (Foto & Kursus)');
        $sheet->setCellValue('A5', 'Tipe Layanan:');
        $sheet->setCellValue('B5', $tipeLabel);
        $sheet->setCellValue('D5', 'Dicetak Oleh:');
        $sheet->setCellValue('E5', auth()->user()->name ?? 'Owner / Administrator');

        $sheet->setCellValue('A6', 'Filter Status:');
        $sheet->setCellValue('B6', $data['status'] ?: 'Semua Status');
        $sheet->setCellValue('D6', 'Total Reservasi:');
        $sheet->setCellValue('E6', count($allReservations) . ' Pemesanan');

        $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('D4:D6')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));

        // 3. KPI Cards Summary
        // Box 1: Total Reservasi
        $sheet->mergeCells('A8:B8');
        $sheet->setCellValue('A8', 'TOTAL RESERVASI');
        $sheet->mergeCells('A9:B9');
        $sheet->setCellValue('A9', $data['totalReservasiCount'] . ' Booking');

        // Box 2: Reservasi Foto
        $sheet->mergeCells('C8:D8');
        $sheet->setCellValue('C8', 'SESI FOTOGRAFI');
        $sheet->mergeCells('C9:D9');
        $sheet->setCellValue('C9', $data['totalFotoCount'] . ' Sesi Foto');

        // Box 3: Pendaftar Kursus
        $sheet->mergeCells('E8:F8');
        $sheet->setCellValue('E8', 'PENDAFTAR KURSUS');
        $sheet->mergeCells('E9:F9');
        $sheet->setCellValue('E9', $data['totalKursusCount'] . ' Peserta');

        // Box 4: Nilai Total Reservasi
        $sheet->mergeCells('G8:H8');
        $sheet->setCellValue('G8', 'NILAI TOTAL');
        $sheet->mergeCells('G9:H9');
        $sheet->setCellValue('G9', (float)$data['totalNilai']);

        $sheet->getStyle('A8:H8')->getFont()->setBold(true)->setSize(8)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A8:H8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A8:H9')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
        $sheet->getStyle('A8:H9')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFE2E8F0'));

        $sheet->getStyle('G9:H9')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('A9:H9')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A9:H9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 4. Table Header
        $headers = [
            'A11' => 'NO',
            'B11' => 'KODE BOOKING / TRX',
            'C11' => 'TIPE',
            'D11' => 'NAMA PELANGGAN',
            'E11' => 'KONTAK',
            'F11' => 'LAYANAN / PROGRAM KURSUS',
            'G11' => 'JADWAL / FORMAT',
            'H11' => 'BIAYA (RP)',
            'I11' => 'STATUS',
            'J11' => 'TANGGAL DAFTAR',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A11:J11')->getFont()->setBold(true)->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A11:J11')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getStyle('A11:J11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(11)->setRowHeight(26);

        // 5. Data Rows
        $row = 12;
        foreach ($allReservations as $index => $res) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $res->kode);
            $sheet->setCellValue('C' . $row, $res->tipe);
            $sheet->setCellValue('D' . $row, $res->nama_pelanggan);
            $sheet->setCellValue('E' . $row, $res->kontak);
            $sheet->setCellValue('F' . $row, $res->item);
            $sheet->setCellValue('G' . $row, $res->jadwal_event);
            $sheet->setCellValue('H' . $row, (float)$res->biaya);
            $sheet->setCellValue('I' . $row, $res->status);
            $sheet->setCellValue('J' . $row, date('d/m/Y H:i', strtotime($res->tanggal_daftar)));

            // Alignments & formats
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getFont()->setBold(true);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('H' . $row)->getFont()->setBold(true);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra background
            if ($row % 2 === 1) {
                $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }

            $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFE2E8F0'));
            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;
        }

        // 6. Summary Total Row
        $sheet->mergeCells('A' . $row . ':G' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL NILAI PERPUTARAN RESERVASI :');
        $sheet->setCellValue('H' . $row, '=SUM(H12:H' . ($row - 1) . ')');

        $sheet->getStyle('A' . $row . ':J' . $row)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getStyle('A' . $row . ':J' . $row)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF0F172A'));
        $sheet->getRowDimension($row)->setRowHeight(26);

        // 7. Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Reservasi_' . $data['startDate'] . '_sd_' . $data['endDate'] . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
