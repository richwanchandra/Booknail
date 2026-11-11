<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Teknisi (Artisan) — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF7F5] text-[#1C1B1B] font-sans antialiased min-h-screen flex flex-col selection:bg-[#F4D7D4] selection:text-[#B76E79]"
      x-data="{ activeTab: 'today', rejectModalOpen: false, rejectActionUrl: '', rejectBookingCode: '',
                designModalOpen: false, designModalUrl: '', designModalTitle: '', designModalNotes: '',
                openDesignModal(url, title, notes) {
                    this.designModalUrl = url;
                    this.designModalTitle = title || 'Referensi Desain Tamu';
                    this.designModalNotes = notes || '';
                    this.designModalOpen = true;
                }
              }">

{{-- NAVBAR --}}
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-[#DCD9D9]/80 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18 sm:h-20">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <x-application-logo class="h-9 w-9 transition-transform group-hover:rotate-6 duration-500"/>
                    <span class="font-serif text-xl font-bold tracking-tight">
                        <span class="text-[#B76E79]">Book</span><span class="text-[#1C1B1B]">nail</span>
                    </span>
                </a>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#F4D7D4]/60 text-[#B76E79] border border-[#B76E79]/20">
                    <x-lucide-sparkles class="w-3.5 h-3.5" />
                    <span>Portal Teknisi Artisan</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                {{-- Duty Status Badge --}}
                <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full border {{ $artist->is_active ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-gray-100 border-gray-300 text-gray-600' }} text-xs font-bold">
                    <span class="w-2.5 h-2.5 rounded-full {{ $artist->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                    <span>{{ $artist->is_active ? 'Siap Melayani (On-Duty)' : 'Sedang Istirahat (Off)' }}</span>
                </div>

                {{-- Notification Center Bell Dropdown --}}
                <div class="relative" x-data="{ notifOpen: false }">
                    <button type="button" @click="notifOpen = !notifOpen"
                            class="relative p-2.5 rounded-full border border-[#DCD9D9] bg-white text-[#49454F] hover:text-[#B76E79] hover:bg-[#FBF9F8] transition shadow-sm focus:outline-none"
                            title="Pusat Notifikasi Terapis">
                        <x-lucide-bell class="w-4 h-4" />
                        @if(($pendingConfirmCount ?? 0) > 0 || ($recentNotifications ?? collect())->count() > 0)
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 text-white text-[9px] font-bold items-center justify-center">
                                    {{ min(9, ($pendingConfirmCount ?? 0) + ($recentNotifications ?? collect())->where('urgent', false)->count()) }}
                                </span>
                            </span>
                        @endif
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="notifOpen" @click.away="notifOpen = false" x-cloak
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-[#DCD9D9] z-50 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                        <div class="p-4 border-b border-[#DCD9D9]/70 bg-[#FBF9F8] flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-lucide-bell class="w-4 h-4 text-[#B76E79]" />
                                <span class="font-serif font-bold text-sm text-[#1C1B1B]">Notifikasi Terapis</span>
                            </div>
                            <button type="button" onclick="testArtistNotification()" class="text-[10px] font-bold text-[#B76E79] hover:underline flex items-center gap-1">
                                <span>Tes Suara 🔔</span>
                            </button>
                        </div>

                        <div class="max-h-80 overflow-y-auto divide-y divide-[#DCD9D9]/50 text-xs">
                            @forelse($recentNotifications ?? [] as $notif)
                                <a href="{{ $notif['url'] }}" @click="notifOpen = false"
                                   class="p-3.5 block hover:bg-[#F4D7D4]/15 transition {{ $notif['urgent'] ? 'bg-amber-50/40' : '' }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="font-bold text-[#1C1B1B] {{ $notif['urgent'] ? 'text-amber-900 flex items-center gap-1' : '' }}">
                                            @if($notif['urgent'])
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            @endif
                                            {{ $notif['title'] }}
                                        </span>
                                        <span class="text-[10px] text-[#49454F]/70 shrink-0">{{ $notif['time'] }}</span>
                                    </div>
                                    <p class="text-[11px] text-[#49454F] mt-1">{{ $notif['body'] }}</p>
                                    @if($notif['code'])
                                        <span class="inline-block mt-1 font-mono text-[9px] bg-stone-100 text-stone-700 px-1.5 py-0.5 rounded font-bold">
                                            #{{ $notif['code'] }}
                                        </span>
                                    @endif
                                </a>
                            @empty
                                <div class="p-6 text-center text-[#49454F]/60">
                                    <x-lucide-check-circle class="w-7 h-7 mx-auto mb-1 text-emerald-500 opacity-60" />
                                    <div class="font-semibold text-xs text-[#1C1B1B]">Semua Beres!</div>
                                    <div class="text-[10px] mt-0.5">Tidak ada notifikasi mendesak saat ini.</div>
                                </div>
                            @endforelse
                        </div>

                        <div class="p-2.5 border-t border-[#DCD9D9]/70 bg-[#FBF9F8] text-center">
                            <span class="text-[10px] text-[#49454F]/70">Pusat Peringatan & Notifikasi Penugasan Salon</span>
                        </div>
                    </div>
                </div>

                {{-- User Info Pill --}}
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-[#FBF9F8] border border-[#DCD9D9]/80">
                    <div class="w-8 h-8 rounded-full bg-[#B76E79] text-white flex items-center justify-center text-xs font-bold shadow-soft">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden md:block pr-1">
                        <div class="text-xs font-bold text-[#1C1B1B] leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-[#B76E79] font-semibold">{{ $artist->specialty ?? 'Nail Artist' }}</div>
                    </div>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="rounded-full border border-[#DCD9D9] bg-white text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#FBF9F8] py-2 px-3.5 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <x-lucide-log-out class="w-3.5 h-3.5 text-[#B76E79]" />
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<main class="flex-1 py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-soft">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <x-lucide-check-circle-2 class="w-5 h-5" />
                </div>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-center gap-3 shadow-soft">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                    <x-lucide-alert-triangle class="w-5 h-5" />
                </div>
                <div class="font-semibold">{{ session('warning') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-soft">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <x-lucide-alert-circle class="w-5 h-5" />
                </div>
                <div class="font-semibold">{{ $errors->first() }}</div>
            </div>
        @endif

        {{-- BANNER NOTIFIKASI JADWAL MENUNGGU KONFIRMASI --}}
        @if(($pendingConfirmCount ?? 0) > 0)
            <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-200 text-amber-800 flex items-center justify-center shrink-0 shadow-xs">
                        <x-lucide-bell-ring class="w-5 h-5 text-amber-800 animate-bounce" />
                    </div>
                    <div>
                        <div class="font-bold text-sm text-amber-950 flex items-center gap-2">
                            <span>Notifikasi Penugasan: {{ $pendingConfirmCount }} Janji Temu Menunggu Konfirmasi Anda</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[9px] font-extrabold uppercase">Mendesak</span>
                        </div>
                        <div class="text-xs text-amber-900/80 mt-0.5">
                            Silakan periksa detail pelanggan, jam, serta foto referensi desain di bawah, lalu klik <strong>"Konfirmasi Jadwal"</strong> untuk mengunci antrean.
                        </div>
                    </div>
                </div>
                <button type="button" @click="activeTab = 'today'" class="rounded-full bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 text-xs transition shrink-0 shadow-sm flex items-center gap-1.5">
                    <span>Lihat Antrean</span>
                    <span>&darr;</span>
                </button>
            </div>
        @endif

        {{-- BANNER JIKA STATUS OFF / ISTIRAHAT --}}
        @if(! $artist->is_active)
            <div class="p-5 rounded-3xl bg-amber-50/90 border-2 border-amber-200 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-200/70 text-amber-800 flex items-center justify-center shrink-0">
                        <x-icon name="power" class="w-5 h-5 text-amber-800" />
                    </div>
                    <div>
                        <div class="font-bold text-sm text-amber-950">Status Ketersediaan: Sedang Istirahat / OFF</div>
                        <div class="text-xs text-amber-800/90 mt-0.5">Nama Anda saat ini disembunyikan dari pemilihan jadwal reservasi pelanggan. Klik tombol di samping saat Anda siap melayani kembali.</div>
                    </div>
                </div>
                <form action="{{ route('artist.toggleDuty') }}" method="POST" class="m-0 shrink-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 text-xs transition flex items-center justify-center gap-2 shadow-sm">
                        <x-icon name="user-check" class="w-4 h-4" />
                        <span>Aktifkan Mode Bertugas (On-Duty)</span>
                    </button>
                </form>
            </div>
        @endif

        {{-- HERO SECTION --}}
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#DCD9D9]/80 shadow-soft flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F4D7D4]/40 border border-[#B76E79]/20 mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#B76E79]"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#B76E79]">Real-Time Artisan Workspace</span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B] flex items-center gap-2">
                    <span>Selamat Bekerja, {{ explode(' ', Auth::user()->name)[0] }}</span>
                    <x-lucide-sparkles class="w-6 h-6 text-[#B76E79]" />
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-[#49454F] max-w-xl leading-relaxed">
                    Sinkronisasi jadwal real-time untuk menghindari double-booking dan bentrok jadwal dengan konfirmasi dua arah langsung dari kursi kerjamu.
                </p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <form action="{{ route('artist.toggleDuty') }}" method="POST" class="m-0 w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto rounded-full font-bold py-2.5 px-5 text-xs transition flex items-center justify-center gap-2 shadow-soft {{ $artist->is_active ? 'bg-white border border-[#DCD9D9] text-[#49454F] hover:bg-gray-50' : 'bg-[#B76E79] text-white hover:bg-[#a25a65]' }}">
                        <x-icon name="power" class="w-4 h-4 {{ $artist->is_active ? 'text-amber-600' : 'text-white' }}" />
                        <span>{{ $artist->is_active ? 'Ubah ke Mode Istirahat (Off)' : 'Aktifkan Mode Bertugas (On-Duty)' }}</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- 4 STAT CARDS --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            {{-- 1. Janji Temu Hari Ini --}}
            <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
                    <x-lucide-calendar class="w-5 h-5" />
                </div>
                <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $todayBookings->count() }}</div>
                <div class="text-xs text-[#49454F] font-semibold mt-1">Antrean Hari Ini</div>
            </div>

            {{-- 2. Menunggu Konfirmasi Kesiapan --}}
            <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shadow-sm">
                    <x-lucide-clock class="w-5 h-5" />
                </div>
                <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $pendingConfirmCount }}</div>
                <div class="text-xs text-[#49454F] font-semibold mt-1">Perlu Konfirmasi 2-Arah</div>
            </div>

            {{-- 3. Sedang Dikerjakan --}}
            <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft">
                <div class="w-10 h-10 rounded-2xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shadow-sm">
                    <x-lucide-wand-2 class="w-5 h-5" />
                </div>
                <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $inProgressCount }}</div>
                <div class="text-xs text-[#49454F] font-semibold mt-1">Sedang Dikerjakan</div>
            </div>

            {{-- 4. Rating Rata-Rata Live --}}
            <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 text-amber-500 flex items-center justify-center shadow-sm">
                    <x-lucide-star class="w-5 h-5 fill-amber-400" />
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs text-[#49454F] font-semibold">/ 5.0 ({{ $totalReviews }} ulasan)</span>
                </div>
                <div class="text-xs text-[#49454F] font-semibold mt-1">Rating Kepuasan Tamu</div>
            </div>
        </div>

        {{-- TAB NAVIGATION --}}
        <div class="flex items-center gap-2 border-b border-[#DCD9D9]/80 pb-3">
            <button @click="activeTab = 'today'"
                    :class="activeTab === 'today' ? 'bg-[#B76E79] text-white shadow-soft' : 'bg-white text-[#49454F] hover:text-[#1C1B1B] border border-[#DCD9D9]'"
                    class="rounded-full px-5 py-2 text-xs font-bold transition flex items-center gap-2">
                <x-lucide-calendar-check class="w-4 h-4" />
                <span>Antrean Hari Ini ({{ $todayBookings->count() }})</span>
            </button>

            <button @click="activeTab = 'upcoming'"
                    :class="activeTab === 'upcoming' ? 'bg-[#B76E79] text-white shadow-soft' : 'bg-white text-[#49454F] hover:text-[#1C1B1B] border border-[#DCD9D9]'"
                    class="rounded-full px-5 py-2 text-xs font-bold transition flex items-center gap-2">
                <x-lucide-calendar-days class="w-4 h-4" />
                <span>Janji Temu Mendatang ({{ $upcomingBookings->count() }})</span>
            </button>

            <button @click="activeTab = 'reviews'"
                    :class="activeTab === 'reviews' ? 'bg-[#B76E79] text-white shadow-soft' : 'bg-white text-[#49454F] hover:text-[#1C1B1B] border border-[#DCD9D9]'"
                    class="rounded-full px-5 py-2 text-xs font-bold transition flex items-center gap-2">
                <x-lucide-star class="w-4 h-4" />
                <span>Rating & Ulasan Saya ({{ $totalReviews }})</span>
            </button>
        </div>

        {{-- TAB 1: ANTREAN HARI INI --}}
        <div x-show="activeTab === 'today'" class="space-y-4">
            @forelse($todayBookings as $booking)
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#DCD9D9]/80 shadow-soft transition hover:border-[#B76E79]/50" id="booking-card-{{ $booking->id }}">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
                        {{-- Kolom 1: Jam & Status --}}
                        <div class="lg:w-64 shrink-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-mono text-xs font-bold text-[#B76E79]">{{ $booking->booking_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $booking->artist_confirmation_badge_class }}">
                                    {{ $booking->artist_confirmation_label }}
                                </span>
                            </div>
                            <div class="font-serif text-xl sm:text-2xl font-bold text-[#1C1B1B] flex items-center gap-2">
                                <x-lucide-clock class="w-5 h-5 text-[#B76E79]" />
                                <span>{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB</span>
                            </div>
                            <div class="text-xs text-[#49454F] font-semibold mt-1">
                                Status: <span class="font-bold text-[#1C1B1B] uppercase">{{ $booking->status }}</span>
                            </div>
                        </div>

                        {{-- Kolom 2: Info Pelanggan & Layanan --}}
                        <div class="flex-1 space-y-2.5 border-y lg:border-y-0 lg:border-x border-[#DCD9D9]/60 py-3 lg:py-0 lg:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($booking->customer_display_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-[#1C1B1B]">{{ $booking->customer_display_name }}</div>
                                        <div class="text-xs text-[#49454F] flex items-center gap-1">
                                            <x-lucide-phone class="w-3 h-3 text-gray-400" />
                                            <span>{{ $booking->customer_display_phone }}</span>
                                            @if($booking->customer_display_phone && $booking->customer_display_phone !== '-')
                                                <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $booking->customer_display_phone)) }}" target="_blank" class="text-[10px] text-emerald-600 font-bold hover:underline ml-1">
                                                    (Chat WA)
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="text-xs font-mono font-bold text-[#1C1B1B]">{{ $booking->formatted_total_price }}</span>
                            </div>

                            {{-- Layanan --}}
                            <div>
                                <div class="text-[11px] text-[#49454F] font-semibold mb-1">Layanan yang Dipesan:</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($booking->bookingDetails as $detail)
                                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-[#FBF9F8] border border-[#DCD9D9] font-semibold text-[#1C1B1B]">
                                            {{ optional($detail->service)->name ?? 'Layanan' }} ({{ $detail->duration_minutes }}m)
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Foto Desain Referensi Pelanggan (Base64/Storage) --}}
                            @if($booking->custom_design_photo || $booking->design_notes)
                                <div class="p-3 rounded-2xl bg-[#F4D7D4]/25 border border-[#B76E79]/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                                    <div class="flex items-center gap-3">
                                        @if($booking->custom_design_photo)
                                            <div class="shrink-0 cursor-pointer group"
                                                 @click="openDesignModal('{{ $booking->custom_design_photo_url }}', '#{{ $booking->booking_code }} - {{ addslashes($booking->customer_display_name) }}', '{{ addslashes($booking->design_notes ?? '') }}')">
                                                <img src="{{ $booking->custom_design_photo_url }}" alt="Desain Pelanggan" class="w-12 h-12 rounded-xl object-cover border border-[#B76E79]/40 shadow-sm group-hover:scale-105 transition">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-xl bg-[#F4D7D4]/60 text-[#B76E79] flex items-center justify-center shrink-0">
                                                <x-lucide-camera class="w-5 h-5" />
                                            </div>
                                        @endif
                                        <div class="text-xs">
                                            <div class="font-bold text-[#B76E79] flex items-center gap-1.5">
                                                <span>Foto Referensi Desain Tamu</span>
                                                @if($booking->custom_design_photo)
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Base64</span>
                                                @endif
                                            </div>
                                            <p class="text-[#49454F] text-[11px] line-clamp-1 mt-0.5">{{ $booking->design_notes ?: 'Tamu melampirkan referensi desain kuku' }}</p>
                                        </div>
                                    </div>
                                    @if($booking->custom_design_photo)
                                        <button type="button"
                                                @click="openDesignModal('{{ $booking->custom_design_photo_url }}', '#{{ $booking->booking_code }} - {{ addslashes($booking->customer_display_name) }}', '{{ addslashes($booking->design_notes ?? '') }}')"
                                                class="shrink-0 px-3.5 py-1.5 rounded-full border border-[#B76E79]/40 bg-white text-xs font-semibold text-[#B76E79] hover:bg-[#F4D7D4]/40 shadow-sm flex items-center gap-1.5 transition">
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                            <span>Lihat Desain ↗</span>
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- Catatan Alasan Tolak / Bentrok Jika Ada --}}
                            @if($booking->artist_notes)
                                <div class="p-2.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-800">
                                    <span class="font-bold">Catatan Kendala Teknisi:</span> {{ $booking->artist_notes }}
                                </div>
                            @endif
                        </div>

                        {{-- Kolom 3: Aksi Konfirmasi 2-Arah & Eksekusi Sesi --}}
                        <div class="lg:w-60 shrink-0 flex flex-col items-stretch justify-center gap-2">
                            {{-- Step 1: Konfirmasi Kesiapan --}}
                            @if($booking->artist_confirmation_status === 'pending' && in_array($booking->status, [\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_CONFIRMED]))
                                <form action="{{ route('artist.bookings.confirm', $booking) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 text-xs transition shadow-soft flex items-center justify-center gap-1.5">
                                        <x-lucide-check-circle class="w-3.5 h-3.5" />
                                        <span>Terima & Kunci Jadwal</span>
                                    </button>
                                </form>

                                <button type="button"
                                        @click="rejectBookingCode = '{{ $booking->booking_code }}'; rejectActionUrl = '{{ route('artist.bookings.reject', $booking) }}'; rejectModalOpen = true;"
                                        class="w-full rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 font-bold py-2 px-4 text-xs transition flex items-center justify-center gap-1.5">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5" />
                                    <span>Laporkan Bentrok / Tolak</span>
                                </button>
                            @endif

                            {{-- Step 2: Mulai Perawatan --}}
                            @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED)
                                <form action="{{ route('artist.bookings.start', $booking) }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="w-full rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-2.5 px-4 text-xs transition shadow-soft flex items-center justify-center gap-1.5">
                                        <x-lucide-play class="w-3.5 h-3.5 fill-white" />
                                        <span>Mulai Perawatan</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Step 3: Selesaikan Perawatan --}}
                            @if($booking->status === \App\Models\Booking::STATUS_IN_PROGRESS)
                                <form action="{{ route('artist.bookings.complete', $booking) }}" method="POST" class="m-0" onsubmit="return confirm('Konfirmasi perawatan kuku selesai? Pelanggan akan mendapatkan poin dan akses ulasan.')">
                                    @csrf
                                    <button type="submit" class="w-full rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-4 text-xs transition shadow-soft flex items-center justify-center gap-1.5">
                                        <x-lucide-sparkles class="w-3.5 h-3.5" />
                                        <span>Selesai Perawatan</span>
                                    </button>
                                </form>
                            @endif

                            @if($booking->status === \App\Models\Booking::STATUS_COMPLETED)
                                <div class="text-center py-2 px-3 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-center gap-1.5">
                                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                                    <span>Perawatan Selesai</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-[#DCD9D9]/80 shadow-soft">
                    <div class="w-16 h-16 rounded-3xl bg-[#F4D7D4]/40 border border-[#DCD9D9] flex items-center justify-center mx-auto mb-4 text-[#B76E79]">
                        <x-lucide-calendar-check class="w-8 h-8" />
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#1C1B1B]">Tidak Ada Antrean Hari Ini</h3>
                    <p class="text-xs sm:text-sm text-[#49454F] mt-1 max-w-sm mx-auto">
                        Belum ada reservasi janji temu yang ditugaskan kepada Anda untuk hari ini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- TAB 2: JANJI TEMU MENDATANG --}}
        <div x-show="activeTab === 'upcoming'" class="space-y-4">
            @forelse($upcomingBookings as $booking)
                <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-bold text-[#B76E79]">{{ $booking->booking_code }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-[#FBF9F8] border border-[#DCD9D9]">
                                {{ $booking->booking_date->format('d M Y') }}
                            </span>
                            <span class="text-xs font-bold text-[#1C1B1B]">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB</span>
                        </div>
                        <div class="text-sm font-bold text-[#1C1B1B]">{{ $booking->customer_display_name }} ({{ $booking->customer_display_phone }})</div>
                        <div class="text-xs text-[#49454F] mt-1">
                            Layanan: {{ $booking->bookingDetails->map(fn($d) => optional($d->service)->name)->filter()->implode(', ') }}
                        </div>
                        @if($booking->custom_design_photo || $booking->design_notes)
                            <div class="mt-2 inline-flex items-center gap-2">
                                @if($booking->custom_design_photo)
                                    <button type="button"
                                            @click="openDesignModal('{{ $booking->custom_design_photo_url }}', '#{{ $booking->booking_code }} - {{ addslashes($booking->customer_display_name) }}', '{{ addslashes($booking->design_notes ?? '') }}')"
                                            class="text-[10px] font-bold text-[#B76E79] hover:underline inline-flex items-center gap-1 bg-[#F4D7D4]/40 px-2.5 py-1 rounded-full border border-[#B76E79]/20">
                                        <x-lucide-image class="w-3 h-3 text-[#B76E79]" />
                                        <span>Lihat Referensi Desain</span>
                                    </button>
                                @endif
                                @if($booking->design_notes)
                                    <span class="text-[10px] text-gray-500 italic max-w-xs truncate">"{{ $booking->design_notes }}"</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if($booking->artist_confirmation_status === 'pending')
                            <form action="{{ route('artist.bookings.confirm', $booking) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="rounded-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 text-xs transition shadow-soft flex items-center gap-1.5">
                                    <x-lucide-check-circle class="w-3.5 h-3.5" />
                                    <span>Konfirmasi Siap</span>
                                </button>
                            </form>
                        @else
                            <span class="text-xs px-3 py-1 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Terkonfirmasi
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-3xl p-12 text-center border border-[#DCD9D9]/80 shadow-soft">
                    <p class="text-xs sm:text-sm text-[#49454F]">Belum ada jadwal janji temu mendatang.</p>
                </div>
            @endforelse
        </div>

        {{-- TAB 3: RATING & ULASAN SAYA --}}
        <div x-show="activeTab === 'reviews'" class="space-y-6">
            {{-- Overview Rata-rata & Distribusi Bintang --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#DCD9D9]/80 shadow-soft grid md:grid-cols-3 gap-6 items-center">
                <div class="text-center md:border-r border-[#DCD9D9]/80 md:pr-6">
                    <div class="font-serif text-5xl font-extrabold text-[#1C1B1B]">{{ number_format($avgRating, 1) }}</div>
                    <div class="flex items-center justify-center gap-1 mt-2 text-amber-400">
                        @for($i = 1; $i <= 5; $i++)
                            <x-icon name="star" class="w-5 h-5 {{ $i <= round($avgRating) ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}" />
                        @endfor
                    </div>
                    <div class="text-xs text-[#49454F] font-semibold mt-2">Berdasarkan {{ $totalReviews }} ulasan dari pelanggan</div>
                </div>

                {{-- Bar Breakdown Bintang --}}
                <div class="md:col-span-2 space-y-2">
                    @foreach([5, 4, 3, 2, 1] as $star)
                        @php
                            $cnt = $starCounts[$star] ?? 0;
                            $pct = $totalReviews > 0 ? ($cnt / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3 text-xs">
                            <div class="w-12 font-bold text-[#1C1B1B] flex items-center gap-1">
                                <span>{{ $star }}</span>
                                <x-lucide-star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                            </div>
                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <div class="w-12 text-right text-gray-500 font-semibold">{{ $cnt }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Feed Ulasan Pelanggan --}}
            <div class="grid sm:grid-cols-2 gap-4">
                @forelse($reviews as $rev)
                    <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr(optional($rev->customer)->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-[#1C1B1B]">{{ optional($rev->customer)->name ?? 'Pelanggan Studio' }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $rev->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5 text-amber-400">
                                    @for($s = 1; $s <= 5; $s++)
                                        <x-icon name="star" class="w-3.5 h-3.5 {{ $s <= $rev->rating ? 'fill-amber-400 text-amber-400' : 'text-gray-300' }}" />
                                    @endfor
                                </div>
                            </div>

                            <p class="text-xs text-[#49454F] leading-relaxed italic">
                                "{{ $rev->comment ?: 'Pelanggan puas dengan hasil perawatan kuku.' }}"
                            </p>

                            @if($rev->photo_url)
                                <div class="mt-3">
                                    <a href="{{ $rev->photo_url }}" target="_blank">
                                        <img src="{{ $rev->photo_url }}" alt="Hasil Kuku Tamu" class="w-20 h-20 rounded-2xl object-cover border border-[#DCD9D9] hover:opacity-90 transition">
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-mono">
                            <span>Ref: {{ optional($rev->booking)->booking_code }}</span>
                            <span>Rating: {{ $rev->rating }}.0 / 5.0</span>
                        </div>
                    </div>
                @empty
                    <div class="sm:col-span-2 bg-white rounded-3xl p-12 text-center border border-[#DCD9D9]/80 shadow-soft">
                        <div class="w-14 h-14 rounded-2xl bg-[#F4D7D4]/40 border border-[#DCD9D9] flex items-center justify-center mx-auto mb-3 text-[#B76E79]">
                            <x-lucide-star class="w-7 h-7" />
                        </div>
                        <h4 class="font-serif text-lg font-bold text-[#1C1B1B]">Belum Ada Ulasan Masuk</h4>
                        <p class="text-xs text-[#49454F] mt-1">Selesaikan perawatan tamu dengan baik agar pelanggan memberikan bintang dan testimoni.</p>
                    </div>
                @endforelse
            </div>

            @if($reviews->hasPages())
                <div class="mt-4">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

{{-- MODAL LAPORKAN BENTROK / TOLAK JADWAL --}}
<div x-show="rejectModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="rejectModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#DCD9D9] relative">
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-4">
            <x-lucide-alert-triangle class="w-6 h-6" />
        </div>
        <h3 class="font-serif text-xl font-bold text-[#1C1B1B]">Laporkan Bentrok / Kendala</h3>
        <p class="text-xs text-[#49454F] mt-1 leading-relaxed">
            Booking <span class="font-mono font-bold text-red-600" x-text="rejectBookingCode"></span> akan ditandai berhalangan. Mohon jelaskan alasannya agar admin dapat mengalihkan pelanggan ke teknisi lain.
        </p>

        <form :action="rejectActionUrl" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Kendala / Bentrok</label>
                <textarea name="reject_reason" required rows="3" placeholder="Contoh: Pengerjaan tamu sebelumnya molor 45 menit, atau ada keadaan darurat..." class="w-full px-3.5 py-2.5 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] focus:ring-0 outline-none text-xs bg-gray-50 resize-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" @click="rejectModalOpen = false" class="rounded-full px-5 py-2 text-xs font-bold text-[#49454F] hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 text-xs transition shadow-soft">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PREVIEW REFERENSI DESAIN PELANGGAN --}}
<div x-show="designModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="designModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl border border-[#DCD9D9] relative flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-serif text-lg sm:text-xl font-bold text-[#1C1B1B]" x-text="designModalTitle"></h3>
                <p class="text-xs text-gray-500">Pratinjau foto referensi desain kuku dari pelanggan (Base64 Database)</p>
            </div>
            <button type="button" @click="designModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <div class="flex-1 overflow-auto py-4 flex items-center justify-center bg-[#FAF7F5] rounded-2xl my-3">
            <template x-if="designModalUrl && designModalUrl.startsWith('data:application/pdf')">
                <iframe :src="designModalUrl" class="w-full h-[60vh] rounded-xl border-0"></iframe>
            </template>
            <template x-if="designModalUrl && !designModalUrl.startsWith('data:application/pdf')">
                <img :src="designModalUrl" alt="Referensi Desain Pelanggan" class="max-h-[60vh] w-auto max-w-full rounded-xl object-contain shadow-md" />
            </template>
        </div>

        <template x-if="designModalNotes">
            <div class="p-3.5 rounded-2xl bg-[#F4D7D4]/25 border border-[#B76E79]/30 text-xs text-[#1C1B1B] mb-2">
                <strong class="text-[#B76E79]">Catatan Tambahan Pelanggan:</strong>
                <p class="mt-1 text-[#49454F] leading-relaxed" x-text="designModalNotes"></p>
            </div>
        </template>

        <div class="flex justify-end pt-2">
            <button type="button" @click="designModalOpen = false" class="rounded-full px-5 py-2 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-[#49454F] transition">
                Tutup Pratinjau
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Initialize real-time notification polling for artist
        initArtistNotificationPolling();
    });

    // 1. Crystal Audio Chime via Web Audio API
    function playAudioChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const playTone = (freq, start, duration) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, ctx.currentTime + start);
                gain.gain.setValueAtTime(0, ctx.currentTime + start);
                gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + start + 0.04);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(ctx.currentTime + start);
                osc.stop(ctx.currentTime + start + duration);
            };
            playTone(659.25, 0.0, 0.40); // E5
            playTone(987.77, 0.15, 0.60); // B5
        } catch (e) {
            console.debug('Audio error:', e);
        }
    }

    // 2. Interactive In-App Notification Toast Modal
    function showArtistNotificationModal(title, body, url, bookingCode) {
        document.querySelectorAll('.artist-notif-modal').forEach(el => el.remove());

        const modal = document.createElement('div');
        modal.className = 'artist-notif-modal';
        modal.style.cssText = 'position:fixed;bottom:2rem;right:2rem;z-index:999999;max-width:440px;width:calc(100vw - 4rem);font-family:system-ui,sans-serif;animation:artistNotifSlideUp 0.35s ease-out;';
        
        const confirmUrl = url || '{{ route("artist.dashboard") }}';

        modal.innerHTML = `
            <div style="background:#fff;border-radius:1.5rem;border:2px solid #B76E79;box-shadow:0 20px 45px -10px rgba(183,110,121,0.35);overflow:hidden;">
                <div style="display:flex;align-items:center;gap:0.75rem;padding:1.25rem 1.25rem 0.75rem;border-bottom:1px solid #f3f0ef;background:#FAF8F8;">
                    <div style="width:42px;height:42px;border-radius:50%;background:#F4D7D4;display:flex;align-items:center;justify-content:center;font-size:1.35rem;box-shadow:0 2px 8px rgba(183,110,121,0.2);">🔔</div>
                    <div style="flex:1;">
                        <span style="display:inline-block;font-size:0.65rem;font-weight:800;letter-spacing:0.08em;color:#B76E79;background:#fff;padding:0.15rem 0.5rem;border-radius:9999px;border:1px solid rgba(183,110,121,0.3);">PENUGASAN BARU</span>
                        <h3 style="font-size:0.95rem;font-weight:700;color:#1C1B1B;margin:2px 0 0 0;">${title}</h3>
                    </div>
                    <button type="button" onclick="this.closest('.artist-notif-modal').remove()" style="background:none;border:none;font-size:1.5rem;color:#8C8889;cursor:pointer;padding:0.2rem;">&times;</button>
                </div>
                <div style="padding:1rem 1.25rem;font-size:0.875rem;color:#49454F;line-height:1.5;">
                    <p style="margin:0 0 0.75rem 0;">${body}</p>
                    ${bookingCode ? `<div style="display:inline-block;padding:0.35rem 0.75rem;background:#FBF9F8;border:1px dashed #DCD9D9;border-radius:0.5rem;font-size:0.8rem;color:#1C1B1B;">Kode Booking: <strong>${bookingCode}</strong></div>` : ''}
                </div>
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;padding:0.85rem 1.25rem 1.25rem;background:#FAF8F8;">
                    <button type="button" onclick="this.closest('.artist-notif-modal').remove()" style="background:transparent;border:1px solid #DCD9D9;color:#49454F;padding:0.55rem 1rem;border-radius:9999px;font-size:0.825rem;font-weight:600;cursor:pointer;">Tutup</button>
                    <a href="${confirmUrl}" style="background:#B76E79;color:#fff;text-decoration:none;padding:0.55rem 1.25rem;border-radius:9999px;font-size:0.825rem;font-weight:700;box-shadow:0 2px 8px rgba(183,110,121,0.35);">👉 Lihat & Konfirmasi Jadwal</a>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
    }

    // 3. Trigger Sound + Browser Notification + In-App Modal
    function triggerArtistPopup(title, body, url, bookingCode) {
        playAudioChime();
        showArtistNotificationModal(title, body, url, bookingCode);

        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                const n = new Notification(title, {
                    body: body,
                    icon: '/favicon.ico',
                    tag: bookingCode || 'artist-tag-' + Date.now(),
                    requireInteraction: true,
                });
                n.onclick = function() {
                    window.focus();
                    window.location.href = url || '{{ route("artist.dashboard") }}';
                    n.close();
                };
            } catch (e) {
                console.debug('Notification error:', e);
            }
        }
    }

    // 4. Test Notification Button Action
    function testArtistNotification() {
        if (!('Notification' in window)) {
            triggerArtistPopup(
                '🔔 Tes Suara Notifikasi Terapis!',
                'Customer Maya baru saja booking Custom 3D Nail Art (BK-TEST). Klik tombol di samping untuk mengunci jadwal.',
                '{{ route("artist.dashboard") }}',
                'BK-TEST'
            );
            return;
        }

        if (Notification.permission === 'granted') {
            triggerArtistPopup(
                '🔔 Tes Notifikasi Terapis!',
                'Customer Maya baru saja booking Custom 3D Nail Art (BK-TEST). Periksa detailnya di antrean Anda.',
                '{{ route("artist.dashboard") }}',
                'BK-TEST'
            );
        } else if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    triggerArtistPopup(
                        '🔔 Notifikasi Berhasil Diaktifkan!',
                        'Anda sekarang akan menerima notifikasi suara dan pop-up saat ada pesanan janji temu baru.',
                        '{{ route("artist.dashboard") }}',
                        'BK-ACTIVE'
                    );
                } else {
                    // Fallback to in-app notification
                    triggerArtistPopup(
                        '🔔 Tes Suara Notifikasi (In-App)',
                        'Izin desktop tidak diberikan, namun notifikasi suara dan pop-up in-app tetap aktif.',
                        '{{ route("artist.dashboard") }}',
                        'BK-INAPP'
                    );
                }
            });
        } else {
            // Already denied/blocked in browser settings - still play chime and in-app modal
            triggerArtistPopup(
                '🔔 Tes Suara Notifikasi Terapis',
                'Pemberitahuan in-app dan suara aktif. (Untuk pop-up desktop Chrome, ubah izin Notifications menjadi Allow pada setelan browser).',
                '{{ route("artist.dashboard") }}',
                'BK-INAPP'
            );
        }
    }

    // 5. Real-Time Auto-Polling for New Assigned Bookings
    let lastArtistServerTime = null;

    function initArtistNotificationPolling() {
        const checkUrl = '{{ route("artist.checkNew") }}';

        // Initial handshake to get current server time
        fetch(checkUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            lastArtistServerTime = data.server_time;
            // Poll every 15 seconds
            setInterval(pollArtistCheckNew, 15000);
        })
        .catch(err => console.debug('Handshake error:', err));
    }

    function pollArtistCheckNew() {
        if (!lastArtistServerTime) return;

        const checkUrl = '{{ route("artist.checkNew") }}?since=' + encodeURIComponent(lastArtistServerTime);

        fetch(checkUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.server_time) {
                lastArtistServerTime = data.server_time;
            }

            if (data.has_new && data.bookings && data.bookings.length > 0) {
                data.bookings.forEach(b => {
                    triggerArtistPopup(
                        '🔔 Janji Temu Baru Ditugaskan!',
                        `Tamu ${b.customer_name} telah memesan ${b.service_name} untuk ${b.date} (${b.time_range}).`,
                        b.confirm_url,
                        b.code
                    );
                });
            }
        })
        .catch(err => console.debug('Polling error:', err));
    }
</script>
</body>
</html>
