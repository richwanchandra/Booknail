@extends('layouts.admin')
@section('title', 'Laporan & Ekspor Reservasi')
@section('pageTitle', 'Laporan & Ekspor Reservasi')
@section('pageSubtitle', 'Filter data reservasi secara dinamis dan ekspor laporan resmi ke format PDF atau CSV (Excel).')

@section('content')
<!-- ========================================== -->
<!-- 1. SUMMARY KPI SNAPSHOT                    -->
<!-- ========================================== -->
<div class="mb-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Reservasi Terfilter -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#1C1B1B]">{{ number_format($summary['total_bookings']) }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Total Reservasi Terpilih</div>
            <div class="mt-2 text-[11px] text-[#49454F]/80 flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ $summary['counts']['completed'] }} selesai</span>
                <span>•</span>
                <span>{{ $summary['counts']['in_progress'] }} jalan</span>
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/40 flex items-center justify-center text-[#B76E79]">
            <x-icon name="file-text" class="w-6 h-6" />
        </div>
    </div>

    <!-- Omset Layanan Selesai -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-2xl sm:text-3xl font-serif font-bold text-emerald-700">Rp {{ number_format($summary['completed_revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Omset Layanan Selesai</div>
            <div class="mt-2 text-[11px] text-emerald-700 font-medium">
                Pemasukan riil yang telah selesai
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-700">
            <x-icon name="sparkles" class="w-6 h-6" />
        </div>
    </div>

    <!-- Nilai Estimasi Keseluruhan -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-2xl sm:text-3xl font-serif font-bold text-[#B76E79]">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Estimasi Total Transaksi</div>
            <div class="mt-2 text-[11px] text-[#49454F]/80">
                Termasuk reservasi terkonfirmasi & jalan
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/30 flex items-center justify-center text-[#B76E79]">
            <x-icon name="calendar-check" class="w-6 h-6" />
        </div>
    </div>

    <!-- Total DP Terkumpul & Rasio Sumber -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-2xl sm:text-3xl font-serif font-bold text-[#1C1B1B]">Rp {{ number_format($summary['total_dp'], 0, ',', '.') }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Total Uang Muka (DP)</div>
            <div class="mt-2 text-[11px] text-[#49454F]/80">
                {{ $summary['counts']['online'] }} Online • {{ $summary['counts']['walk_in'] }} Walk-In
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-700">
            <x-icon name="clock" class="w-6 h-6" />
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. FILTER & EXPORT CONTROL PANEL           -->
<!-- ========================================== -->
<div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft mb-6 space-y-5">
    <!-- Top Bar: Judul Panel & Tombol Ekspor -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-4 border-b border-[#DCD9D9]/60">
        <div>
            <h3 class="font-serif font-bold text-lg text-[#1C1B1B] flex items-center gap-2">
                <span>Filter Laporan & Ekspor Data</span>
            </h3>
            <p class="text-xs text-[#49454F] mt-0.5">Tentukan rentang tanggal dan parameter filter, lalu unduh dokumen hasil laporan.</p>
        </div>

        <!-- Tombol Aksi Ekspor (PDF & CSV / Excel) -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <!-- Tombol Ekspor PDF -->
            <a href="{{ route('admin.reports.exportPdf', request()->all()) }}"
               target="_blank"
               class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-xs font-bold shadow-soft flex items-center justify-center gap-2 transition"
               title="Unduh Laporan Format PDF Siap Cetak (Landscape)">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh PDF</span>
            </a>

            <!-- Tombol Ekspor CSV (Excel) -->
            <a href="{{ route('admin.reports.exportCsv', request()->all()) }}"
               class="flex-1 sm:flex-none px-4 py-2.5 rounded-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-soft flex items-center justify-center gap-2 transition"
               title="Unduh Data Mentah Format CSV Kompatibel Microsoft Excel">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh CSV (Excel)</span>
            </a>
        </div>
    </div>

    <!-- Quick Period Presets -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
        <span class="text-[#49454F] font-bold shrink-0 text-[11px] uppercase tracking-wider mr-1">Preset:</span>
        <a href="{{ route('admin.reports.index', array_merge(request()->except('start_date', 'end_date', 'page'), ['period' => 'today'])) }}"
           class="px-3.5 py-1.5 rounded-full font-bold transition shrink-0 {{ ($period === 'today') ? 'bg-[#1C1B1B] text-white' : 'bg-[#F6F3F2] text-[#49454F] hover:bg-[#E8E6E5]' }}">
            Hari Ini
        </a>
        <a href="{{ route('admin.reports.index', array_merge(request()->except('start_date', 'end_date', 'page'), ['period' => 'this_week'])) }}"
           class="px-3.5 py-1.5 rounded-full font-bold transition shrink-0 {{ ($period === 'this_week') ? 'bg-[#1C1B1B] text-white' : 'bg-[#F6F3F2] text-[#49454F] hover:bg-[#E8E6E5]' }}">
            Minggu Ini
        </a>
        <a href="{{ route('admin.reports.index', array_merge(request()->except('start_date', 'end_date', 'page'), ['period' => 'this_month'])) }}"
           class="px-3.5 py-1.5 rounded-full font-bold transition shrink-0 {{ ($period === 'this_month' || (empty($period) && empty($startDate) && empty($endDate))) ? 'bg-[#1C1B1B] text-white' : 'bg-[#F6F3F2] text-[#49454F] hover:bg-[#E8E6E5]' }}">
            Bulan Ini
        </a>
        <a href="{{ route('admin.reports.index', array_merge(request()->except('start_date', 'end_date', 'page'), ['period' => 'last_month'])) }}"
           class="px-3.5 py-1.5 rounded-full font-bold transition shrink-0 {{ ($period === 'last_month') ? 'bg-[#1C1B1B] text-white' : 'bg-[#F6F3F2] text-[#49454F] hover:bg-[#E8E6E5]' }}">
            Bulan Lalu
        </a>
        <a href="{{ route('admin.reports.index', array_merge(request()->except('start_date', 'end_date', 'page'), ['period' => 'all'])) }}"
           class="px-3.5 py-1.5 rounded-full font-bold transition shrink-0 {{ ($period === 'all') ? 'bg-[#1C1B1B] text-white' : 'bg-[#F6F3F2] text-[#49454F] hover:bg-[#E8E6E5]' }}">
            Semua Waktu
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 pt-2">
        <!-- Tanggal Mulai -->
        <div>
            <label class="block text-[11px] font-bold text-[#1C1B1B] mb-1">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate ?? '' }}"
                   class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
        </div>

        <!-- Tanggal Selesai -->
        <div>
            <label class="block text-[11px] font-bold text-[#1C1B1B] mb-1">Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ $endDate ?? '' }}"
                   class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-[11px] font-bold text-[#1C1B1B] mb-1">Status Reservasi</label>
            <select name="status" class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $val => $lbl)
                    <option value="{{ $val }}" {{ ($status === $val) ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        <!-- Nail Artist Filter -->
        <div>
            <label class="block text-[11px] font-bold text-[#1C1B1B] mb-1">Nail Artist</label>
            <select name="artist_id" class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
                <option value="">Semua Teknisi</option>
                @foreach($artists as $art)
                    <option value="{{ $art->id }}" {{ ($artistId == $art->id) ? 'selected' : '' }}>
                        {{ $art->user->name ?? 'Artist #'.$art->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Sumber Pesanan Filter -->
        <div>
            <label class="block text-[11px] font-bold text-[#1C1B1B] mb-1">Sumber Pesanan</label>
            <select name="source" class="w-full px-3.5 py-2 rounded-xl bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
                <option value="">Semua Sumber</option>
                <option value="online" {{ ($source === 'online') ? 'selected' : '' }}>Online Website</option>
                <option value="walk_in" {{ ($source === 'walk_in') ? 'selected' : '' }}>Walk-In Kasir</option>
            </select>
        </div>

        <!-- Tombol Filter & Reset -->
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 py-2 rounded-xl bg-[#1C1B1B] hover:bg-[#333] text-white text-xs font-bold transition">
                Terapkan
            </button>
            <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-xl border border-[#DCD9D9] hover:bg-[#F6F3F2] text-[#49454F] text-xs font-bold transition" title="Reset Semua Filter">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- 3. PREVIEW DATA TABLE                      -->
<!-- ========================================== -->
<div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft overflow-hidden">
    <div class="p-6 border-b border-[#DCD9D9]/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h3 class="font-serif font-bold text-lg text-[#1C1B1B]">Hasil Pratinjau Laporan</h3>
            <p class="text-xs text-[#49454F] mt-0.5">
                Menampilkan data reservasi yang sesuai dengan kriteria filter terpilih.
            </p>
        </div>
        <div class="text-xs font-bold text-[#49454F] bg-[#F6F3F2] px-3.5 py-1.5 rounded-full border border-[#DCD9D9]/70">
            Total Ditemukan: <span class="text-[#B76E79]">{{ number_format($summary['total_bookings']) }}</span> Janji Temu
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-[#F6F3F2] border-b border-[#DCD9D9]/80 text-[#49454F] uppercase font-bold tracking-wider text-[11px]">
                    <th class="py-3.5 px-4">No</th>
                    <th class="py-3.5 px-4">Kode Booking</th>
                    <th class="py-3.5 px-4">Tanggal & Jam</th>
                    <th class="py-3.5 px-4">Pelanggan</th>
                    <th class="py-3.5 px-4">Layanan & Durasi</th>
                    <th class="py-3.5 px-4">Nail Artist</th>
                    <th class="py-3.5 px-4">Sumber</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Total Biaya</th>
                    <th class="py-3.5 px-4 text-right">Uang Muka (DP)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DCD9D9]/60">
                @forelse($bookings as $index => $b)
                    @php
                        $firstDetail = $b->bookingDetails->first();
                        $durationMin = $b->bookingDetails->sum('duration_minutes') ?: 60;
                    @endphp
                    <tr class="hover:bg-[#F4D7D4]/10 transition">
                        <td class="py-4 px-4 text-[#49454F] font-semibold">
                            {{ $bookings->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap font-mono font-bold text-[#B76E79]">
                            <a href="{{ route('admin.bookings.index', ['q' => $b->booking_code]) }}" class="hover:underline" title="Lihat di reservasi">
                                #{{ $b->booking_code }}
                            </a>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-bold text-[#1C1B1B]">{{ $b->formatted_booking_date }}</div>
                            <div class="text-[#49454F] text-[11px] mt-0.5">{{ $b->time_range }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-[#1C1B1B]">{{ $b->customer_display_name }}</div>
                            <div class="text-[#49454F] text-[11px]">{{ $b->customer_display_phone }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-[#1C1B1B]">{{ $firstDetail->service->name ?? 'Layanan Salon' }}</div>
                            @if($b->bookingDetails->count() > 1)
                                <div class="text-[10px] text-[#B76E79] font-medium">+{{ $b->bookingDetails->count() - 1 }} layanan tambahan</div>
                            @endif
                            <div class="text-[10px] text-[#49454F] mt-0.5">⏱ {{ $durationMin }} menit</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-[#1C1B1B]">{{ $b->artist->user->name ?? 'Belum Ditugaskan' }}</div>
                            <div class="text-[10px] text-[#49454F]">Nail Artist</div>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($b->is_walk_in)
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-bold">Walk-In</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] bg-sky-50 text-sky-700 font-bold">Online</span>
                            @endif
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $b->status_badge_class }}">
                                {{ $b->status_label }}
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-right font-bold text-[#1C1B1B]">
                            {{ $b->formatted_total_price }}
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap text-right text-xs">
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($b->dp_amount, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-[#49454F]">
                            <x-icon name="file-text" class="w-8 h-8 mx-auto text-[#49454F]/40 mb-2" />
                            <div class="font-bold text-sm">Tidak Ada Data Reservasi</div>
                            <div class="text-xs text-[#49454F]/70 mt-1">Coba sesuaikan filter rentang tanggal atau status untuk menemukan data.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="p-4 border-t border-[#DCD9D9]/60">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection

