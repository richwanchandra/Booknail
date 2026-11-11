@extends('layouts.admin')
@section('title', 'Manajemen Reservasi')
@section('pageTitle', 'Manajemen Reservasi')
@section('pageSubtitle', 'Periksa detail reservasi dan kelola status pembayaran DP customer.')

@section('content')
<!-- STATS CARDS -->
<div class="mb-8 grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#1C1B1B]">{{ number_format($counts['today'] ?? 0) }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Reservasi Hari Ini</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-[#F4D7D4]/40 flex items-center justify-center text-brand-700">
            <x-lucide-calendar class="w-5 h-5" />
        </div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#B76E79]">{{ number_format($counts['pending'] ?? 0) }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Menunggu Verifikasi DP</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700">
            <x-lucide-clock class="w-5 h-5" />
        </div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-emerald-700">Rp {{ number_format($counts['revenue'] ?? 0, 0, ',', '.') }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Total DP Masuk</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700">
            <x-lucide-wallet class="w-5 h-5" />
        </div>
    </div>
    <div class="bg-white rounded-3xl p-6 border border-[#DCD9D9]/80 shadow-soft flex items-center justify-between">
        <div>
            <div class="text-3xl font-serif font-bold text-[#1C1B1B]">{{ number_format($counts['total'] ?? 0) }}</div>
            <div class="text-xs text-[#49454F] mt-1 font-semibold">Layanan Aktif</div>
        </div>
        <div class="w-10 h-10 rounded-full bg-[#F4D7D4]/40 flex items-center justify-center text-brand-700">
            <x-lucide-sparkles class="w-5 h-5" />
        </div>
    </div>
</div>

<!-- ACTION HEADER & FILTER BAR -->
<div class="mb-6 space-y-4">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <!-- Source Tabs (Semua, Online, Walk-In) -->
        <div class="flex items-center gap-2 p-1 rounded-full bg-[#F6F3F2] border border-[#DCD9D9]/70 text-xs w-full sm:w-auto overflow-x-auto">
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('source', 'page'), [])) }}"
               class="px-4 py-2 rounded-full font-bold transition {{ empty($source) ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                Semua ({{ $counts['total'] }})
            </a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['source' => 'online'])) }}"
               class="px-4 py-2 rounded-full font-bold transition inline-flex items-center gap-1.5 {{ ($source ?? '') === 'online' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                <x-lucide-globe class="w-3.5 h-3.5" />
                <span>Online ({{ $counts['online'] }})</span>
            </a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['source' => 'walk_in'])) }}"
               class="px-4 py-2 rounded-full font-bold transition inline-flex items-center gap-1.5 {{ ($source ?? '') === 'walk_in' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]' }}">
                <x-lucide-building-2 class="w-3.5 h-3.5" />
                <span>Walk-In ({{ $counts['walk_in'] }})</span>
            </a>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Tombol Laporan & Ekspor -->
            <a href="{{ route('admin.reports.index') }}"
               class="px-5 py-3 rounded-full bg-white hover:bg-[#F6F3F2] border border-[#DCD9D9] text-[#1C1B1B] text-xs font-bold shadow-soft flex items-center justify-center gap-2 transition shrink-0">
                <x-icon name="file-text" class="w-4 h-4 text-[#B76E79]" />
                <span>Laporan & Ekspor</span>
            </a>

            <!-- Tombol Tambah Pesanan Walk-In -->
            <a href="{{ route('admin.walk-in.create') }}"
               class="px-6 py-3 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-xs font-bold shadow-soft flex items-center justify-center gap-2 transition shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>+ Tambah Pesanan Walk-In</span>
            </a>
        </div>
    </div>

    <!-- SEARCH & STATUS FILTERS -->
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="w-full flex flex-col md:flex-row items-center gap-3">
        @if(!empty($source))
            <input type="hidden" name="source" value="{{ $source }}">
        @endif
        <div class="relative flex-1 w-full">
            <input name="q" value="{{ $search ?? '' }}" placeholder="Cari kode booking, nama tamu, atau no. HP..."
                   class="w-full px-5 py-3 rounded-full bg-white border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79]">
        </div>
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto">
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('status', 'page'), [])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ empty($status) ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">Semua Status</a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ ($status ?? '') === 'pending' ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">⏳ Verifikasi DP</a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'confirmed'])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ ($status ?? '') === 'confirmed' ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">Dikonfirmasi</a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'in_progress'])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ ($status ?? '') === 'in_progress' ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">Sedang Berjalan</a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ ($status ?? '') === 'completed' ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">Selesai</a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['status' => 'cancelled'])) }}" class="px-4 py-2.5 rounded-full text-xs font-bold transition {{ ($status ?? '') === 'cancelled' ? 'bg-[#1C1B1B] text-white' : 'bg-white border border-[#DCD9D9] text-[#49454F]' }}">Dibatalkan</a>
        </div>
    </form>
</div>

<!-- BOOKINGS LIST CARDS -->
<div class="space-y-4">
    @forelse($bookings as $b)
        @php
            $pendingDp = $b->payments()->where('payment_type', 'dp')->where('status', \App\Models\Payment::STATUS_PENDING)->first();
            $verifiedDp = $b->payments()->where('payment_type', 'dp')->where('status', \App\Models\Payment::STATUS_VERIFIED)->first();
            $fullPayment = $b->payments()->where('payment_type', 'full')->where('status', \App\Models\Payment::STATUS_VERIFIED)->first();
            $svcs = $b->bookingDetails;
            $mainSvc = $svcs->first();
        @endphp
        <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 p-6 md:p-8 shadow-soft transition hover:border-[#B76E79]/50">
            <!-- Header Row -->
            <div class="flex flex-wrap items-center justify-between border-b border-[#DCD9D9]/40 pb-4 mb-5 gap-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="w-2 h-2 rounded-full {{ in_array($b->status, ['confirmed', 'in_progress', 'completed']) || $verifiedDp || $fullPayment ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#49454F]">{{ $b->status_label }}</span>

                    <!-- Source Badge (Walk-In vs Online) -->
                    @if($b->is_walk_in_order)
                        <span class="bg-purple-100 text-purple-800 text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <x-lucide-building-2 class="w-3 h-3 text-purple-700" />
                            <span>Walk-In</span>
                            @if($b->branch)
                                <span class="opacity-80 font-normal">· {{ $b->branch }}</span>
                            @endif
                        </span>
                    @else
                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                            <x-lucide-globe class="w-3 h-3 text-blue-600" />
                            <span>Web Online</span>
                        </span>
                    @endif

                    @if($pendingDp && !empty($pendingDp->proof_file))
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1">
                            <x-lucide-file-check class="w-3 h-3 text-amber-700" />
                            <span>Butuh Verifikasi DP</span>
                        </span>
                    @elseif($b->status === \App\Models\Booking::STATUS_PENDING && (!$pendingDp || empty($pendingDp->proof_file)))
                        <span class="bg-rose-100 text-rose-800 text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1">
                            <x-lucide-alert-triangle class="w-3 h-3 text-rose-600" />
                            <span>Belum Ada Bukti DP (Wajib Upload)</span>
                        </span>
                    @elseif($verifiedDp)
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-3 py-1 rounded-full">DP Terverifikasi</span>
                    @elseif($fullPayment)
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-3 py-1 rounded-full">Lunas (Kasir)</span>
                    @endif

                    <!-- Cascade Delay Badge -->
                    @if($b->has_delay)
                        <span class="bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                            <x-lucide-clock class="w-3 h-3 text-amber-700 shrink-0" />
                            <span>Mundur +{{ $b->delay_minutes }} mnt (Awal: {{ substr($b->original_start_time, 0, 5) }})</span>
                        </span>
                        @if($b->customer_delay_ack === 'confirmed_new_time')
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                <x-lucide-check-circle-2 class="w-3 h-3 text-emerald-600" />
                                <span>Tamu Konfirmasi Hadir</span>
                            </span>
                        @elseif($b->customer_delay_ack === 'reschedule_requested')
                            <span class="bg-rose-50 text-rose-700 border border-rose-200 text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                <x-lucide-alert-circle class="w-3 h-3 text-rose-600" />
                                <span>Tamu Minta Reschedule</span>
                            </span>
                        @endif
                    @endif

                    <!-- 2-Way Artisan Confirmation Badge -->
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full border {{ $b->artist_confirmation_badge_class }} flex items-center gap-1">
                        <x-lucide-user-check class="w-3 h-3 shrink-0" />
                        <span>{{ $b->artist_confirmation_label }}</span>
                    </span>
                    @if($b->artist_notes)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-200">
                            Kendala: {{ Str::limit($b->artist_notes, 25) }}
                        </span>
                    @endif
                </div>
                <div class="text-xs font-mono text-[#49454F] font-semibold">#{{ $b->booking_code }}</div>
            </div>

            <!-- Content Grid -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left: Customer & Jadwal -->
                <div class="space-y-3">
                    <div>
                        <div class="text-[11px] font-semibold text-[#49454F]">Pelanggan</div>
                        <div class="font-serif text-lg font-bold text-[#1C1B1B]">{{ $b->customer_display_name }}</div>
                        <div class="text-xs text-[#49454F]">{{ $b->customer_display_phone }}</div>
                        @if($b->cashier_notes)
                            <div class="mt-2 text-[11px] text-[#49454F] bg-[#F6F3F2] px-3 py-1.5 rounded-xl inline-flex items-center gap-1.5 font-mono">
                                <x-lucide-receipt class="w-3.5 h-3.5 text-gray-500" />
                                <span><strong>Kasir:</strong> {{ $b->cashier_notes }}</span>
                            </div>
                        @endif

                        @if($b->service_upgrade_notes)
                            <div class="mt-2 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 flex items-start gap-1.5">
                                <x-lucide-zap class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" />
                                <div>
                                    <div class="font-bold">Catatan Upgrade Layanan:</div>
                                    <div class="whitespace-pre-line text-[#49454F]">{{ $b->service_upgrade_notes }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="pt-2">
                        <div class="text-[11px] font-semibold text-[#49454F]">Jadwal</div>
                        <div class="font-serif text-base font-bold text-[#1C1B1B]">{{ $b->formatted_booking_date }}</div>
                        <div class="text-xs text-[#49454F] flex items-center gap-1.5 flex-wrap">
                            <span>Pukul {{ $b->time_range }} WIB</span>
                            @if($b->has_delay)
                                <span class="text-[11px] text-amber-700 font-mono">(Semula {{ substr($b->original_start_time, 0, 5) }} - {{ substr($b->original_end_time, 0, 5) }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right: Layanan & Pembayaran -->
                <div class="space-y-3">
                    <div>
                        <div class="text-[11px] font-semibold text-[#49454F]">Layanan</div>
                        <div class="font-serif text-lg font-bold text-[#1C1B1B]">
                            {{ $mainSvc?->service?->name ?? 'Nail Service' }}
                        </div>
                        <div class="text-xs text-[#49454F]">
                            Bersama {{ optional($b->artist->user)->name ?? 'Teknisi' }} · {{ $mainSvc?->formatted_duration ?? '45 mnt' }}
                        </div>
                    </div>
                    <div class="pt-2">
                        <div class="text-[11px] font-semibold text-[#49454F]">Pembayaran</div>
                        <div class="font-serif text-lg font-bold text-[#B76E79]">
                            DP: {{ $b->formatted_dp_amount }}
                        </div>
                        <div class="text-xs text-[#49454F]">Total: {{ $b->formatted_total_price }}</div>
                    </div>
                </div>
            </div>

            <!-- Bukti Transfer Banner -->
            @if($pendingDp && $pendingDp->proof_file)
                <div class="mt-5 p-4 rounded-2xl bg-[#F6F3F2] border border-emerald-200/70 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-2 text-xs text-[#49454F] font-semibold truncate">
                        <x-lucide-paperclip class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="truncate">{{ $pendingDp->proof_display_name }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Bukti Tersedia</span>
                    </div>
                    <button type="button"
                       onclick="previewProofImage('{{ $pendingDp->proof_url }}', '#{{ $b->booking_code }} - {{ optional($b->customer)->name ?? 'Pelanggan' }}')"
                       class="shrink-0 px-4 py-1.5 rounded-full border border-emerald-300 bg-white text-xs font-semibold text-emerald-800 hover:bg-emerald-50 shadow-sm flex items-center gap-1.5 transition">
                        <x-lucide-external-link class="w-3.5 h-3.5" />
                        <span>Lihat Bukti Transfer ↗</span>
                    </button>
                </div>
            @elseif($b->status === \App\Models\Booking::STATUS_PENDING && (!$pendingDp || empty($pendingDp->proof_file)))
                <div class="mt-5 p-4 rounded-2xl bg-rose-50/90 border border-rose-200 text-xs text-rose-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-start gap-2.5">
                        <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="font-bold text-rose-900">Pelanggan Belum Mengunggah Bukti Pembayaran DP</div>
                            <div class="text-[11px] text-rose-800 mt-0.5 leading-relaxed">
                                Sesuai ketentuan, pelanggan wajib mengunggah bukti transfer. Tombol konfirmasi dinonaktifkan sampai pelanggan mengunggah bukti pembayaran.
                            </div>
                        </div>
                    </div>
                    <span class="shrink-0 px-3 py-1 rounded-full bg-rose-200/80 text-rose-900 text-[10px] font-bold">
                        Wajib Bukti DP
                    </span>
                </div>
            @elseif($verifiedDp && $verifiedDp->proof_file)
                <div class="mt-5 p-4 rounded-2xl bg-[#F6F3F2] flex items-center justify-between text-xs text-[#49454F]">
                    <div class="flex items-center gap-2 font-semibold">
                        <x-lucide-paperclip class="w-4 h-4 text-gray-400 shrink-0" />
                        <span>{{ $verifiedDp->proof_display_name }}</span>
                    </div>
                    <button type="button"
                       onclick="previewProofImage('{{ $verifiedDp->proof_url }}', '#{{ $b->booking_code }} - {{ optional($b->customer)->name ?? 'Pelanggan' }}')"
                       class="font-semibold underline text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                        <span>Bukti Transfer ↗</span>
                    </button>
                </div>
            @endif

            @if($b->notes)
                <div class="mt-3 text-xs text-[#49454F]">
                    <strong>Catatan:</strong> {{ $b->notes }}
                </div>
            @endif

            <!-- Referensi Desain Pelanggan (Base64) -->
            @if($b->custom_design_photo || $b->design_notes)
                <div class="mt-4 p-4 rounded-2xl bg-[#F4D7D4]/25 border border-[#B76E79]/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($b->custom_design_photo)
                            <div class="shrink-0 cursor-pointer group" onclick="previewDesignImage('{{ $b->custom_design_photo_url }}', '#{{ $b->booking_code }} - {{ addslashes($b->customer_display_name) }}', '{{ addslashes($b->design_notes ?? '') }}')">
                                <img src="{{ $b->custom_design_photo_url }}" alt="Desain Pelanggan" class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm ring-1 ring-[#B76E79]/40 group-hover:scale-105 transition">
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/60 text-[#B76E79] flex items-center justify-center shrink-0">
                                <x-lucide-camera class="w-6 h-6" />
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-[#B76E79]">Referensi Desain Pelanggan</span>
                                @if($b->custom_design_photo)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Foto Tersedia (Base64)</span>
                                @endif
                            </div>
                            <div class="text-xs text-[#49454F] mt-0.5 line-clamp-2">
                                <strong>Catatan:</strong> {{ $b->design_notes ?: 'Pelanggan melampirkan foto referensi desain kuku' }}
                            </div>
                        </div>
                    </div>
                    @if($b->custom_design_photo)
                        <button type="button"
                                onclick="previewDesignImage('{{ $b->custom_design_photo_url }}', '#{{ $b->booking_code }} - {{ addslashes($b->customer_display_name) }}', '{{ addslashes($b->design_notes ?? '') }}')"
                                class="shrink-0 px-4 py-2 rounded-full border border-[#B76E79]/40 bg-white text-xs font-semibold text-[#B76E79] hover:bg-[#F4D7D4]/40 shadow-sm flex items-center gap-1.5 transition">
                            <x-lucide-eye class="w-3.5 h-3.5" />
                            <span>Lihat Referensi Desain ↗</span>
                        </button>
                    @endif
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="mt-6 pt-4 border-t border-[#DCD9D9]/40 flex flex-wrap items-center justify-end gap-3">
                @if($b->status === \App\Models\Booking::STATUS_PENDING)
                    @php
                        $hasValidProof = $pendingDp && !empty($pendingDp->proof_file);
                    @endphp

                    @if($hasValidProof)
                        <form action="{{ route('admin.bookings.approve', $b) }}" method="POST">
                            @csrf
                            <button class="px-7 py-3 rounded-full bg-[#5E9C76] text-white text-sm font-semibold hover:bg-[#4d8362] transition shadow-soft inline-flex items-center gap-1.5">
                                <x-lucide-check class="w-4 h-4" />
                                <span>Setujui Pembayaran & Konfirmasi</span>
                            </button>
                        </form>
                    @else
                        <div class="inline-flex items-center" title="Pesanan tidak dapat dikonfirmasi booking karena pelanggan belum mengunggah bukti pembayaran DP">
                            <button type="button" disabled
                                    class="px-6 py-2.5 rounded-full bg-stone-200 text-stone-500 text-xs font-bold cursor-not-allowed inline-flex items-center gap-1.5 opacity-80 border border-stone-300">
                                <x-lucide-shield-alert class="w-4 h-4 text-stone-500" />
                                <span>Konfirmasi Dinonaktifkan (Wajib Bukti DP)</span>
                            </button>
                        </div>
                    @endif

                    @if($pendingDp)
                        <form action="{{ route('admin.bookings.reject', $b) }}" method="POST">
                            @csrf
                            <button class="px-7 py-3 rounded-full bg-[#F6F3F2] text-[#B76E79] border border-[#DCD9D9] text-sm font-semibold hover:bg-rose-50 transition inline-flex items-center gap-1.5"
                                    onclick="return confirm('Reject bukti DP booking ini?')">
                                <x-lucide-x class="w-4 h-4" />
                                <span>Tolak</span>
                            </button>
                        </form>
                    @endif
                @endif

                {{-- DYNAMIC SERVICE UPGRADE & CASCADE SHIFT BUTTON --}}
                @if(in_array($b->status, [\App\Models\Booking::STATUS_IN_PROGRESS, \App\Models\Booking::STATUS_CONFIRMED]))
                    <button type="button"
                            onclick="openUpgradeModal({{ $b->id }}, '{{ $b->booking_code }}', '{{ addslashes($b->customer_display_name) }}', '{{ substr($b->start_time, 0, 5) }}', '{{ substr($b->end_time, 0, 5) }}', '{{ optional($b->artist?->user)->name ?? 'Terapis' }}')"
                            class="px-5 py-2.5 rounded-full bg-amber-50 text-amber-900 border border-amber-300 text-sm font-semibold hover:bg-amber-100 transition inline-flex items-center gap-1.5 shadow-sm">
                        <x-lucide-zap class="w-4 h-4 text-amber-600" />
                        <span>Upgrade Layanan / +Durasi</span>
                    </button>
                @endif

                @if($b->status === \App\Models\Booking::STATUS_CONFIRMED)
                    <form action="{{ route('admin.bookings.start', $b) }}" method="POST">
                        @csrf
                        <button class="px-6 py-2.5 rounded-full bg-[#B76E79] text-white text-sm font-semibold hover:bg-[#a35460] transition inline-flex items-center gap-1.5">
                            <x-lucide-sparkles class="w-4 h-4" />
                            <span>Mulai Perawatan</span>
                        </button>
                    </form>
                @endif

                @if($b->status === \App\Models\Booking::STATUS_IN_PROGRESS)
                    <form action="{{ route('admin.bookings.complete', $b) }}" method="POST">
                        @csrf
                        <button class="px-6 py-2.5 rounded-full bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition inline-flex items-center gap-1.5">
                            <x-lucide-check class="w-4 h-4" />
                            <span>Perawatan Selesai</span>
                        </button>
                    </form>
                @endif

                @if(!in_array($b->status, [\App\Models\Booking::STATUS_CANCELLED, \App\Models\Booking::STATUS_COMPLETED], true))
                    <form action="{{ route('admin.bookings.cancel', $b) }}" method="POST">
                        @csrf
                        <button class="px-5 py-2.5 rounded-full border border-[#DCD9D9] text-[#49454F] text-xs font-semibold hover:bg-gray-100 transition"
                                onclick="return confirm('Batalkan reservasi ini?')">
                            Batalkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl p-16 text-center border border-[#DCD9D9]/80 shadow-soft">
            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-[#F4D7D4]/40 flex items-center justify-center text-[#B76E79] shadow-soft">
                <x-lucide-inbox class="w-6 h-6" />
            </div>
            <div class="font-serif text-xl font-bold text-[#1C1B1B]">Belum ada reservasi</div>
            <p class="text-xs text-[#49454F] mt-1">Tidak ada data reservasi yang sesuai dengan filter filter kamu.</p>
        </div>
    @endforelse
</div>

<div class="mt-8">
    {{ $bookings->withQueryString()->links() }}
</div>

<!-- UPGRADE & CASCADE SHIFT MODAL -->
<div id="upgradeModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 md:p-8 border border-[#DCD9D9] shadow-2xl relative my-8">
        <!-- Close Button -->
        <button type="button" onclick="closeUpgradeModal()" class="absolute top-6 right-6 w-9 h-9 rounded-full bg-[#FBF9F8] border border-[#DCD9D9] flex items-center justify-center text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#F4D7D4]/40 transition">
            <x-lucide-x class="w-4 h-4" />
        </button>

        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center shadow-sm shrink-0">
                <x-lucide-zap class="w-6 h-6" />
            </div>
            <div>
                <h3 class="font-serif text-xl font-bold text-[#1C1B1B]">Upgrade Layanan & Perpanjang Durasi</h3>
                <p class="text-xs text-[#49454F]">Penyesuaian jadwal real-time dengan notifikasi otomatis ke antrean terdampak.</p>
            </div>
        </div>

        <!-- Target Booking Info Box -->
        <div class="p-4 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] mb-6 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <div class="text-[#79747E] text-[11px] font-semibold">Kode Booking</div>
                <div id="modalBookingCode" class="font-mono font-bold text-[#B76E79]">BN...</div>
            </div>
            <div>
                <div class="text-[#79747E] text-[11px] font-semibold">Pelanggan</div>
                <div id="modalCustomerName" class="font-bold text-[#1C1B1B] truncate">-</div>
            </div>
            <div>
                <div class="text-[#79747E] text-[11px] font-semibold">Terapis</div>
                <div id="modalArtistName" class="font-bold text-[#1C1B1B] truncate">-</div>
            </div>
            <div>
                <div class="text-[#79747E] text-[11px] font-semibold">Jadwal Sesi Ini</div>
                <div class="font-bold text-[#1C1B1B]"><span id="modalStartTime">--:--</span> s/d <span id="modalEndTime" class="text-[#B76E79]">--:--</span></div>
            </div>
        </div>

        <form id="upgradeForm" method="POST" action="">
            @csrf
            <div class="space-y-5">
                <!-- Tambah Durasi Manual & Preset Buttons -->
                <div>
                    <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                        Tambahan Durasi Pengerjaan (Menit) <span class="text-[#B76E79]">*</span>
                    </label>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <input type="number" id="additionalMinutesInput" name="additional_minutes" value="30" min="5" max="240" step="5" required
                                   onchange="triggerPreview()" onkeyup="triggerPreview()"
                                   class="w-28 px-3 py-2.5 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm font-bold focus:outline-none focus:border-[#B76E79] text-center">
                            <span class="text-xs text-[#49454F] font-semibold">menit</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="setMinutes(15)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-[#DCD9D9] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition">+15m</button>
                            <button type="button" onclick="setMinutes(30)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-[#DCD9D9] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition">+30m</button>
                            <button type="button" onclick="setMinutes(45)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-[#DCD9D9] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition">+45m</button>
                            <button type="button" onclick="setMinutes(60)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-[#DCD9D9] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition">+60m</button>
                            <button type="button" onclick="setMinutes(90)" class="px-2.5 py-1 rounded-full text-xs font-semibold border border-[#DCD9D9] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition">+90m</button>
                        </div>
                    </div>
                </div>

                <!-- Pilihan Tambah Layanan Ekstra (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                        Pilih Layanan Tambahan di Lokasi (Opsional)
                    </label>
                    <div class="max-h-36 overflow-y-auto rounded-2xl border border-[#DCD9D9] bg-[#FBF9F8] p-2.5 space-y-1.5">
                        @foreach($services as $svc)
                            <label class="flex items-center justify-between gap-3 text-xs p-2 rounded-xl hover:bg-white cursor-pointer transition">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}"
                                           data-duration="{{ $svc->duration_minutes }}" data-price="{{ $svc->price }}"
                                           onchange="handleServiceCheck(this)"
                                           class="svc-check rounded border-[#DCD9D9] text-[#B76E79] focus:ring-[#B76E79]">
                                    <span class="font-medium text-[#1C1B1B]">{{ $svc->name }}</span>
                                </div>
                                <div class="text-[11px] text-[#49454F] font-mono">
                                    +{{ $svc->duration_minutes }}m · Rp {{ number_format($svc->price, 0, ',', '.') }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Tambahan Biaya & Catatan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                            Tambahan Biaya / Upgrade (Rp)
                        </label>
                        <input type="number" id="additionalPriceInput" name="additional_price" value="0" min="0" step="5000"
                               class="w-full px-4 py-2.5 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm font-bold focus:outline-none focus:border-[#B76E79]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                            Catatan Khusus Upgrade
                        </label>
                        <input type="text" name="upgrade_notes" placeholder="Contoh: Tambah nail art 3D glitter 10 kuku"
                               class="w-full px-4 py-2.5 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79]">
                    </div>
                </div>

                <!-- LIVE PREVIEW CASCADE SHIFT (DOMINO EFFECT) -->
                <div class="rounded-2xl border-2 border-[#B76E79]/40 bg-[#FBF9F8] p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-serif text-xs font-bold text-[#1C1B1B] flex items-center gap-1.5">
                            <x-lucide-sparkles class="w-4 h-4 text-[#B76E79]" />
                            <span>Simulasi Efek Domino ke Antrean Berikutnya:</span>
                        </div>
                        <span id="previewStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">Mengkalkulasi...</span>
                    </div>

                    <div id="previewShiftList" class="space-y-2 max-h-40 overflow-y-auto text-xs divide-y divide-[#DCD9D9]/60">
                        <div class="text-[#79747E] py-2 text-center">Memuat simulasi pergeseran jadwal...</div>
                    </div>

                    <div id="shiftWarningBox" class="hidden mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2">
                        <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                        <span id="shiftWarningText">Perhatian: Salah satu booking akan bergeser melewati jam operasional terapis!</span>
                    </div>
                </div>

                <!-- Option: Notify Customers via WhatsApp -->
                <label class="flex items-center gap-2.5 text-xs text-[#1C1B1B] cursor-pointer">
                    <input type="checkbox" name="notify_customers" value="1" checked class="rounded border-[#DCD9D9] text-[#B76E79] focus:ring-[#B76E79]">
                    <span class="font-semibold">Kirim notifikasi otomatis via WhatsApp (Flight Delay Alert) ke seluruh pelanggan terdampak</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-[#DCD9D9]/70">
                <button type="button" onclick="closeUpgradeModal()" class="px-5 py-2.5 rounded-full border border-[#DCD9D9] text-xs font-bold text-[#49454F] hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitUpgrade" class="px-6 py-3 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-xs font-bold shadow-soft transition-all flex items-center gap-2">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Terapkan Upgrade & Geser Jadwal Otomatis</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentTargetBookingId = null;
let previewTimeout = null;

function openUpgradeModal(id, code, customer, start, end, artist) {
    currentTargetBookingId = id;
    document.getElementById('modalBookingCode').textContent = '#' + code;
    document.getElementById('modalCustomerName').textContent = customer;
    document.getElementById('modalArtistName').textContent = artist;
    document.getElementById('modalStartTime').textContent = start;
    document.getElementById('modalEndTime').textContent = end;

    // Reset inputs
    document.getElementById('additionalMinutesInput').value = '30';
    document.getElementById('additionalPriceInput').value = '0';
    document.querySelectorAll('.svc-check').forEach(cb => cb.checked = false);

    const form = document.getElementById('upgradeForm');
    form.action = '/admin/bookings/' + id + '/apply-upgrade';

    document.getElementById('upgradeModal').classList.remove('hidden');
    triggerPreview();
}

function closeUpgradeModal() {
    document.getElementById('upgradeModal').classList.add('hidden');
}

function setMinutes(mins) {
    document.getElementById('additionalMinutesInput').value = mins;
    triggerPreview();
}

function handleServiceCheck(checkbox) {
    let totalMins = 0;
    let totalPrice = 0;
    document.querySelectorAll('.svc-check:checked').forEach(cb => {
        totalMins += parseInt(cb.dataset.duration || 0);
        totalPrice += parseInt(cb.dataset.price || 0);
    });

    if (totalMins > 0) {
        document.getElementById('additionalMinutesInput').value = totalMins;
    } else {
        document.getElementById('additionalMinutesInput').value = '30';
    }

    document.getElementById('additionalPriceInput').value = totalPrice;
    triggerPreview();
}

function triggerPreview() {
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(() => {
        fetchCascadePreview();
    }, 250);
}

function fetchCascadePreview() {
    if (!currentTargetBookingId) return;

    const mins = document.getElementById('additionalMinutesInput').value || 30;
    const badge = document.getElementById('previewStatusBadge');
    const container = document.getElementById('previewShiftList');
    const warningBox = document.getElementById('shiftWarningBox');

    badge.textContent = 'Mengkalkulasi...';
    badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700';

    fetch('/admin/bookings/' + currentTargetBookingId + '/preview-upgrade', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ additional_minutes: mins })
    })
    .then(res => res.json())
    .then(data => {
        if (data.total_affected === 0) {
            badge.textContent = 'Aman: Tidak ada jadwal terdampak';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
            container.innerHTML = '<div class="text-emerald-700 py-3 text-center text-xs">Sesi ini selesai pada ' + data.current_booking.new_end_time + ' WIB. Tidak ada booking setelahnya yang terdampak!</div>';
            warningBox.classList.add('hidden');
        } else {
            badge.textContent = data.total_affected + ' Booking Bergeser';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800';

            let html = '';
            data.shifted_bookings.forEach(item => {
                html += '<div class="py-2 flex items-center justify-between gap-3">';
                html += '<div>';
                html += '<span class="font-bold text-[#1C1B1B]">#' + item.booking_code + ' (' + item.customer_name + ')</span>';
                html += '<div class="text-[11px] text-[#49454F]">Semula ' + item.current_start_time + '-' + item.current_end_time + ' &rarr; <span class="font-bold text-[#B76E79]">Mundur ' + item.new_start_time + '-' + item.new_end_time + '</span></div>';
                html += '</div>';
                html += '<div class="text-right shrink-0">';
                html += '<span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-bold text-[10px]">+ ' + item.delay_minutes + ' mnt</span>';
                if (item.exceeds_artist_shift) {
                    html += '<div class="text-[9px] text-rose-600 font-bold mt-0.5">Lewati Shift</div>';
                }
                html += '</div>';
                html += '</div>';
            });
            container.innerHTML = html;

            if (data.has_shift_exceeded) {
                warningBox.classList.remove('hidden');
                document.getElementById('shiftWarningText').textContent = 'Peringatan: Jadwal terakhir bergeser melewati jam kerja terapis (' + data.artist_shift_end + ' WIB). Disarankan koordinasi dengan tamu untuk opsi reschedule!';
            } else {
                warningBox.classList.add('hidden');
            }
        }
    })
    .catch(err => {
        badge.textContent = 'Gagal kalkulasi';
        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-rose-800';
        container.innerHTML = '<div class="text-rose-600 py-2 text-center text-xs">Gagal memuat preview kalkulasi: ' + err.message + '</div>';
    });
}

function previewProofImage(url, title) {
    const modal = document.getElementById('proofPreviewModal');
    const titleEl = document.getElementById('proofPreviewTitle');
    const imgEl = document.getElementById('proofPreviewImg');
    const pdfEl = document.getElementById('proofPreviewPdf');

    if (!url) {
        alert('File bukti pembayaran tidak ditemukan.');
        return;
    }

    titleEl.textContent = title || 'Bukti Transfer';
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

function closeProofPreviewModal() {
    const modal = document.getElementById('proofPreviewModal');
    const imgEl = document.getElementById('proofPreviewImg');
    const pdfEl = document.getElementById('proofPreviewPdf');
    imgEl.src = '';
    pdfEl.src = '';
    modal.classList.add('hidden');
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

<!-- Modal Preview Bukti Transfer Admin -->
<div id="proofPreviewModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-gray-200 relative flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h4 class="font-serif font-bold text-lg text-gray-900" id="proofPreviewTitle">Bukti Transfer</h4>
                <p class="text-xs text-gray-500">Pratinjau bukti pembayaran DP (Base64 Database)</p>
            </div>
            <button type="button" onclick="closeProofPreviewModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                &times;
            </button>
        </div>
        <div class="flex-1 overflow-auto py-4 flex items-center justify-center bg-gray-50 rounded-2xl my-3">
            <img id="proofPreviewImg" src="" alt="Bukti Transfer" class="max-h-[65vh] w-auto max-w-full rounded-xl object-contain shadow-md" />
            <iframe id="proofPreviewPdf" src="" class="hidden w-full h-[65vh] rounded-xl border-0"></iframe>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeProofPreviewModal()" class="px-5 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
<!-- Modal Preview Referensi Desain Pelanggan -->
<div id="designPreviewModal" class="fixed inset-0 z-50 hidden bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-gray-200 relative flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h4 class="font-serif font-bold text-lg text-gray-900" id="designPreviewTitle">Referensi Desain</h4>
                <p class="text-xs text-gray-500">Pratinjau foto referensi desain dari pelanggan (Base64 Database)</p>
            </div>
            <button type="button" onclick="closeDesignPreviewModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                &times;
            </button>
        </div>
        <div class="flex-1 overflow-auto py-4 flex items-center justify-center bg-gray-50 rounded-2xl my-3">
            <img id="designPreviewImg" src="" alt="Desain Pelanggan" class="max-h-[60vh] w-auto max-w-full rounded-xl object-contain shadow-md" />
            <iframe id="designPreviewPdf" src="" class="hidden w-full h-[60vh] rounded-xl border-0"></iframe>
        </div>
        <div id="designPreviewNotesBox" class="p-3.5 rounded-2xl bg-[#F4D7D4]/25 border border-[#B76E79]/30 text-xs text-[#1C1B1B] mb-2 hidden">
            <strong class="text-[#B76E79]">Catatan Tambahan Pelanggan:</strong>
            <p id="designPreviewNotes" class="mt-1 text-[#49454F] leading-relaxed"></p>
        </div>
        <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeDesignPreviewModal()" class="px-5 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection
