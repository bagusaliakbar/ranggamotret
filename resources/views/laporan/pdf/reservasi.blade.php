<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reservasi Fotografi & Kursus — RanggaMotret.id</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        /* Header & Kop Surat */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 9px;
            font-weight: bold;
            color: #d97706;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .brand-contact {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 4px;
        }
        .report-badge {
            text-align: right;
            vertical-align: top;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .report-meta {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Metadata & KPI Cards */
        .meta-kpi-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            vertical-align: top;
        }
        .kpi-label {
            font-size: 7.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-sub {
            font-size: 7.5px;
            color: #059669;
            font-weight: bold;
            margin-top: 1px;
        }

        /* Filter Information Banner */
        .filter-banner {
            background-color: #f1f5f9;
            border-left: 3px solid #0f172a;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5px;
            color: #334155;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 8px;
            border: 1px solid #0f172a;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
        }
        .badge-foto {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-kursus {
            background-color: #eef2ff;
            color: #3730a3;
            border: 1px solid #c7d2fe;
        }
        .badge-status {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .badge-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .font-bold {
            font-weight: bold;
        }

        /* Total Footer */
        .tfoot-total td {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9px;
            color: #0f172a;
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            padding: 8px;
        }

        /* Signature / Validasi */
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .stamp-box {
            border: 1.5px dashed #059669;
            color: #059669;
            padding: 4px 8px;
            font-size: 7.5px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

    <!-- Header & Kop Perusahaan -->
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <h1 class="brand-title">RANGGAMOTRET.ID</h1>
                <div class="brand-subtitle">Creative Studio & Photography Academy</div>
                <div class="brand-contact">
                    Jl. Fotografi Kreatif No. 88, Tasikmalaya, Jawa Barat | WhatsApp: +62 812-3456-7890<br>
                    Website: https://ranggamotret.id | Email: reservations@ranggamotret.id
                </div>
            </td>
            <td class="report-badge" style="width: 45%; vertical-align: top;">
                <h2 class="report-title">Laporan Reservasi</h2>
                <div class="report-meta">
                    <strong>Periode:</strong> {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}<br>
                    <strong>Waktu Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>
                    <strong>Operator:</strong> {{ auth()->user()->name ?? 'Owner / Administrator' }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Filter Banner -->
    <div class="filter-banner">
        <strong>Filter Diterapkan:</strong> 
        Tipe Layanan: <strong>{{ $tipe === 'foto' ? 'Sesi Fotografi' : ($tipe === 'kursus' ? 'Akademi Kursus' : 'Semua Layanan (Foto & Kursus)') }}</strong> | 
        Status: <strong>{{ $status ?: 'Semua Status Reservasi' }}</strong> |
        Total Record: <strong>{{ count($allReservations) }} Pemesanan</strong>
    </div>

    <!-- Ringkasan Statistik KPI -->
    <table class="meta-kpi-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Semua Reservasi</div>
                <div class="kpi-value">{{ number_format($totalReservasiCount) }}</div>
                <div class="kpi-sub" style="color: #64748b;">Gabungan Foto & Kursus</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Reservasi Sesi Fotografi</div>
                <div class="kpi-value" style="color: #059669;">{{ number_format($totalFotoCount) }}</div>
                <div class="kpi-sub">{{ $totalSelesaiFoto }} Sesi Terjadwal / Selesai</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Pendaftaran Kursus</div>
                <div class="kpi-value" style="color: #4f46e5;">{{ number_format($totalKursusCount) }}</div>
                <div class="kpi-sub" style="color: #4f46e5;">{{ $totalLunasKursus }} Siswa Aktif Belajar</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Nilai Reservasi</div>
                <div class="kpi-value">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
                <div class="kpi-sub" style="color: #0284c7;">Estimasi Nilai Pemesanan</div>
            </td>
        </tr>
    </table>

    <!-- Data Table Rekap Reservasi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 14%;">Kode Booking / Trx</th>
                <th style="width: 8%;" class="text-center">Tipe</th>
                <th style="width: 16%;">Nama Pelanggan</th>
                <th style="width: 22%;">Layanan / Materi Pelatihan</th>
                <th style="width: 14%;">Jadwal / Format</th>
                <th style="width: 11%;" class="text-right">Biaya</th>
                <th style="width: 11%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allReservations as $index => $res)
                @php
                    $isSuccess = in_array($res->status, ['Dibayar', 'Selesai', 'Disetujui', 'Terjadwal']);
                    $isPending = in_array($res->status, ['Menunggu Konfirmasi', 'Menunggu Verifikasi', 'Menunggu Pembayaran']);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono font-bold">
                        {{ $res->kode }}
                        <div style="font-size: 7.5px; color: #64748b; font-weight: normal;">
                            {{ date('d/m/Y H:i', strtotime($res->tanggal_daftar)) }}
                        </div>
                    </td>
                    <td class="text-center">
                        @if($res->tipe === 'Kursus')
                            <span class="badge badge-kursus">Kursus</span>
                        @else
                            <span class="badge badge-foto">Foto</span>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold">{{ $res->nama_pelanggan }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">
                            {{ $res->kontak }}
                        </div>
                    </td>
                    <td>
                        <div class="font-bold">{{ $res->item }}</div>
                    </td>
                    <td>
                        {{ $res->jadwal_event }}
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($res->biaya, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if($isSuccess)
                            <span class="badge badge-success">{{ $res->status }}</span>
                        @elseif($isPending)
                            <span class="badge badge-warning">{{ $res->status }}</span>
                        @else
                            <span class="badge badge-status">{{ $res->status }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data reservasi fotografi maupun kursus pada filter rentang tanggal dan status yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($allReservations) > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="6" class="text-right" style="letter-spacing: 0.5px;">TOTAL NILAI PERPUTARAN RESERVASI :</td>
                    <td class="text-right">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Tanda Tangan & Pengesahan Dokumen -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <div style="font-size: 8px; color: #64748b;">
                    * Dokumen rekapitulasi operasional dan pendaftaran kursus RanggaMotret.id.<br>
                    * Data bersumber langsung dari sistem terintegrasi pemesanan foto dan LMS kursus.
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div style="font-size: 8.5px; color: #334155; margin-bottom: 4px;">
                    Tasikmalaya, {{ now()->translatedFormat('d F Y') }}
                </div>
                <div class="stamp-box">
                    &#10003; OPERATIONAL AUDITED
                </div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">
                    Mengetahui & Menyetujui,
                </div>
                <div style="margin-top: 35px; font-weight: bold; font-size: 10px; color: #0f172a; text-decoration: underline;">
                    Rangga Permana, S.Ds.
                </div>
                <div style="font-size: 8px; color: #64748b;">
                    Founder & Operational Lead RanggaMotret.id
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
