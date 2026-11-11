<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reservasi - Nail Glam Studio</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #1C1B1B;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #B76E79;
            padding-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .studio-title {
            font-size: 16pt;
            font-weight: bold;
            color: #B76E79;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .studio-subtitle {
            font-size: 8pt;
            color: #49454F;
            margin-top: 2px;
        }

        .report-title-box {
            text-align: right;
        }

        .report-main-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1C1B1B;
            text-transform: uppercase;
            margin: 0;
        }

        .report-meta {
            font-size: 7.5pt;
            color: #49454F;
            margin-top: 3px;
        }

        /* Summary KPI Cards */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 14px;
        }

        .summary-card {
            background-color: #FAF8F8;
            border: 1px solid #DCD9D9;
            border-radius: 6px;
            padding: 6px 10px;
            text-align: center;
        }

        .summary-card.highlight {
            background-color: #FDF4F5;
            border-color: #F4D7D4;
        }

        .summary-label {
            font-size: 7pt;
            color: #49454F;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 12pt;
            font-weight: bold;
            color: #1C1B1B;
            margin-top: 2px;
        }

        .summary-card.highlight .summary-value {
            color: #B76E79;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 7.5pt;
        }

        .data-table th {
            background-color: #F4D7D4;
            color: #1C1B1B;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 6.8pt;
            letter-spacing: 0.3px;
            padding: 6px 5px;
            border: 1px solid #DCD9D9;
            text-align: left;
        }

        .data-table td {
            padding: 5px;
            border: 1px solid #E5E3E3;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #FCFBFB;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-confirmed { background-color: #DCFCE7; color: #15803D; }
        .badge-completed { background-color: #D1FAE5; color: #047857; }
        .badge-in_progress { background-color: #EDE9FE; color: #6D28D9; }
        .badge-pending { background-color: #FEF3C7; color: #B45309; }
        .badge-cancelled { background-color: #FEE2E2; color: #B91C1C; }

        .badge-source {
            background-color: #F3F4F6;
            color: #374151;
            font-size: 6.5pt;
            padding: 1px 4px;
            border-radius: 3px;
        }

        /* Footer & Signatures */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: right;
            padding-right: 20px;
        }

        .sign-space {
            height: 45px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="studio-title">NAIL GLAM STUDIO</div>
                <div class="studio-subtitle">Ruko Veranda, Metland Puri, Cipondoh, Kota Tangerang · Telp/WA: 0812-3456-7890</div>
                <div class="studio-subtitle">Sistem Informasi Reservasi & Manajemen Salon Kecantikan Kuku</div>
            </td>
            <td style="width: 45%;" class="report-title-box">
                <div class="report-main-title">LAPORAN HASIL RESERVASI</div>
                <div class="report-meta">
                    <strong>Periode:</strong> 
                    @if($startDate && $endDate)
                        {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMM Y') }}
                    @elseif($startDate)
                        Mulai {{ \Carbon\Carbon::parse($startDate)->locale('id')->isoFormat('D MMM Y') }}
                    @elseif($endDate)
                        Sampai {{ \Carbon\Carbon::parse($endDate)->locale('id')->isoFormat('D MMM Y') }}
                    @else
                        Semua Waktu (All Time)
                    @endif
                </div>
                <div class="report-meta">
                    <strong>Status:</strong> {{ $statusLabel }}
                    @if($artist) | <strong>Teknisi:</strong> {{ $artist->user->name }} @endif
                </div>
                <div class="report-meta">Dicetak: {{ $printedAt }} | Oleh: {{ $adminName }}</div>
            </td>
        </tr>
    </table>

    <!-- KPI Summary Row -->
    <table class="summary-table">
        <tr>
            <td class="summary-card" style="width: 20%;">
                <div class="summary-label">Total Reservasi</div>
                <div class="summary-value">{{ number_format($totalBookings) }}</div>
            </td>
            <td class="summary-card highlight" style="width: 25%;">
                <div class="summary-label">Omset Treatment Selesai</div>
                <div class="summary-value">Rp {{ number_format($completedRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="width: 25%;">
                <div class="summary-label">Estimasi Nilai Pemesanan</div>
                <div class="summary-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="width: 15%;">
                <div class="summary-label">Total DP Terkumpul</div>
                <div class="summary-value">Rp {{ number_format($totalDp, 0, ',', '.') }}</div>
            </td>
            <td class="summary-card" style="width: 15%;">
                <div class="summary-label">Status Selesai / Jalan</div>
                <div class="summary-value" style="font-size: 10pt;">
                    <span style="color: #047857;">{{ $statusCounts['completed'] }}</span> / 
                    <span style="color: #6D28D9;">{{ $statusCounts['in_progress'] }}</span> / 
                    <span style="color: #B45309;">{{ $statusCounts['pending'] }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 9%;">Kode Booking</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 8%;">Jam</th>
                <th style="width: 14%;">Pelanggan</th>
                <th style="width: 18%;">Layanan Treatment</th>
                <th style="width: 11%;">Nail Artist</th>
                <th style="width: 7%;" class="text-center">Sumber</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 11%;" class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $index => $b)
                @php
                    $servicesStr = $b->bookingDetails->map(fn($d) => $d->service->name ?? 'Layanan')->implode(', ');
                    $durationMin = $b->bookingDetails->sum('duration_minutes') ?: 60;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-family: monospace;">{{ $b->booking_code }}</strong>
                    </td>
                    <td>
                        {{ $b->booking_date ? $b->booking_date->format('d/m/Y') : '-' }}
                    </td>
                    <td>
                        {{ substr($b->start_time, 0, 5) }} - {{ substr($b->end_time, 0, 5) }}
                        <div style="font-size: 6.5pt; color: #666;">({{ $durationMin }}m)</div>
                    </td>
                    <td>
                        <strong>{{ $b->customer_display_name }}</strong>
                        <div style="font-size: 6.8pt; color: #555;">{{ $b->customer_display_phone }}</div>
                    </td>
                    <td>
                        {{ $servicesStr }}
                    </td>
                    <td>
                        {{ $b->artist->user->name ?? 'Belum Ditugaskan' }}
                    </td>
                    <td class="text-center">
                        @if($b->is_walk_in)
                            <span class="badge-source">Walk-In</span>
                        @else
                            <span class="badge-source">Online</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $b->status }}">
                            {{ $b->status_label }}
                        </span>
                    </td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($b->total_price, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #888;">
                        Tidak ada data reservasi yang ditemukan untuk kriteria filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($bookings->isNotEmpty())
            <tfoot>
                <tr style="background-color: #F8F6F6; font-weight: bold;">
                    <td colspan="9" class="text-right" style="padding: 7px 5px; border-top: 2px solid #B76E79;">
                        TOTAL NILAI KESELURUHAN ({{ number_format($totalBookings) }} RESERVASI):
                    </td>
                    <td class="text-right" style="padding: 7px 5px; border-top: 2px solid #B76E79; color: #B76E79; font-size: 8.5pt;">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- Signature / Footer Section -->
    <table class="footer-table">
        <tr>
            <td style="width: 60%; vertical-align: top; font-size: 7pt; color: #777;">
                <div>Catatan:</div>
                <div>• Laporan ini diterbitkan otomatis oleh Sistem Informasi Reservasi Nail Glam Studio.</div>
                <div>• Data yang disajikan mencakup seluruh pemesanan online via website dan walk-in kasir.</div>
            </td>
            <td style="width: 40%;" class="signature-box">
                <div style="font-size: 8pt; color: #1C1B1B;">Tangerang, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</div>
                <div style="font-size: 8pt; font-weight: bold; margin-top: 2px;">Pengelola Nail Glam Studio,</div>
                <div class="sign-space"></div>
                <div style="font-weight: bold; text-decoration: underline; font-size: 8.5pt;">( {{ $adminName }} )</div>
                <div style="font-size: 7.5pt; color: #555;">Administrator Studio</div>
            </td>
        </tr>
    </table>

</body>
</html>

