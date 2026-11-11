@extends('layouts.admin')
@section('title', 'Jadwal Janji Temu Mingguan')
@section('pageTitle', 'Jadwal Janji Temu Mingguan')
@section('pageSubtitle', 'Weekly Appointments Schedule — Kelola seluruh janji temu 7 hari dengan detail artist, station, & pelanggan.')

@section('content')
<!-- ========================================== -->
<!-- 1. WEEKLY KPI SNAPSHOT BAR                  -->
<!-- ========================================== -->
<div class="mb-8 grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Bookings -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#1C1B1B]">{{ number_format($kpi['total_bookings']) }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold flex items-center gap-1.5">
                <span>Total Janji Minggu Ini</span>
            </div>
            <div class="mt-2 text-[11px] text-[#49454F]/80">
                <span class="text-emerald-700 font-bold">{{ $kpi['completed'] }}</span> selesai, <span class="text-purple-700 font-bold">{{ $kpi['in_progress'] }}</span> jalan
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/50 flex items-center justify-center text-[#B76E79]">
            <x-icon name="calendar-days" class="w-6 h-6" />
        </div>
    </div>

    <!-- Confirmed vs Pending DP -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-emerald-700">{{ $kpi['confirmed'] }} <span class="text-xs font-sans font-normal text-[#49454F]">/ {{ $kpi['pending_dp'] }} DP</span></div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Terkonfirmasi vs Pending DP</div>
            <div class="mt-2 text-[11px] text-amber-700 font-medium">
                @if($kpi['pending_dp'] > 0)
                    ⏳ {{ $kpi['pending_dp'] }} perlu verifikasi bukti DP
                @else
                    Semua pembayaran DP aman
                @endif
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-700">
            <x-icon name="calendar-check" class="w-6 h-6" />
        </div>
    </div>

    <!-- Staff Roster Availability -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#B76E79]">{{ $kpi['staff_roster'] }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Kesiapan Roster Artist</div>
            <div class="mt-2 text-[11px] text-[#49454F]/80">
                Teknisi aktif & siap melayani
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/40 flex items-center justify-center text-[#B76E79]">
            <x-icon name="users" class="w-6 h-6" />
        </div>
    </div>

    <!-- Peak Day Utilization -->
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-xl font-serif font-bold text-[#1C1B1B] truncate max-w-[150px]" title="{{ $kpi['peak_day'] }}">{{ $kpi['peak_day'] }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Hari Paling Padat</div>
            <div class="mt-2 text-[11px] text-[#49454F]/80">
                Puncak okupansi 4 station
            </div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-700">
            <x-icon name="sparkles" class="w-6 h-6" />
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. NAVIGATION BAR & QUICK ACTIONS          -->
<!-- ========================================== -->
<div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft mb-6 space-y-4">
    <!-- Top Row: Date Navigation & Actions -->
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <!-- Date Navigator -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Prev Week Button -->
            <a href="{{ route('admin.appointments.index', array_merge(request()->except('week', 'date'), ['week' => $prevWeek])) }}"
               class="p-2.5 rounded-full border border-[#DCD9D9] hover:bg-[#F6F3F2] text-[#1C1B1B] transition shadow-sm"
               title="Minggu Sebelumnya">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Week Range Label & Cross-Month Badge -->
            <div class="flex items-center gap-2">
                <span class="text-base sm:text-lg font-serif font-bold text-[#1C1B1B]">
                    {{ $weekRangeText }}
                </span>
                @if($startOfWeek->month !== $endOfWeek->month)
                    <span class="px-2 py-0.5 rounded-full bg-[#F4D7D4]/60 text-[#B76E79] text-[10px] font-bold uppercase tracking-wider">
                        Lintas Bulan
                    </span>
                @endif
            </div>

            <!-- Next Week Button -->
            <a href="{{ route('admin.appointments.index', array_merge(request()->except('week', 'date'), ['week' => $nextWeek])) }}"
               class="p-2.5 rounded-full border border-[#DCD9D9] hover:bg-[#F6F3F2] text-[#1C1B1B] transition shadow-sm"
               title="Minggu Berikutnya">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <!-- Today Jump Button -->
            <a href="{{ route('admin.appointments.index', array_merge(request()->except('week', 'date'), ['week' => $todayWeek])) }}"
               class="px-3.5 py-1.5 rounded-full border border-[#B76E79] text-[#B76E79] hover:bg-[#B76E79] hover:text-white text-xs font-bold transition shadow-sm">
                Hari Ini
            </a>

            <!-- Jump to Specific Date Form -->
            <div class="relative">
                <input type="date" value="{{ $selectedDate->format('Y-m-d') }}"
                       onchange="window.location.href='{{ route('admin.appointments.index', array_merge(request()->except('week', 'date'), [])) }}' + (window.location.search ? '&' : '?') + 'week=' + this.value"
                       class="px-3 py-1.5 rounded-full border border-[#DCD9D9] text-xs font-medium text-[#49454F] bg-white focus:outline-none focus:border-[#B76E79] cursor-pointer"
                       title="Pilih tanggal acuan">
            </div>
        </div>

        <!-- Right Side: View Mode Switcher & Quick Actions -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="flex items-center p-1 rounded-full bg-[#F6F3F2] border border-[#DCD9D9]/70 text-xs font-bold">
                <a href="{{ route('admin.appointments.index', array_merge(request()->except('view'), ['view' => 'week'])) }}"
                   class="px-3 py-1.5 rounded-full transition {{ $viewMode === 'week' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                    Mingguan
                </a>
                <a href="{{ route('admin.appointments.index', array_merge(request()->except('view'), ['view' => 'day'])) }}"
                   class="px-3 py-1.5 rounded-full transition {{ $viewMode === 'day' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                    Harian
                </a>
                <a href="{{ route('admin.appointments.index', array_merge(request()->except('view'), ['view' => 'list'])) }}"
                   class="px-3 py-1.5 rounded-full transition {{ $viewMode === 'list' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                    Daftar (List)
                </a>
            </div>

            <!-- Quick Action: Block Slot Modal Button -->
            <button type="button" onclick="openBlockSlotModal()"
                    class="px-4 py-2.5 rounded-full border border-[#DCD9D9] hover:bg-[#F6F3F2] text-[#1C1B1B] text-xs font-bold flex items-center gap-2 transition shadow-sm">
                <x-icon name="calendar-x" class="w-4 h-4 text-amber-700" />
                <span>Blokir Slot / Cuti</span>
            </button>

            <!-- Quick Action: + New Appointment -->
            <a href="{{ route('admin.walk-in.create') }}"
               class="px-5 py-2.5 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-xs font-bold shadow-soft flex items-center gap-2 transition shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>+ Janji Temu Baru</span>
            </a>
        </div>
    </div>

    <!-- Bottom Row: Filters Bar (Artist, Status, Search) -->
    <form method="GET" action="{{ route('admin.appointments.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3 pt-3 border-t border-[#DCD9D9]/50">
        <input type="hidden" name="week" value="{{ $selectedDate->format('Y-m-d') }}">
        <input type="hidden" name="view" value="{{ $viewMode }}">

        <!-- Search Box -->
        <div class="relative flex-1">
            <input name="q" value="{{ $search ?? '' }}" placeholder="Cari kode booking, nama klien, atau no. telepon..."
                   class="w-full pl-10 pr-4 py-2 rounded-full bg-white border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79]">
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#49454F]/70">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Artist Filter -->
        <div class="w-full md:w-56">
            <select name="artist_id" onchange="this.form.submit()"
                    class="w-full px-4 py-2 rounded-full bg-white border border-[#DCD9D9] text-xs font-medium text-[#49454F] focus:outline-none focus:border-[#B76E79]">
                <option value="">Semua Nail Artist</option>
                @foreach($artists as $art)
                    <option value="{{ $art->id }}" {{ ($artistId == $art->id) ? 'selected' : '' }}>
                        {{ $art->user->name ?? 'Artist #'.$art->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status Filter -->
        <div class="w-full md:w-48">
            <select name="status" onchange="this.form.submit()"
                    class="w-full px-4 py-2 rounded-full bg-white border border-[#DCD9D9] text-xs font-medium text-[#49454F] focus:outline-none focus:border-[#B76E79]">
                <option value="">Semua Status</option>
                <option value="pending" {{ ($status === 'pending') ? 'selected' : '' }}>⏳ Menunggu DP</option>
                <option value="confirmed" {{ ($status === 'confirmed') ? 'selected' : '' }}>✓ Dikonfirmasi</option>
                <option value="in_progress" {{ ($status === 'in_progress') ? 'selected' : '' }}>⚡ Sedang Berjalan</option>
                <option value="completed" {{ ($status === 'completed') ? 'selected' : '' }}>★ Selesai</option>
                <option value="cancelled" {{ ($status === 'cancelled') ? 'selected' : '' }}>✕ Dibatalkan</option>
            </select>
        </div>

        <!-- Filter Submit / Reset -->
        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded-full bg-[#1C1B1B] text-white text-xs font-bold hover:bg-[#333] transition">
                Filter
            </button>
            @if($artistId || $status || $search)
                <a href="{{ route('admin.appointments.index', ['week' => $selectedDate->format('Y-m-d'), 'view' => $viewMode]) }}"
                   class="px-3 py-2 rounded-full border border-[#DCD9D9] text-xs text-[#49454F] hover:bg-[#F6F3F2] transition" title="Reset Filter">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- ========================================== -->
<!-- 3. MAIN SCHEDULE CONTENT AREA             -->
<!-- ========================================== -->

@if($viewMode === 'list')
    <!-- ========================================== -->
    <!-- LIST VIEW MODE                             -->
    <!-- ========================================== -->
    <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft overflow-hidden">
        <div class="p-6 border-b border-[#DCD9D9]/60 flex items-center justify-between">
            <div>
                <h3 class="font-serif font-bold text-lg text-[#1C1B1B]">Daftar Reservasi Mingguan</h3>
                <p class="text-xs text-[#49454F] mt-0.5">Seluruh pemesanan tercatat pada rentang {{ $weekRangeText }}</p>
            </div>
            <div class="text-xs font-bold text-[#49454F]">
                Total: {{ $weekBookings->count() }} Janji Temu
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#F6F3F2] border-b border-[#DCD9D9]/80 text-[#49454F] uppercase font-bold tracking-wider text-[11px]">
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4">Station</th>
                        <th class="py-3.5 px-4">Pelanggan</th>
                        <th class="py-3.5 px-4">Layanan & Durasi</th>
                        <th class="py-3.5 px-4">Nail Artist</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Total Biaya</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DCD9D9]/60">
                    @forelse($weekBookings as $b)
                        @php
                            $firstDetail = $b->bookingDetails->first();
                            $durationMin = $b->bookingDetails->sum('duration_minutes') ?: 60;
                            $stationNum = (($b->artist_id - 1) % $studioStationsCount) + 1;
                        @endphp
                        <tr class="hover:bg-[#F4D7D4]/10 transition">
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-bold text-[#1C1B1B]">{{ $b->formatted_booking_date }}</div>
                                <div class="text-[#B76E79] font-semibold text-[11px] mt-0.5">{{ $b->time_range }}</div>
                                <div class="text-[10px] text-[#49454F]/70 font-mono mt-0.5">#{{ $b->booking_code }}</div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full bg-[#F4D7D4]/40 text-[#B76E79] font-bold text-[11px] inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#B76E79]"></span>
                                    Station {{ $stationNum }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-[#1C1B1B]">{{ $b->customer_display_name }}</div>
                                <div class="text-[#49454F] text-[11px]">{{ $b->customer_display_phone }}</div>
                                @if($b->is_walk_in_order)
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-semibold">Walk-In</span>
                                @else
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] bg-sky-50 text-sky-700 font-semibold">Online</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-[#1C1B1B]">{{ $firstDetail->service->name ?? 'Layanan Salon' }}</div>
                                @if($b->bookingDetails->count() > 1)
                                    <div class="text-[10px] text-[#49454F]">+{{ $b->bookingDetails->count() - 1 }} layanan tambahan</div>
                                @endif
                                <div class="text-[10px] text-[#49454F] mt-0.5">⏱ {{ $durationMin }} menit</div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#B76E79]/20 text-[#B76E79] font-bold flex items-center justify-center text-xs">
                                        {{ substr($b->artist->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#1C1B1B]">{{ $b->artist->user->name ?? 'Belum Ditugaskan' }}</div>
                                        <div class="text-[10px] text-[#49454F]">Nail Artist</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $b->status_badge_class }}">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap font-bold text-[#1C1B1B]">
                                {{ $b->formatted_total_price }}
                            </td>
                            <td class="py-4 px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($b->custom_design_photo)
                                        <button type="button" onclick="previewDesignImage('{{ $b->custom_design_photo_url }}', 'Desain Pelanggan #{{ $b->booking_code }}', '{{ addslashes($b->notes ?? '') }}')"
                                                class="p-1.5 rounded-lg border border-[#DCD9D9] text-[#B76E79] hover:bg-[#F4D7D4]/30 transition" title="Lihat Referensi Desain">
                                            <x-icon name="palette" class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.bookings.index', ['q' => $b->booking_code]) }}"
                                       class="px-2.5 py-1 rounded-full bg-[#F6F3F2] hover:bg-[#DCD9D9] text-[#1C1B1B] text-[11px] font-bold transition">
                                        Kelola
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[#49454F]">
                                <x-icon name="calendar-x" class="w-8 h-8 mx-auto text-[#49454F]/50 mb-2" />
                                <div class="font-bold text-sm">Tidak Ada Janji Temu Ditemukan</div>
                                <div class="text-xs text-[#49454F]/70 mt-1">Tidak ada jadwal booking pada rentang waktu atau filter ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@elseif($viewMode === 'day')
    <!-- ========================================== -->
    <!-- DAY VIEW MODE (STATION BREAKDOWN)          -->
    <!-- ========================================== -->
    <div class="space-y-6">
        <!-- Day Selector Pill Bar -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            @foreach($days as $day)
                <a href="{{ route('admin.appointments.index', array_merge(request()->except('week', 'date'), ['week' => $day['date'], 'view' => 'day'])) }}"
                   class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 shrink-0 {{ $selectedDate->format('Y-m-d') === $day['date'] ? 'bg-[#B76E79] text-white shadow-soft' : ($day['is_today'] ? 'bg-[#F4D7D4]/60 text-[#B76E79] border border-[#B76E79]/50' : 'bg-white border border-[#DCD9D9] text-[#49454F] hover:bg-[#F6F3F2]') }}">
                    <span>{{ $day['day_name'] }}</span>
                    <span class="text-[11px] opacity-80">{{ $day['day_num'] }} {{ $day['month_short'] }}</span>
                    @if($day['bookings_count'] > 0)
                        <span class="w-5 h-5 rounded-full {{ $selectedDate->format('Y-m-d') === $day['date'] ? 'bg-white/20 text-white' : 'bg-[#B76E79]/20 text-[#B76E79]' }} flex items-center justify-center text-[10px]">
                            {{ $day['bookings_count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>

        @php
            $currentDayInfo = collect($days)->firstWhere('date', $selectedDate->format('Y-m-d')) ?? $days[0];
            $currentDayBookings = $currentDayInfo['bookings'];
        @endphp

        <!-- 4 Stations Column Layout for the Selected Day -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @for($st = 1; $st <= $studioStationsCount; $st++)
                @php
                    $stationBookings = $currentDayBookings->filter(fn($b) => $b->station_number === $st)->values();
                    // Artist typically assigned to this station
                    $assignedArtist = $artists[$st - 1] ?? null;
                @endphp
                <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft p-5 flex flex-col h-full">
                    <!-- Station Header -->
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#DCD9D9]/60">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#B76E79]"></span>
                                <h4 class="font-serif font-bold text-base text-[#1C1B1B]">Station {{ $st }}</h4>
                            </div>
                            <div class="text-[11px] text-[#49454F] mt-0.5">
                                @if($assignedArtist)
                                    Default: <span class="font-semibold text-[#1C1B1B]">{{ $assignedArtist->user->name }}</span>
                                @else
                                    Nail Station Studio
                                @endif
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-[#F6F3F2] text-[#49454F] font-bold text-[10px]">
                            {{ $stationBookings->count() }} Janji
                        </span>
                    </div>

                    <!-- Station Appointments -->
                    <div class="space-y-3 flex-1">
                        @forelse($stationBookings as $b)
                            @php
                                $detail = $b->bookingDetails->first();
                                $duration = $b->bookingDetails->sum('duration_minutes') ?: 60;
                            @endphp
                            <div class="bg-[#F6F3F2]/60 hover:bg-[#F4D7D4]/20 border border-[#DCD9D9]/80 rounded-2xl p-4 transition space-y-3">
                                <!-- Time & Status -->
                                <div class="flex items-center justify-between gap-1 text-[11px]">
                                    <span class="font-bold text-[#B76E79] bg-white px-2 py-0.5 rounded-full border border-[#DCD9D9]/50 shadow-2xs">
                                        {{ $b->time_range }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $b->status_badge_class }}">
                                        {{ $b->status_label }}
                                    </span>
                                </div>

                                <!-- Client & Service -->
                                <div>
                                    <div class="font-bold text-sm text-[#1C1B1B]">{{ $b->customer_display_name }}</div>
                                    <div class="text-[11px] text-[#49454F]">{{ $b->customer_display_phone }}</div>
                                    <div class="text-xs font-semibold text-[#1C1B1B] mt-1.5 flex items-center gap-1 text-emerald-800">
                                        <x-icon name="sparkles" class="w-3.5 h-3.5 shrink-0 text-[#B76E79]" />
                                        <span class="truncate">{{ $detail->service->name ?? 'Layanan Studio' }}</span>
                                    </div>
                                    <div class="text-[10px] text-[#49454F] mt-0.5">⏱ {{ $duration }} menit • {{ $b->formatted_total_price }}</div>
                                </div>

                                <!-- Artist Assigned & Actions -->
                                <div class="pt-2 border-t border-[#DCD9D9]/60 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[11px] text-[#1C1B1B]">
                                        <div class="w-5 h-5 rounded-full bg-[#B76E79] text-white text-[9px] font-bold flex items-center justify-center">
                                            {{ substr($b->artist->user->name ?? 'A', 0, 1) }}
                                        </div>
                                        <span class="font-semibold text-xs">{{ $b->artist->user->name ?? 'Artist' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if($b->custom_design_photo)
                                            <button type="button" onclick="previewDesignImage('{{ $b->custom_design_photo_url }}', 'Desain #{{ $b->booking_code }}', '{{ addslashes($b->notes ?? '') }}')"
                                                    class="p-1 rounded bg-white text-[#B76E79] border border-[#DCD9D9] hover:bg-[#F4D7D4]/30" title="Desain">
                                                <x-icon name="palette" class="w-3 h-3" />
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.bookings.index', ['q' => $b->booking_code]) }}"
                                           class="text-[10px] font-bold text-[#B76E79] hover:underline">
                                            Detail &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-40 rounded-2xl border-2 border-dashed border-[#DCD9D9] flex flex-col items-center justify-center text-center p-4 text-[#49454F]/70">
                                <x-icon name="clock" class="w-6 h-6 mb-1 text-[#49454F]/40" />
                                <div class="text-xs font-semibold">Station Kosong</div>
                                <div class="text-[10px]">Tersedia untuk walk-in</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endfor
        </div>
    </div>

@else
    <!-- ======================================================= -->
    <!-- 7-DAY WEEKLY CALENDAR BREAKDOWN (DEFAULT 'WEEK' VIEW)   -->
    <!-- ======================================================= -->
    <div class="overflow-x-auto pb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-4 min-w-[1100px] xl:min-w-0">
            @foreach($days as $day)
                <div class="bg-white rounded-3xl border {{ $day['is_today'] ? 'border-[#B76E79] ring-2 ring-[#B76E79]/30 shadow-md' : 'border-[#DCD9D9]/80 shadow-soft' }} flex flex-col transition hover:border-[#B76E79]/60">
                    <!-- Day Column Header -->
                    <div class="p-4 border-b border-[#DCD9D9]/70 {{ $day['is_today'] ? 'bg-[#F4D7D4]/30 rounded-t-3xl' : '' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold uppercase tracking-wider {{ $day['is_today'] ? 'text-[#B76E79]' : 'text-[#49454F]' }}">
                                {{ $day['day_name'] }}
                            </span>
                            @if($day['is_today'])
                                <span class="px-2 py-0.5 rounded-full bg-[#B76E79] text-white text-[9px] font-bold uppercase tracking-wider">
                                    Hari Ini
                                </span>
                            @endif
                        </div>

                        <!-- Date & Bookings Count Badge -->
                        <div class="flex items-baseline justify-between">
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-serif font-bold text-[#1C1B1B]">{{ $day['day_num'] }}</span>
                                <span class="text-xs font-semibold text-[#49454F]">{{ $day['month_short'] }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $day['bookings_count'] > 0 ? 'bg-[#B76E79]/15 text-[#B76E79]' : 'bg-[#F6F3F2] text-[#49454F]/70' }}">
                                {{ $day['bookings_count'] }} Janji
                            </span>
                        </div>

                        <!-- Station Capacity Progress Bar -->
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-[10px] text-[#49454F] mb-1">
                                <span>Kapasitas 4 Station</span>
                                <span class="font-bold {{ $day['capacity_pct'] >= 80 ? 'text-rose-700' : ($day['capacity_pct'] >= 50 ? 'text-amber-700' : 'text-emerald-700') }}">
                                    {{ $day['capacity_pct'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-[#E8E6E5] rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-300 {{ $day['capacity_pct'] >= 80 ? 'bg-rose-500' : ($day['capacity_pct'] >= 50 ? 'bg-amber-500' : 'bg-[#B76E79]') }}"
                                     style="width: {{ $day['capacity_pct'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Day Appointment Cards Column -->
                    <div class="p-3 space-y-3 flex-1 flex flex-col min-h-[350px]">
                        @forelse($day['bookings'] as $b)
                            @php
                                $detail = $b->bookingDetails->first();
                                $duration = $b->bookingDetails->sum('duration_minutes') ?: 60;
                            @endphp
                            <div class="bg-[#FDFBFB] hover:bg-white rounded-2xl border border-[#DCD9D9]/80 p-3 shadow-2xs hover:shadow-soft transition-all duration-200 space-y-2.5 relative group">
                                <!-- Top Bar: Time slot & Station pill -->
                                <div class="flex items-center justify-between gap-1">
                                    <div class="flex items-center gap-1">
                                        <span class="px-2 py-0.5 rounded-md bg-[#F4D7D4]/50 text-[#B76E79] font-bold text-[10px]">
                                            {{ $b->time_range }}
                                        </span>
                                        <span class="text-[9px] text-[#49454F]/80">({{ $duration }}m)</span>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-bold text-[9px]">
                                        {{ $b->assigned_station }}
                                    </span>
                                </div>

                                <!-- Client Name & Walk-In/Online Source -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <div class="font-bold text-xs text-[#1C1B1B] truncate" title="{{ $b->customer_display_name }}">
                                            {{ $b->customer_display_name }}
                                        </div>
                                        @if($b->is_walk_in_order)
                                            <span class="text-[8px] uppercase tracking-wider font-bold px-1 py-0.2 rounded bg-amber-100 text-amber-800">Walk-In</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-[#49454F] truncate">{{ $b->customer_display_phone }}</div>
                                </div>

                                <!-- Service Details -->
                                <div class="bg-white rounded-xl p-2 border border-[#DCD9D9]/50">
                                    <div class="text-[11px] font-semibold text-[#1C1B1B] truncate" title="{{ $detail->service->name ?? 'Layanan' }}">
                                        {{ $detail->service->name ?? 'Layanan Studio' }}
                                    </div>
                                    @if($b->bookingDetails->count() > 1)
                                        <div class="text-[9px] text-[#B76E79] font-medium">+{{ $b->bookingDetails->count() - 1 }} layanan tambahan</div>
                                    @endif
                                    <div class="text-[10px] text-[#49454F] font-bold mt-0.5">{{ $b->formatted_total_price }}</div>
                                </div>

                                <!-- Assigned Nail Artist -->
                                <div class="flex items-center justify-between pt-1">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-[#B76E79]/20 text-[#B76E79] font-bold text-[10px] flex items-center justify-center shrink-0">
                                            {{ substr($b->artist->user->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div class="truncate">
                                            <div class="text-[11px] font-bold text-[#1C1B1B] truncate">{{ $b->artist->user->name ?? 'Teknisi' }}</div>
                                            <div class="text-[9px] text-[#49454F]/70 leading-none">Nail Artist</div>
                                        </div>
                                    </div>

                                    <!-- Status Pill -->
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 {{ $b->status_badge_class }}">
                                        {{ $b->status_label }}
                                    </span>
                                </div>

                                <!-- Action bar & Custom Design Pill (Hover or Always) -->
                                <div class="pt-2 border-t border-[#DCD9D9]/50 flex items-center justify-between text-[10px]">
                                    @if($b->custom_design_photo)
                                        <button type="button" onclick="previewDesignImage('{{ $b->custom_design_photo_url }}', 'Desain #{{ $b->booking_code }}', '{{ addslashes($b->notes ?? '') }}')"
                                                class="px-2 py-0.5 rounded-full bg-[#F4D7D4]/60 text-[#B76E79] font-bold hover:bg-[#B76E79] hover:text-white transition flex items-center gap-1">
                                            <x-icon name="palette" class="w-2.5 h-2.5" />
                                            <span>Desain</span>
                                        </button>
                                    @else
                                        <span class="text-[9px] text-[#49454F]/50 font-mono">#{{ $b->booking_code }}</span>
                                    @endif

                                    <a href="{{ route('admin.bookings.index', ['q' => $b->booking_code]) }}"
                                       class="text-[#B76E79] hover:text-[#1C1B1B] font-bold flex items-center gap-0.5">
                                        <span>Kelola</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <!-- Empty Day State -->
                            <div class="flex-1 rounded-2xl border-2 border-dashed border-[#DCD9D9]/70 flex flex-col items-center justify-center p-4 text-center text-[#49454F]/50 my-auto">
                                <x-icon name="calendar" class="w-6 h-6 mb-1 opacity-40" />
                                <span class="text-xs font-semibold">Tidak ada janji</span>
                                <span class="text-[10px] mt-0.5">Semua 4 station kosong</span>
                                <a href="{{ route('admin.walk-in.create') }}" class="mt-2 text-[10px] font-bold text-[#B76E79] hover:underline">
                                    + Tambah Walk-In
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- ========================================== -->
<!-- 4. BLOCK SLOT / ARTIST LEAVE MODAL        -->
<!-- ========================================== -->
<div id="blockSlotModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-[#DCD9D9] relative animate-in fade-in zoom-in-95 duration-200">
        <!-- Close Button -->
        <button type="button" onclick="closeBlockSlotModal()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-[#F6F3F2] text-[#49454F] hover:text-[#1C1B1B] flex items-center justify-center">
            ✕
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <x-icon name="calendar-x" class="w-5 h-5" />
            </div>
            <div>
                <h3 class="font-serif font-bold text-lg text-[#1C1B1B]">Blokir Slot / Cuti Teknisi</h3>
                <p class="text-xs text-[#49454F]">Tandai jadwal libur atau blokir station untuk perawatan/istirahat.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.appointments.blockSlot') }}" class="space-y-4">
            @csrf

            <!-- Artist Selection -->
            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1">Pilih Nail Artist *</label>
                <select name="artist_id" required class="w-full px-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs text-[#1C1B1B] focus:outline-none focus:border-[#B76E79]">
                    <option value="">-- Pilih Artist --</option>
                    @foreach($artists as $art)
                        <option value="{{ $art->id }}">{{ $art->user->name ?? 'Artist #'.$art->id }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Selection -->
            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1">Tanggal Blokir / Cuti *</label>
                <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs text-[#1C1B1B] focus:outline-none focus:border-[#B76E79]">
            </div>

            <!-- Leave Type -->
            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1">Tipe Pemblokiran</label>
                <select name="leave_type" class="w-full px-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs text-[#1C1B1B] focus:outline-none focus:border-[#B76E79]">
                    <option value="personal">Keperluan Pribadi / Izin</option>
                    <option value="annual">Cuti Tahunan</option>
                    <option value="sick">Sakit (Medical)</option>
                    <option value="other">Studio Maintenance / Lainnya</option>
                </select>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1">Keterangan / Alasan *</label>
                <textarea name="reason" rows="3" required placeholder="Contoh: Izin keperluan keluarga, perbaikan station..."
                          class="w-full px-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs text-[#1C1B1B] focus:outline-none focus:border-[#B76E79]"></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#DCD9D9]/60">
                <button type="button" onclick="closeBlockSlotModal()"
                        class="px-4 py-2 rounded-full border border-[#DCD9D9] text-xs font-bold text-[#49454F] hover:bg-[#F6F3F2] transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-xs font-bold shadow-soft transition">
                    Simpan Blokir Slot
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. DESIGN REFERENCE PREVIEW MODAL         -->
<!-- ========================================== -->
<div id="designPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl border border-[#DCD9D9] relative flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-[#DCD9D9]/80 mb-3">
            <div class="flex items-center gap-2">
                <x-icon name="palette" class="w-5 h-5 text-[#B76E79]" />
                <h3 id="designPreviewTitle" class="font-serif font-bold text-base text-[#1C1B1B]">Referensi Desain Pelanggan</h3>
            </div>
            <button type="button" onclick="closeDesignPreviewModal()" class="w-8 h-8 rounded-full bg-[#F6F3F2] text-[#49454F] hover:text-[#1C1B1B] flex items-center justify-center font-bold">
                ✕
            </button>
        </div>

        <div id="designPreviewNotesBox" class="mb-3 p-3 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 hidden">
            <div class="font-bold mb-0.5">Catatan Khusus Pelanggan:</div>
            <div id="designPreviewNotes" class="italic"></div>
        </div>

        <div class="flex-1 overflow-auto rounded-2xl bg-[#F6F3F2] border border-[#DCD9D9] flex items-center justify-center min-h-[300px] p-2">
            <img id="designPreviewImg" src="" alt="Desain Referensi" class="max-h-[60vh] max-w-full rounded-xl object-contain shadow-xs">
            <iframe id="designPreviewPdf" src="" class="w-full h-[60vh] rounded-xl hidden border-0"></iframe>
        </div>

        <div class="mt-4 flex items-center justify-end">
            <button type="button" onclick="closeDesignPreviewModal()"
                    class="px-5 py-2 rounded-full bg-[#1C1B1B] text-white text-xs font-bold hover:bg-[#333] transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Scripts -->
<script>
    function openBlockSlotModal() {
        document.getElementById('blockSlotModal').classList.remove('hidden');
    }

    function closeBlockSlotModal() {
        document.getElementById('blockSlotModal').classList.add('hidden');
    }

    function previewDesignImage(url, title, notes) {
        const modal = document.getElementById('designPreviewModal');
        const titleEl = document.getElementById('designPreviewTitle');
        const imgEl = document.getElementById('designPreviewImg');
        const pdfEl = document.getElementById('designPreviewPdf');
        const notesEl = document.getElementById('designPreviewNotes');
        const notesBox = document.getElementById('designPreviewNotesBox');

        if (!url) {
            alert('File referensi desain tidak ditemukan.');
            return;
        }

        titleEl.textContent = title || 'Referensi Desain Pelanggan';
        if (notes && notes.trim() !== '') {
            notesEl.textContent = notes;
            notesBox.classList.remove('hidden');
        } else {
            notesBox.classList.add('hidden');
        }

        if (url.startsWith('data:application/pdf')) {
            imgEl.classList.add('hidden');
            pdfEl.classList.remove('hidden');
            pdfEl.src = url;
        } else {
            pdfEl.classList.add('hidden');
            imgEl.classList.remove('hidden');
            imgEl.src = url;
        }

        modal.classList.remove('hidden');
    }

    function closeDesignPreviewModal() {
        const modal = document.getElementById('designPreviewModal');
        const imgEl = document.getElementById('designPreviewImg');
        const pdfEl = document.getElementById('designPreviewPdf');
        imgEl.src = '';
        pdfEl.src = '';
        modal.classList.add('hidden');
    }
</script>
@endsection

