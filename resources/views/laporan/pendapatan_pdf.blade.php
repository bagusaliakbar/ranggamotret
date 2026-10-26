<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan — RanggaMotret.id</title>
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
                    Website: https://ranggamotret.id | Email: finance@ranggamotret.id
                </div>
            </td>
            <td class="report-badge" style="width: 45%; vertical-align: top;">
                <h2 class="report-title">Laporan Pendapatan</h2>
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
        Kategori: <strong>{{ $kategori ? ($kategori === 'foto' ? 'Sesi Fotografi' : 'Akademi Kursus') : 'Semua Pemasukan (Foto & Kursus)' }}</strong> | 
        Metode Bayar: <strong>{{ $metode ? ($metode === 'gateway' ? 'Midtrans Payment Gateway' : 'Transfer Bank Manual') : 'Semua Metode Pembayaran' }}</strong> |
        Total Record: <strong>{{ count($pembayarans) }} Transaksi Lunas</strong>
    </div>

    <!-- Ringkasan Statistik KPI -->
    <table class="meta-kpi-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Omzet Pendapatan</div>
                <div class="kpi-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ $transaksiCount }} Transaksi Lunas</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Pendapatan Sesi Fotografi</div>
                <div class="kpi-value">Rp {{ number_format($pendapatanFoto, 0, ',', '.') }}</div>
                <div class="kpi-sub" style="color: #64748b;">Layanan Studio & Paket Foto</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Pendapatan Akademi Kursus</div>
                <div class="kpi-value" style="color: #4f46e5;">Rp {{ number_format($pendapatanKursus, 0, ',', '.') }}</div>
                <div class="kpi-sub" style="color: #4f46e5;">Pelatihan & LMS Online</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                @php
                    $avgOrder = $transaksiCount > 0 ? round($totalPendapatan / $transaksiCount) : 0;
                @endphp
                <div class="kpi-label">Rata-Rata per Transaksi</div>
                <div class="kpi-value">Rp {{ number_format($avgOrder, 0, ',', '.') }}</div>
                <div class="kpi-sub" style="color: #0284c7;">Rerata Nilai Keranjang</div>
            </td>
        </tr>
    </table>

    <!-- Data Table Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 14%;">No. Transaksi</th>
                <th style="width: 8%;" class="text-center">Tipe</th>
                <th style="width: 16%;">Nama Pelanggan</th>
                <th style="width: 22%;">Layanan / Materi Kursus</th>
                <th style="width: 13%;">Metode Pembayaran</th>
                <th style="width: 11%;" class="text-center">Tanggal Bayar</th>
                <th style="width: 12%;" class="text-right">Nominal Masuk</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayarans as $index => $p)
                @php
                    $isCourse = !empty($p->kursus_id);
                    $isGateway = str_contains($p->metode_pembayaran ?? '', 'Midtrans') || !empty($p->snap_token);
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono font-bold">
                        {{ $p->nomor_transaksi }}
                        @if($p->invoice)
                            <div style="font-size: 7.5px; color: #64748b; font-weight: normal;">
                                Inv: {{ $p->invoice->nomor_invoice }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isCourse)
                            <span class="badge badge-kursus">Kursus</span>
                        @else
                            <span class="badge badge-foto">Foto</span>
                        @endif
                    </td>
                    <td>
                        <div class="font-bold">{{ $p->user->name ?? 'Pelanggan' }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">
                            {{ $p->user->phone ?? ($p->user->email ?? '-') }}
                        </div>
                    </td>
                    <td>
                        @if($p->booking && $p->booking->isKursus())
                            <div class="font-bold">{{ $p->booking->kursus?->nama ?? 'Kursus Akademi' }}</div>
                            <div style="font-size: 7.5px; color: #64748b;">
                                Tingkat: {{ ucfirst($p->booking->kursus?->level ?? 'Semua') }} • {{ $p->booking->nomor_booking }}
                            </div>
                        @elseif($p->booking)
                            <div class="font-bold">{{ $p->booking->layanan?->nama ?? 'Sesi Pemotretan' }}</div>
                            <div style="font-size: 7.5px; color: #64748b;">
                                Paket: {{ $p->booking->paket?->nama_paket ?? '-' }}
                            </div>
                        @elseif($p->kursus)
                            <div class="font-bold">{{ $p->kursus->nama }}</div>
                            <div style="font-size: 7.5px; color: #64748b;">
                                Tingkat: {{ ucfirst($p->kursus->level ?? 'Semua') }}
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{ $isGateway ? 'Midtrans Snap' : ($p->metode_pembayaran ?: 'Transfer Bank') }}
                    </td>
                    <td class="text-center">
                        {{ date('d/m/Y H:i', strtotime($p->tanggal_pembayaran ?? $p->created_at)) }}
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada transaksi pendapatan lunas pada rentang tanggal dan kriteria filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($pembayarans) > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="7" class="text-right" style="letter-spacing: 0.5px;">TOTAL KESELURUHAN PENDAPATAN :</td>
                    <td class="text-right">Rp {{ number_format($pembayarans->sum('jumlah'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Tanda Tangan & Pengesahan Dokumen -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <div style="font-size: 8px; color: #64748b;">
                    * Dokumen ini digenerate secara otomatis oleh Sistem Keuangan & Reservasi RanggaMotret.id.<br>
                    * Seluruh angka pendapatan tercantum merupakan transaksi valid berstatus Lunas (Paid).
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div style="font-size: 8.5px; color: #334155; margin-bottom: 4px;">
                    Tasikmalaya, {{ now()->translatedFormat('d F Y') }}
                </div>
                <div class="stamp-box">
                    &#10003; VERIFIED & AUDITED
                </div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">
                    Mengetahui & Menyetujui,
                </div>
                <div style="margin-top: 35px; font-weight: bold; font-size: 10px; color: #0f172a; text-decoration: underline;">
                    Rangga Permana, S.Ds.
                </div>
                <div style="font-size: 8px; color: #64748b;">
                    Founder & Executive Director RanggaMotret.id
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
