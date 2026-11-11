<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-fcm-scripts />
</head>
<body class="bg-[#FAF7F5] text-[#1C1B1B] font-sans antialiased min-h-screen flex flex-col selection:bg-[#F4D7D4] selection:text-[#B76E79]"
      x-data="{ reviewModalOpen: false, reviewBookingCode: '', reviewArtistName: '', reviewActionUrl: '', selectedRating: 5, hoverRating: 0,
                openReview(code, artist, url) {
                    this.reviewBookingCode = code;
                    this.reviewArtistName = artist;
                    this.reviewActionUrl = url;
                    this.selectedRating = 5;
                    this.hoverRating = 0;
                    this.reviewModalOpen = true;
                },
                uploadModalOpen: false,
                uploadBookingCode: '',
                uploadDpAmount: '',
                uploadActionUrl: '',
                uploadCurrentProof: null,
                selectedProofName: '',
                openUploadProof(code, dpAmount, actionUrl, currentProof) {
                    this.uploadBookingCode = code;
                    this.uploadDpAmount = dpAmount;
                    this.uploadActionUrl = actionUrl;
                    this.uploadCurrentProof = currentProof;
                    this.selectedProofName = '';
                    this.uploadModalOpen = true;
                },
                proofPreviewModalOpen: false,
                proofPreviewUrl: '',
                proofPreviewTitle: '',
                openProofPreview(url, title) {
                    this.proofPreviewUrl = url;
                    this.proofPreviewTitle = title || 'Bukti Transfer DP';
                    this.proofPreviewModalOpen = true;
                }
              }">

{{-- NAVBAR --}}
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-[#DCD9D9]/80 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18 sm:h-20">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <x-application-logo class="h-9 w-9 transition-transform group-hover:rotate-6 duration-500"/>
                <span class="font-serif text-xl font-bold tracking-tight">
                    <span class="text-[#B76E79]">Book</span><span class="text-[#1C1B1B]">nail</span>
                </span>
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="{{ route('home') }}" class="text-[#49454F] hover:text-[#B76E79] transition">Beranda</a>
                <a href="{{ route('services.index') }}" class="text-[#49454F] hover:text-[#B76E79] transition">Layanan</a>
                <a href="{{ route('booking.index') }}" class="text-[#49454F] hover:text-[#B76E79] transition">Booking</a>
                <a href="{{ route('dashboard') }}" class="text-[#B76E79] font-bold border-b-2 border-[#B76E79] pb-0.5">Dashboard</a>
            </nav>
            <div class="flex items-center gap-3">
                <button type="button" id="fcm-status-badge" onclick="window.__requestFcmPermission && window.__requestFcmPermission()" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#FBF9F8] border border-[#DCD9D9]/80 text-xs font-semibold text-[#49454F] hover:text-[#B76E79] transition shadow-soft" title="Status Notifikasi Chrome">
                    <span class="w-2 h-2 rounded-full bg-stone-400"></span>
                    <span>Notifikasi</span>
                </button>
                <div class="hidden sm:flex items-center gap-3 px-3.5 py-1.5 rounded-full bg-[#FBF9F8] border border-[#DCD9D9]/80">
                    <div class="w-8 h-8 rounded-full bg-[#B76E79] text-white flex items-center justify-center text-xs font-bold shadow-soft">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="pr-2">
                        <div class="text-xs font-bold text-[#1C1B1B] leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-[#B76E79] font-semibold uppercase tracking-wider">{{ Auth::user()->hasRole('admin') ? 'Administrator' : 'Customer' }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="rounded-full border border-[#DCD9D9] bg-white text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#FBF9F8] py-2 px-3.5 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <x-lucide-log-out class="w-3.5 h-3.5 text-[#B76E79]" /> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<main class="flex-1 py-8 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- SESSION FLASH ALERTS --}}
        @if(session('success'))
            <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-soft">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <x-lucide-check-circle-2 class="w-5 h-5" />
                </div>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-soft">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <x-lucide-alert-circle class="w-5 h-5" />
                </div>
                <div class="font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-soft">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <x-lucide-alert-circle class="w-5 h-5 text-rose-600 shrink-0" />
                    <span>Perhatian:</span>
                </div>
                @foreach($errors->all() as $err)
                    <div class="text-xs text-rose-700 pl-7">• {{ $err }}</div>
                @endforeach
            </div>
        @endif

        {{-- PAGE HEADER BANNER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2.5 h-2.5 rounded-full {{ Auth::user()->hasRole('admin') ? 'bg-purple-500' : 'bg-emerald-500' }} animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#B76E79]">
                        {{ Auth::user()->hasRole('admin') ? 'Kasir & Studio Control' : 'Beauty Member Portal' }}
                    </span>
                </div>
                <h1 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-[#1C1B1B] flex items-center gap-2.5 flex-wrap">
                    <span>Halo, {{ explode(' ', Auth::user()->name)[0] }}</span>
                    <x-lucide-sparkles class="w-7 h-7 text-[#B76E79] inline-block" />
                </h1>
                <p class="mt-1.5 text-xs sm:text-sm text-[#49454F] max-w-2xl leading-relaxed">
                    {{ Auth::user()->hasRole('admin') ? 'Kelola operasional reservasi studio, pantau omzet kasir, dan kunci slot tamu walk-in secara real-time.' : 'Kelola reservasi kuku cantikmu, kumpulkan poin loyalitas, dan beri ulasan perawatan favorit.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('admin.walk-in.create') }}" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-2.5 px-5 text-sm shadow-soft transition-all transform active:scale-95 flex items-center gap-2">
                        <x-lucide-store class="w-4 h-4" />
                        <span>+ Input Walk-In</span>
                    </a>
                    <span class="rounded-full bg-purple-50 border border-purple-200/80 text-purple-700 font-bold text-xs px-3.5 py-2 flex items-center gap-1.5">
                        <x-lucide-shield-check class="w-4 h-4 text-purple-600" />
                        <span>Admin Mode</span>
                    </span>
                @else
                    <div class="p-2 px-4 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9]/80 shadow-sm flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center shrink-0 shadow-sm">
                            <x-lucide-sparkles class="w-5 h-5" />
                        </div>
                        <div>
                            <div class="text-[10px] text-[#49454F] font-bold uppercase tracking-wider leading-none">Loyalty Points</div>
                            <div class="font-serif text-lg font-bold text-[#1C1B1B] leading-tight mt-0.5">{{ number_format(Auth::user()->loyalty_points ?? 0) }} pts</div>
                        </div>
                    </div>
                @endif
                <a href="{{ route('booking.index') }}" class="rounded-full border border-[#DCD9D9] bg-[#FBF9F8] text-[#1C1B1B] hover:border-[#B76E79] hover:bg-[#F4D7D4]/30 font-bold py-2.5 px-5 text-sm transition flex items-center gap-2 shadow-sm">
                    <x-lucide-plus class="w-4 h-4 text-[#B76E79]" />
                    <span>Booking Baru</span>
                </a>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- ADMIN DASHBOARD --}}
        {{-- ============================================ --}}
        @if(isset($todayBookings))
            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-5">
                {{-- 1. Booking Hari Ini --}}
                <div class="bg-white rounded-3xl p-5 md:p-6 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50">
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-blue-100/40 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-blue-600 flex items-center justify-center shadow-sm">
                            <x-lucide-calendar class="w-5 h-5" />
                        </div>
                        <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $todayBookings }}</div>
                        <div class="text-xs text-[#49454F] font-semibold mt-1">Booking Hari Ini</div>
                    </div>
                </div>

                {{-- 2. Menunggu DP --}}
                <div class="bg-white rounded-3xl p-5 md:p-6 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50">
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-amber-100/40 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-amber-600 flex items-center justify-center shadow-sm">
                            <x-lucide-clock class="w-5 h-5" />
                        </div>
                        <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $pendingBookings }}</div>
                        <div class="text-xs text-[#49454F] font-semibold mt-1">Menunggu DP</div>
                    </div>
                </div>

                {{-- 3. Terkonfirmasi --}}
                <div class="bg-white rounded-3xl p-5 md:p-6 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50">
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-emerald-100/40 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-emerald-600 flex items-center justify-center shadow-sm">
                            <x-lucide-check-circle-2 class="w-5 h-5" />
                        </div>
                        <div class="mt-4 font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $confirmedBookings }}</div>
                        <div class="text-xs text-[#49454F] font-semibold mt-1">Terkonfirmasi</div>
                    </div>
                </div>

                {{-- 4. Revenue Hari Ini --}}
                <div class="bg-gradient-to-br from-[#B76E79] to-[#96535D] text-white rounded-3xl p-5 md:p-6 shadow-soft relative overflow-hidden border border-[#B76E79]">
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/10 pointer-events-none"></div>
                    <div class="relative text-white">
                        <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur text-white flex items-center justify-center shadow-sm">
                            <x-lucide-wallet class="w-5 h-5" />
                        </div>
                        <div class="mt-4 font-serif text-xl sm:text-2xl font-bold text-white">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
                        <div class="text-xs text-white/85 font-semibold mt-1">Revenue Hari Ini</div>
                    </div>
                </div>

                {{-- 5. Revenue Bulan Ini --}}
                <div class="bg-white rounded-3xl p-5 md:p-6 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden col-span-2 lg:col-span-1 transition hover:border-[#B76E79]/50">
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-purple-100/40 pointer-events-none"></div>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-purple-600 flex items-center justify-center shadow-sm">
                            <x-lucide-bar-chart-3 class="w-5 h-5" />
                        </div>
                        <div class="mt-4 font-serif text-xl sm:text-2xl font-bold text-[#1C1B1B]">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</div>
                        <div class="text-xs text-[#49454F] font-semibold mt-1">Revenue Bulan Ini</div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                {{-- RECENT BOOKINGS TABLE --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft overflow-hidden">
                    <div class="px-6 md:px-8 py-5 border-b border-[#DCD9D9]/70 flex items-center justify-between flex-wrap gap-3 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center shadow-sm shrink-0">
                                <x-lucide-receipt class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-serif text-lg md:text-xl font-bold text-[#1C1B1B]">Booking Terbaru</h2>
                                <p class="text-xs text-[#49454F] mt-0.5">8 transaksi reservasi terakhir dari semua pelanggan</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-[#FBF9F8] border border-[#DCD9D9] text-xs font-bold text-[#49454F] px-3.5 py-1">
                            Total {{ $recentBookings->count() }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-[#FBF9F8] text-left text-xs font-bold text-[#49454F] uppercase tracking-wider border-b border-[#DCD9D9]/60">
                                    <th class="px-6 py-3.5">Kode</th>
                                    <th class="px-4 py-3.5">Customer</th>
                                    <th class="px-4 py-3.5">Artist</th>
                                    <th class="px-4 py-3.5">Tanggal</th>
                                    <th class="px-4 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#DCD9D9]/50">
                                @forelse($recentBookings as $b)
                                    <tr class="hover:bg-[#FBF9F8]/60 transition">
                                        <td class="px-6 py-4 font-mono font-bold text-[#B76E79] text-xs">
                                            <div>{{ $b->booking_code ?? 'BN000000000' }}</div>
                                            @if($b->is_walk_in_order)
                                                <span class="inline-block mt-1 text-[9px] font-sans font-bold bg-purple-50 text-purple-700 border border-purple-200 px-2 py-0.5 rounded-full">
                                                    Walk-In
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full {{ $b->is_walk_in_order ? 'bg-purple-100 text-purple-700' : 'bg-[#F4D7D4] text-[#B76E79]' }} text-[10px] font-bold flex items-center justify-center shrink-0 shadow-sm">
                                                    {{ strtoupper(substr($b->customer_display_name, 0, 1)) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-[#1C1B1B] truncate text-xs sm:text-sm">{{ $b->customer_display_name }}</div>
                                                    @if($b->customer_display_phone !== '-')
                                                        <div class="text-[10px] text-[#49454F]">{{ $b->customer_display_phone }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-xs sm:text-sm text-[#49454F] font-semibold">
                                            {{ optional(optional($b->artist)->user)->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-xs sm:text-sm text-[#1C1B1B] font-semibold">
                                                {{ optional($b->booking_date)->format('d M Y') ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-[#49454F] font-mono">{{ $b->time_range ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-bold border {{ $b->status_badge_class }}">
                                                {{ $b->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-serif font-bold text-[#1C1B1B] whitespace-nowrap">
                                            {{ $b->formatted_total_price }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center text-[#49454F]">
                                            <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] flex items-center justify-center text-[#B76E79] shadow-soft">
                                                <x-lucide-inbox class="w-6 h-6" />
                                            </div>
                                            <div class="font-serif text-lg font-bold text-[#1C1B1B]">Belum ada booking</div>
                                            <div class="text-xs text-[#49454F] mt-1">Transaksi booking baru akan muncul di sini</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ARTIST LIST --}}
                <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft overflow-hidden flex flex-col">
                    <div class="px-6 py-5 border-b border-[#DCD9D9]/70 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center shadow-sm shrink-0">
                                <x-lucide-users class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="font-serif text-lg font-bold text-[#1C1B1B]">Nail Artist</h2>
                                <p class="text-xs text-[#49454F] mt-0.5">Terapis aktif beserta rating</p>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-[#DCD9D9]/50 flex-1 max-h-[480px] overflow-y-auto">
                        @forelse($artists as $artist)
                            @php
                                $initial = strtoupper(substr($artist->user->name, 0, 1));
                                $rating = number_format($artist->reviews_avg_rating ?? 4.8, 1);
                            @endphp
                            <div class="px-6 py-4 flex items-center gap-3.5 hover:bg-[#FBF9F8]/60 transition">
                                <div class="w-11 h-11 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-[#B76E79] font-serif font-bold flex items-center justify-center shrink-0 shadow-sm">
                                    {{ $initial }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-serif font-bold text-sm text-[#1C1B1B] truncate">{{ $artist->user->name }}</div>
                                    <div class="text-[11px] text-[#49454F] mt-0.5">{{ $artist->specialty ?? 'All Nail Art' }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="flex items-center gap-1 justify-end">
                                        <x-lucide-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                        <span class="font-bold text-[#1C1B1B] text-sm">{{ $rating }}</span>
                                    </div>
                                    <div class="text-[10px] text-[#49454F] mt-0.5">{{ $artist->reviews_count ?? 0 }} ulasan</div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center text-[#49454F]">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] flex items-center justify-center text-[#B76E79] shadow-soft">
                                    <x-lucide-users class="w-6 h-6" />
                                </div>
                                <div class="font-serif text-lg font-bold text-[#1C1B1B]">Belum ada artis</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="px-6 py-4 border-t border-[#DCD9D9]/60 bg-[#FBF9F8]/60">
                        <a href="{{ route('booking.index') }}" class="w-full py-2.5 px-4 rounded-full border border-[#DCD9D9] bg-white text-[#1C1B1B] hover:bg-[#F4D7D4]/30 hover:border-[#B76E79] text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                            Lihat Semua Artist
                        </a>
                    </div>
                </div>
            </div>

        {{-- ============================================ --}}
        {{-- CUSTOMER DASHBOARD --}}
        {{-- ============================================ --}}
        @elseif(isset($myBookings))
            @php
                $activeDelayed = $myBookings->first(fn($item) => $item->has_delay && in_array($item->status, [\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_CONFIRMED, \App\Models\Booking::STATUS_IN_PROGRESS]));
            @endphp

            @if($activeDelayed)
                <div class="p-5 md:p-6 rounded-3xl bg-amber-50 border-2 border-amber-300/90 text-amber-900 shadow-soft relative overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-200/80 text-amber-800 flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <x-lucide-clock class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Pemberitahuan Penyesuaian Jadwal</span>
                                    <span class="text-[10px] font-bold bg-amber-200 text-amber-900 px-2.5 py-0.5 rounded-full">
                                        Mundur +{{ $activeDelayed->delay_minutes }} Menit
                                    </span>
                                    <span class="text-xs font-mono font-bold text-amber-900">#{{ $activeDelayed->booking_code }}</span>
                                </div>
                                <h3 class="font-serif text-lg font-bold text-[#1C1B1B] mt-1">
                                    Jadwal Perawatanmu Bersama {{ optional($activeDelayed->artist?->user)->name ?? 'Terapis' }} Hari Ini Disesuaikan
                                </h3>
                                <p class="text-xs text-[#49454F] mt-1 leading-relaxed">
                                    Jadwal semula: <span class="line-through font-mono">{{ substr($activeDelayed->original_start_time, 0, 5) }}</span> WIB &rarr;
                                    <strong>Jadwal Baru: <span class="font-mono text-[#B76E79]">{{ substr($activeDelayed->start_time, 0, 5) }} - {{ substr($activeDelayed->end_time, 0, 5) }}</span> WIB</strong>.
                                </p>
                                <p class="text-[11px] text-[#79747E] mt-1 italic">
                                    Alasan: {{ $activeDelayed->delay_reason ?? 'Pengerjaan detail perawatan tamu sebelumnya membutuhkan waktu ekstra.' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0">
                            @if($activeDelayed->customer_delay_ack === 'confirmed_new_time')
                                <span class="px-4 py-2.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center gap-1.5 border border-emerald-200">
                                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                                    <span>Jadwal Baru Dikonfirmasi</span>
                                </span>
                            @else
                                <form action="{{ route('booking.ackDelay', $activeDelayed) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" class="px-5 py-2.5 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold text-xs shadow-soft transition flex items-center gap-1.5">
                                        <x-lucide-check class="w-3.5 h-3.5" />
                                        <span>Saya Tetap Hadir</span>
                                    </button>
                                </form>
                                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin Booknail, saya ingin konsultasi reschedule untuk booking ' . $activeDelayed->booking_code) }}" target="_blank"
                                   class="px-4 py-2.5 rounded-full border border-[#DCD9D9] bg-white text-[#49454F] hover:text-[#1C1B1B] font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                                    <x-lucide-phone class="w-3.5 h-3.5 text-[#B76E79]" />
                                    <span>Reschedule</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- LOYALTY + QUICK STATS --}}
            <div class="grid lg:grid-cols-3 gap-6">
                {{-- VIP LOYALTY CARD --}}
                <div class="lg:col-span-1 rounded-3xl bg-gradient-to-br from-[#B76E79] to-[#96535D] p-6 text-white shadow-soft relative overflow-hidden border border-[#B76E79] flex flex-col justify-between">
                    <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-10 w-32 h-32 rounded-full bg-[#F4D7D4]/20 blur-xl pointer-events-none"></div>

                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-widest text-white/90 flex items-center gap-1.5">
                                    <x-lucide-gem class="w-4 h-4 text-white" />
                                    <span>VIP Member Card</span>
                                </div>
                                <div class="font-serif text-xl font-bold mt-1 text-white">{{ Auth::user()->name }}</div>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white shadow-sm">
                                <x-lucide-sparkles class="w-6 h-6" />
                            </div>
                        </div>

                        <div class="mt-8 pt-5 border-t border-white/20">
                            <div class="text-xs text-white/80 font-semibold">Poin Loyalty Kamu</div>
                            <div class="flex items-end justify-between mt-1.5">
                                <div class="font-serif text-4xl font-bold text-white">
                                    {{ number_format(Auth::user()->loyalty_points ?? 0) }}
                                    <span class="text-base text-white/80 font-semibold ml-1">pts</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-white/75 font-semibold">Setara Diskon</div>
                                    <div class="text-sm font-bold text-white">Rp {{ number_format((Auth::user()->loyalty_points ?? 0) * 100, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative mt-6 p-3 rounded-2xl bg-white/15 backdrop-blur border border-white/20 text-[11px] text-white/90 leading-relaxed flex items-start gap-2">
                        <x-lucide-lightbulb class="w-4 h-4 text-amber-200 shrink-0 mt-0.5" />
                        <span>1 poin = Rp 100 diskon. Kumpulkan poin di setiap kunjungan untuk diskon perawatan berikutnya!</span>
                    </div>
                </div>

                {{-- 4 STATS CARDS --}}
                <div class="grid grid-cols-2 gap-4 lg:gap-5 lg:col-span-2">
                    {{-- Booking Akan Datang --}}
                    <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                            <x-lucide-calendar class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $upcomingCount }}</div>
                            <div class="text-xs text-[#49454F] font-semibold mt-0.5">Booking Akan Datang</div>
                        </div>
                    </div>

                    {{-- Selesai Dikerjakan --}}
                    <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-emerald-600 flex items-center justify-center shrink-0 shadow-sm">
                            <x-lucide-check-circle-2 class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $completedCount }}</div>
                            <div class="text-xs text-[#49454F] font-semibold mt-0.5">Selesai Dikerjakan</div>
                        </div>
                    </div>

                    {{-- Total Belanja --}}
                    <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-[#B76E79] flex items-center justify-center shrink-0 shadow-sm">
                            <x-lucide-wallet class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="font-serif text-xl sm:text-2xl font-bold text-[#1C1B1B]">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                            <div class="text-xs text-[#49454F] font-semibold mt-0.5">Total Belanja</div>
                        </div>
                    </div>

                    {{-- Review Diberikan --}}
                    <div class="bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft relative overflow-hidden transition hover:border-[#B76E79]/50 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-amber-500 flex items-center justify-center shrink-0 shadow-sm">
                            <x-lucide-star class="w-6 h-6 fill-amber-500 text-amber-500" />
                        </div>
                        <div>
                            <div class="font-serif text-2xl sm:text-3xl font-bold text-[#1C1B1B]">{{ $reviewsCount }}</div>
                            <div class="text-xs text-[#49454F] font-semibold mt-0.5">Review Diberikan</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT BOOKING SAYA --}}
            <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-soft overflow-hidden">
                <div class="px-6 md:px-8 py-5 border-b border-[#DCD9D9]/70 flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center shadow-sm shrink-0">
                            <x-lucide-calendar-check class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="font-serif text-lg md:text-xl font-bold text-[#1C1B1B]">Riwayat Booking Saya</h2>
                            <p class="text-xs text-[#49454F] mt-0.5">Semua transaksi jadwal perawatan kuku kamu</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-full bg-[#FBF9F8] border border-[#DCD9D9] text-xs font-bold text-[#49454F] px-3.5 py-1">
                            {{ $myBookings->total() }} total
                        </span>
                        <a href="{{ route('booking.index') }}" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-2 px-4 text-xs shadow-soft transition flex items-center gap-1.5">
                            <x-lucide-plus class="w-3.5 h-3.5" />
                            <span>Booking Baru</span>
                        </a>
                    </div>
                </div>

                @if($myBookings->count())
                    <div class="divide-y divide-[#DCD9D9]/50">
                        @foreach($myBookings as $b)
                            @php
                                $dpPayment = $b->payments->where('payment_type', 'dp')->first();
                                $hasProof = $dpPayment && !empty($dpPayment->proof_file);
                            @endphp
                            <div class="px-6 py-5 hover:bg-[#FBF9F8]/50 transition">
                                <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-6">
                                    {{-- Kolom 1: Kode & Status --}}
                                    <div class="lg:w-44 shrink-0">
                                        <div class="font-mono text-xs font-bold text-[#B76E79]">{{ $b->booking_code ?? 'BN000000' }}</div>
                                        <span class="mt-2 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $b->status_badge_class }}">
                                            {{ $b->status_label }}
                                        </span>

                                        @if($b->has_delay)
                                            <div class="mt-1.5 flex flex-col gap-1">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                    <x-lucide-clock class="w-3 h-3 text-amber-700" />
                                                    <span>Mundur +{{ $b->delay_minutes }} mnt</span>
                                                </span>
                                                @if($b->customer_delay_ack === 'confirmed_new_time')
                                                    <span class="text-[9px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                                                        <x-lucide-check-circle-2 class="w-3 h-3 text-emerald-600" />
                                                        <span>Jadwal Dikonfirmasi</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Kolom 2: Artist & Layanan --}}
                                    <div class="flex-1 grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-[#B76E79] text-xs font-serif font-bold flex items-center justify-center shrink-0 shadow-sm">
                                                    {{ strtoupper(substr(optional(optional($b->artist)->user)->name ?? 'A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-serif font-bold text-[#1C1B1B] text-sm">Artist: {{ optional(optional($b->artist)->user)->name ?? 'Belum dipilih' }}</div>
                                                    <div class="text-xs text-[#49454F] flex items-center gap-2 mt-0.5 flex-wrap">
                                                        <span class="flex items-center gap-1">
                                                            <x-lucide-calendar class="w-3.5 h-3.5 text-[#B76E79]" />
                                                            <span>{{ optional($b->booking_date)->format('d M Y') ?? '-' }}</span>
                                                        </span>
                                                        <span>·</span>
                                                        <span class="flex items-center gap-1">
                                                            <x-lucide-clock class="w-3.5 h-3.5 text-[#B76E79]" />
                                                            <span class="font-mono font-bold text-[#1C1B1B]">{{ $b->time_range ?? '-' }}</span>
                                                        </span>
                                                        @if($b->has_delay)
                                                            <span class="text-[10px] text-amber-700 font-mono line-through">({{ substr($b->original_start_time, 0, 5) }} - {{ substr($b->original_end_time, 0, 5) }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            @if($b->details && $b->details->count())
                                                <div class="text-xs text-[#49454F] mb-1.5 font-semibold">Layanan:</div>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach($b->details->take(3) as $d)
                                                        <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-[#F6F3F2] border border-[#DCD9D9]/70 text-[#1C1B1B] font-semibold truncate max-w-[170px]">
                                                             {{ optional($d->service)->name ?? 'Layanan' }}
                                                        </span>
                                                    @endforeach
                                                    @if($b->details->count() > 3)
                                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F6F3F2] border border-[#DCD9D9]/70 text-[#49454F] font-semibold">+{{ $b->details->count() - 3 }} lagi</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-[#49454F] italic">Detail layanan belum tersedia</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Kolom 3: Harga & Tombol Aksi --}}
                                    <div class="lg:w-48 shrink-0 text-left lg:text-right flex lg:flex-col items-center lg:items-end justify-between gap-3">
                                        <div>
                                            <div class="text-[10px] text-[#49454F] font-bold uppercase tracking-wider">Total</div>
                                            <div class="font-serif text-xl font-bold text-[#1C1B1B]">{{ $b->formatted_total_price }}</div>
                                            @if(in_array($b->status, [\App\Models\Booking::STATUS_CONFIRMED, \App\Models\Booking::STATUS_IN_PROGRESS, \App\Models\Booking::STATUS_COMPLETED]))
                                                <div class="text-[10px] text-emerald-600 font-bold mt-0.5 flex items-center gap-1 justify-start lg:justify-end">
                                                    <x-lucide-check class="w-3 h-3 text-emerald-600" />
                                                    <span>DP Terverifikasi</span>
                                                </div>
                                            @elseif($b->status === \App\Models\Booking::STATUS_PENDING)
                                                @if($hasProof)
                                                    <div class="text-[10px] text-amber-700 font-bold mt-0.5 flex items-center gap-1 justify-start lg:justify-end">
                                                        <x-lucide-clock class="w-3 h-3 text-amber-600" />
                                                        <span>Menunggu Verifikasi DP</span>
                                                    </div>
                                                @else
                                                    <div class="text-[10px] text-rose-700 font-bold mt-0.5 flex items-center gap-1 justify-start lg:justify-end">
                                                        <x-lucide-alert-triangle class="w-3 h-3 text-rose-600" />
                                                        <span>Wajib Upload Bukti DP</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>

                                        <div class="flex flex-wrap gap-2 justify-end">
                                            @if($b->has_delay && $b->customer_delay_ack !== 'confirmed_new_time' && in_array($b->status, [\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_CONFIRMED, \App\Models\Booking::STATUS_IN_PROGRESS]))
                                                <form action="{{ route('booking.ackDelay', $b) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="action" value="confirmed">
                                                    <button type="submit" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-1.5 px-3 text-xs shadow-soft transition whitespace-nowrap">
                                                        Konfirmasi Jam
                                                    </button>
                                                </form>
                                            @endif

                                            @if($b->status === \App\Models\Booking::STATUS_PENDING)
                                                @if($hasProof)
                                                    <button type="button"
                                                            @click="openUploadProof('{{ $b->booking_code }}', '{{ $b->formatted_dp_amount }}', '{{ route('booking.uploadProof', $b) }}', '{{ $dpPayment->proof_url }}')"
                                                            class="rounded-full border border-amber-300 bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold py-1.5 px-3.5 text-xs shadow-soft transition whitespace-nowrap flex items-center gap-1.5">
                                                        <x-lucide-file-check class="w-3.5 h-3.5 text-amber-700" />
                                                        <span>Ganti Bukti DP</span>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                            @click="openUploadProof('{{ $b->booking_code }}', '{{ $b->formatted_dp_amount }}', '{{ route('booking.uploadProof', $b) }}', null)"
                                                            class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-1.5 px-4 text-xs shadow-soft transition whitespace-nowrap flex items-center gap-1.5 animate-pulse">
                                                        <x-lucide-upload-cloud class="w-3.5 h-3.5" />
                                                        <span>Upload Bukti DP *</span>
                                                    </button>
                                                @endif
                                            @elseif($b->status === \App\Models\Booking::STATUS_COMPLETED && !$b->review)
                                                <button type="button"
                                                        @click="openReview('{{ $b->booking_code }}', '{{ optional($b->artist->user)->name ?? 'Nail Artist' }}', '{{ route('reviews.store', $b) }}')"
                                                        class="rounded-full border border-[#B76E79] bg-[#F4D7D4]/40 text-[#B76E79] hover:bg-[#B76E79] hover:text-white font-bold py-1.5 px-3.5 text-xs transition whitespace-nowrap flex items-center gap-1.5 shadow-sm">
                                                    <x-lucide-star class="w-3.5 h-3.5" />
                                                    <span>Beri Review</span>
                                                </button>
                                            @elseif($b->review)
                                                <div class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 border border-amber-200 text-amber-700">
                                                    <x-lucide-star class="w-3.5 h-3.5 fill-amber-400 text-amber-400" />
                                                    <span>{{ $b->review->rating }}.0 · Direview</span>
                                                </div>
                                            @endif
                                            @if($b->custom_design_photo)
                                                <button type="button"
                                                        @click="openProofPreview('{{ $b->custom_design_photo_url }}', 'Desain Referensi #' + '{{ $b->booking_code }}')"
                                                        class="rounded-full border border-[#B76E79]/40 bg-[#F4D7D4]/30 hover:bg-[#F4D7D4]/60 text-[#B76E79] py-1.5 px-3 text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                                                    <x-lucide-image class="w-3.5 h-3.5 text-[#B76E79]" />
                                                    <span>Lihat Desain</span>
                                                </button>
                                            @endif
                                            <button class="rounded-full border border-[#DCD9D9] bg-white text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#FBF9F8] py-1.5 px-3 text-xs font-bold transition shadow-sm" onclick="alert('Detail booking akan tampil disini')">
                                                Detail
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Notice Wajib Upload Bukti DP --}}
                                @if($b->status === \App\Models\Booking::STATUS_PENDING)
                                    @if(!$hasProof)
                                        <div class="mt-4 p-3.5 rounded-2xl bg-rose-50/90 border border-rose-200 text-xs text-rose-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                                            <div class="flex items-start gap-2.5">
                                                <x-lucide-alert-circle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
                                                <div class="text-[11px] leading-relaxed">
                                                    <strong class="font-bold text-rose-900">Perhatian:</strong> Pelanggan wajib mengunggah bukti pembayaran DP ({{ $b->formatted_dp_amount }}). Jika tidak, pesanan <strong class="underline decoration-rose-400">tidak dapat dikonfirmasi booking</strong> oleh admin studio.
                                                </div>
                                            </div>
                                            <button type="button"
                                                    @click="openUploadProof('{{ $b->booking_code }}', '{{ $b->formatted_dp_amount }}', '{{ route('booking.uploadProof', $b) }}', null)"
                                                    class="shrink-0 px-4 py-1.5 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white text-[11px] font-bold shadow-soft transition flex items-center gap-1.5">
                                                <x-lucide-upload class="w-3.5 h-3.5" />
                                                <span>Upload Sekarang</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="mt-4 p-3 rounded-2xl bg-amber-50/80 border border-amber-200/80 text-xs text-amber-950 flex items-center justify-between gap-2 shadow-sm">
                                            <div class="flex items-center gap-2 text-[11px]">
                                                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 shrink-0" />
                                                <span>Bukti transfer telah diterima & sedang ditinjau admin. Menunggu konfirmasi status booking.</span>
                                            </div>
                                            <button type="button"
                                               @click="openProofPreview('{{ $dpPayment->proof_url }}', 'Bukti Transfer #{{ $b->booking_code }}')"
                                               class="text-[11px] font-bold text-[#B76E79] hover:underline shrink-0 flex items-center gap-1">
                                                <span>Lihat Bukti</span>
                                                <x-lucide-external-link class="w-3 h-3" />
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($myBookings->hasPages())
                        <div class="px-6 py-5 border-t border-[#DCD9D9]/60 bg-[#FBF9F8]/60">
                            {{ $myBookings->onEachSide(1)->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-20 text-center">
                        <div class="w-16 h-16 rounded-3xl bg-[#F4D7D4]/50 border border-[#DCD9D9] flex items-center justify-center mx-auto mb-4 text-[#B76E79] shadow-soft">
                            <x-lucide-sparkles class="w-8 h-8" />
                        </div>
                        <h3 class="font-serif text-2xl font-bold text-[#1C1B1B]">Belum ada booking</h3>
                        <p class="mt-2 text-sm text-[#49454F] max-w-md mx-auto leading-relaxed">Saatnya memanjakan kuku cantikmu dengan nail art premium di Nail Glam Studio.</p>
                        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                            <a href="{{ route('booking.index') }}" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-3 px-6 text-sm shadow-soft transition-all transform active:scale-95">
                                Booking Sekarang
                            </a>
                            <a href="{{ route('services.index') }}" class="rounded-full border border-[#DCD9D9] bg-[#FBF9F8] text-[#1C1B1B] hover:bg-[#F4D7D4]/30 font-bold py-3 px-6 text-sm transition shadow-sm">
                                Lihat Layanan
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>
</main>

{{-- FOOTER --}}
<footer class="bg-[#1C1B1B] text-white pt-12 pb-8 mt-12 border-t border-[#DCD9D9]/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3 items-center">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10"/>
                <span class="font-serif text-xl font-bold">
                    <span class="text-[#B76E79]">Book</span><span class="text-white">nail</span>
                </span>
            </div>
            <div class="text-center text-[#DCD9D9]/80 text-sm">
                Studio nail art premium dengan sistem reservasi terpadu & kasir otomatis.
            </div>
            <div class="text-center md:text-right text-xs text-[#DCD9D9]/60">
                © {{ date('Y') }} Nail Glam Studio. Made with <x-lucide-heart class="w-3.5 h-3.5 text-[#B76E79] inline-block fill-[#B76E79] align-middle" />
            </div>
        </div>
    </div>
</footer>

{{-- MODAL REVIEW & RATING PELANGGAN --}}
<div x-show="reviewModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="reviewModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-[#DCD9D9] relative">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#B76E79]">Customer Review</span>
                <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#1C1B1B] mt-0.5">Beri Rating Perawatan</h3>
            </div>
            <button type="button" @click="reviewModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <form :action="reviewActionUrl" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
            @csrf
            {{-- Info Booking & Artist --}}
            <div class="p-3.5 rounded-2xl bg-[#FAF7F5] border border-[#DCD9D9]/80 flex items-center justify-between text-xs">
                <div>
                    <div class="text-[10px] text-gray-500 font-semibold">Artisan Teknisi</div>
                    <div class="font-bold text-[#1C1B1B]" x-text="reviewArtistName"></div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] text-gray-500 font-semibold">Kode Reservasi</div>
                    <div class="font-mono font-bold text-[#B76E79]" x-text="reviewBookingCode"></div>
                </div>
            </div>

            {{-- Star Rating Picker --}}
            <div class="text-center py-2">
                <label class="block text-xs font-bold text-gray-700 mb-2">Berapa bintang untuk kerapian dan hasil kuku cantikmu?</label>
                <input type="hidden" name="rating" :value="selectedRating">
                <div class="flex items-center justify-center gap-2">
                    <template x-for="s in [1, 2, 3, 4, 5]" :key="s">
                        <button type="button"
                                @click="selectedRating = s"
                                @mouseenter="hoverRating = s"
                                @mouseleave="hoverRating = 0"
                                class="p-1 transition-transform transform hover:scale-125 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 transition-colors shrink-0" :class="(hoverRating ? hoverRating >= s : selectedRating >= s) ? 'fill-amber-400 text-amber-400' : 'text-gray-200 fill-none'">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </button>
                    </template>
                </div>
                <div class="text-xs font-bold text-amber-600 mt-2" x-text="selectedRating + ' dari 5 Bintang'"></div>
            </div>

            {{-- Komentar --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Ulasan Pengalaman Perawatan</label>
                <textarea name="comment" rows="3" placeholder="Ceritakan bagaimana kerapian nail art, keramahan teknisi, dan kenyamanan studio..." class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] focus:ring-0 outline-none text-xs bg-gray-50/50 resize-none font-sans leading-relaxed"></textarea>
            </div>

            {{-- Foto Hasil Kuku --}}
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Hasil Kuku (Opsional, Max 2MB)</label>
                <input type="file" name="review_photo" accept="image/jpeg,image/png" class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-[#B76E79] file:text-white file:font-bold hover:file:bg-[#a25a65] cursor-pointer rounded-2xl border-2 border-gray-100 bg-white p-1">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" @click="reviewModalOpen = false" class="rounded-full px-5 py-2.5 text-xs font-bold text-[#49454F] hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-2.5 px-6 text-xs transition shadow-soft flex items-center gap-2">
                    <x-lucide-send class="w-3.5 h-3.5" />
                    <span>Kirim Ulasan</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PREVIEW BUKTI TRANSFER --}}
<div x-show="proofPreviewModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="proofPreviewModalOpen = false" class="bg-white rounded-3xl p-6 max-w-2xl w-full shadow-2xl border border-[#DCD9D9] relative my-8 flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-serif text-lg sm:text-xl font-bold text-[#1C1B1B]" x-text="proofPreviewTitle"></h3>
                <span class="text-[10px] text-gray-500">Pratinjau bukti transfer yang diunggah (Base64)</span>
            </div>
            <button type="button" @click="proofPreviewModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
        <div class="flex-1 overflow-auto py-4 flex items-center justify-center bg-[#FAF7F5] rounded-2xl my-3">
            <template x-if="proofPreviewUrl && proofPreviewUrl.startsWith('data:application/pdf')">
                <iframe :src="proofPreviewUrl" class="w-full h-[65vh] rounded-xl border-0"></iframe>
            </template>
            <template x-if="proofPreviewUrl && !proofPreviewUrl.startsWith('data:application/pdf')">
                <img :src="proofPreviewUrl" alt="Bukti Transfer" class="max-h-[65vh] w-auto max-w-full rounded-xl object-contain shadow-md" />
            </template>
        </div>
        <div class="flex justify-end pt-2">
            <button type="button" @click="proofPreviewModalOpen = false" class="rounded-full px-5 py-2 bg-gray-100 hover:bg-gray-200 text-xs font-bold text-[#49454F] transition">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD BUKTI PEMBAYARAN DP PELANGGAN --}}
<div x-show="uploadModalOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div @click.away="uploadModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-[#DCD9D9] relative my-8">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center shadow-sm shrink-0">
                    <x-lucide-wallet class="w-5 h-5" />
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#B76E79]">Pembayaran DP Reservasi</span>
                    <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#1C1B1B]">Upload Bukti Transfer</h3>
                </div>
            </div>
            <button type="button" @click="uploadModalOpen = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Warning Box: Wajib Upload --}}
        <div class="mt-4 p-3.5 rounded-2xl bg-amber-50 border border-amber-200/90 text-xs text-amber-900 flex items-start gap-2.5">
            <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
            <div class="text-[11px] leading-relaxed">
                <strong>Kebijakan Studio:</strong> Pelanggan <strong>wajib</strong> mengunggah bukti pembayaran DP. Jika belum diunggah, maka <strong>pesanan tidak dapat dikonfirmasi booking</strong> oleh admin.
            </div>
        </div>

        {{-- Info Transfer Bank --}}
        <div class="mt-4 p-4 rounded-2xl bg-[#FAF7F5] border border-[#DCD9D9]/80 space-y-2.5 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-[#49454F]">Kode Reservasi:</span>
                <span class="font-mono font-bold text-[#B76E79]" x-text="'#' + uploadBookingCode"></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[#49454F]">Nominal DP 30%:</span>
                <span class="font-serif text-base font-bold text-[#B76E79]" x-text="uploadDpAmount"></span>
            </div>
            <div class="pt-2 border-t border-[#DCD9D9]/60 flex items-center justify-between">
                <div>
                    <div class="text-[10px] uppercase font-bold text-[#49454F]">Tujuan Transfer:</div>
                    <div class="font-bold text-[#1C1B1B] text-sm">BCA <span class="font-mono">1234567890</span></div>
                    <div class="text-[11px] text-[#49454F]">a.n. Nail Glam Studio</div>
                </div>
                <button type="button" onclick="navigator.clipboard.writeText('1234567890'); alert('Nomor rekening BCA berhasil disalin!');"
                        class="px-3 py-1.5 rounded-full border border-[#DCD9D9] bg-white hover:bg-gray-50 text-[11px] font-bold text-[#1C1B1B] transition shadow-sm flex items-center gap-1">
                    <x-lucide-copy class="w-3 h-3 text-[#B76E79]" />
                    <span>Salin No. Rek</span>
                </button>
            </div>
        </div>

        {{-- Preview existing proof if any --}}
        <template x-if="uploadCurrentProof">
            <div class="mt-3 p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-center justify-between">
                <div class="flex items-center gap-2 truncate">
                    <x-lucide-file-check class="w-4 h-4 text-emerald-600 shrink-0" />
                    <span class="truncate">Sudah ada bukti terupload</span>
                </div>
                <button type="button" @click="openProofPreview(uploadCurrentProof, 'Bukti Transfer #' + uploadBookingCode)" class="font-bold text-[#B76E79] hover:underline shrink-0 text-[11px]">
                    Lihat File ↗
                </button>
            </div>
        </template>

        <form :action="uploadActionUrl" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-4 py-2.5 rounded-2xl bg-white border border-[#DCD9D9] text-xs font-medium focus:outline-none focus:border-[#B76E79]">
                    <option value="bank_transfer">Transfer Bank (BCA)</option>
                    <option value="qris">QRIS (GoPay / OVO / ShopeePay / BCA Mobile)</option>
                    <option value="ewallet">E-Wallet Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                    Pilih File Foto Bukti Pembayaran <span class="text-rose-600">*</span>
                </label>
                <div class="relative border-2 border-dashed border-[#DCD9D9] hover:border-[#B76E79] rounded-2xl p-6 text-center bg-[#FAF7F5] transition cursor-pointer">
                    <input type="file" name="dp_proof_file" accept="image/jpeg,image/png,image/webp,application/pdf" required
                           @change="selectedProofName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-[#F4D7D4]/60 flex items-center justify-center text-[#B76E79]">
                        <x-lucide-upload-cloud class="w-5 h-5" />
                    </div>
                    <template x-if="selectedProofName">
                        <div>
                            <div class="text-xs font-bold text-emerald-700 flex items-center justify-center gap-1.5">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                                <span x-text="selectedProofName"></span>
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">Klik untuk mengganti pilihan file</div>
                        </div>
                    </template>
                    <template x-if="!selectedProofName">
                        <div>
                            <div class="text-xs font-bold text-[#1C1B1B]">Klik atau seret foto bukti transfer di sini</div>
                            <div class="text-[10px] text-gray-500 mt-1">Format JPG, PNG, WEBP, atau PDF (Maks. 3MB)</div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" @click="uploadModalOpen = false" class="rounded-full px-5 py-2.5 text-xs font-bold text-[#49454F] hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold py-2.5 px-6 text-xs transition shadow-soft flex items-center gap-2">
                    <x-lucide-send class="w-3.5 h-3.5" />
                    <span>Upload & Kirim Bukti</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Customer real-time booking status poller
    let lastCustomerCheckTime = new Date().toISOString();
    const seenCustomerEvents = new Set();

    function pollCustomerBookingUpdates() {
        fetch('{{ route("customer.bookings.checkUpdates") }}?since=' + encodeURIComponent(lastCustomerCheckTime), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.server_time) {
                lastCustomerCheckTime = data.server_time;
            }
            if (data.has_update && data.notification) {
                const n = data.notification;
                const eventKey = n.booking_code + '_' + n.status;
                if (!seenCustomerEvents.has(eventKey)) {
                    seenCustomerEvents.add(eventKey);
                    
                    // Audio chime
                    if (typeof window.playChimeSound === 'function') {
                        window.playChimeSound();
                    }

                    // Native Chrome notification
                    if ('Notification' in window && Notification.permission === 'granted') {
                        try {
                            const nativeN = new Notification(n.title, {
                                body: n.body,
                                icon: '/favicon.ico',
                                badge: '/favicon.ico',
                                tag: eventKey,
                                requireInteraction: true,
                            });
                            nativeN.onclick = function() {
                                window.focus();
                                window.location.reload();
                                this.close();
                            };
                        } catch (e) {
                            console.debug('Native notification error:', e);
                        }
                    }

                    // On-screen popup modal
                    if (typeof window.showFcmBookingPopup === 'function') {
                        window.showFcmBookingPopup(n.title, n.body, n.url, n.booking_code);
                    }
                }
            }
        })
        .catch(err => console.debug('Customer check error:', err));
    }

    document.addEventListener('DOMContentLoaded', function() {
        setInterval(pollCustomerBookingUpdates, 4000);
    });
</script>

</body>
</html>
