<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booknail — Studio Nail Art Premium & Booking Online</title>
    <meta name="description"
        content="Studio nail art premium dengan reservasi online 24/7. Manicure, Pedicure, Nail Art Custom, Extension, dan Treatment oleh nail artist tersertifikasi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-fcm-scripts />
</head>

<body class="bg-white text-gray-800 antialiased overflow-x-hidden">

    {{-- =============== NAVBAR =============== --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/70 backdrop-blur-xl border-b border-brand-100/50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <x-application-logo
                        class="h-10 w-10 sm:h-11 sm:w-11 transition-transform group-hover:rotate-6 duration-500" />
                    <span class="font-serif text-xl sm:text-2xl font-extrabold tracking-tight">
                        <span class="text-gradient">Book</span><span class="text-gray-900">nail</span>
                    </span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#home"
                        class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition">Beranda</a>
                    <a href="#services"
                        class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition">Layanan</a>
                    <a href="#gallery"
                        class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition">Galeri</a>
                    <a href="#artists"
                        class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition">Artis</a>
                    <a href="#testimonials"
                        class="text-sm font-semibold text-gray-700 hover:text-brand-600 transition">Ulasan</a>
                </div>

                <div class="hidden lg:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-ghost">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-secondary !py-2 !px-5 text-sm">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="btn-primary !py-2.5 !px-5 text-sm flex items-center gap-1.5">
                            Daftar
                            <x-icon name="arrow-right" class="w-4 h-4" />
                        </a>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <button id="mobile-menu-btn"
                    class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-gray-600 hover:bg-brand-50 hover:text-brand-600 transition">
                    <span id="icon-open"><x-icon name="menu" class="w-6 h-6" /></span>
                    <span id="icon-close" class="hidden"><x-icon name="x" class="w-6 h-6" /></span>
                </button>
            </div>
        </nav>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden lg:hidden border-t border-brand-100/60 bg-white/95 backdrop-blur-xl">
            <div class="px-4 py-4 space-y-1">
                <a href="#home"
                    class="mobile-link block px-4 py-3 rounded-xl text-gray-700 font-semibold hover:bg-brand-50 hover:text-brand-600">Beranda</a>
                <a href="#services"
                    class="mobile-link block px-4 py-3 rounded-xl text-gray-700 font-semibold hover:bg-brand-50 hover:text-brand-600">Layanan</a>
                <a href="#gallery"
                    class="mobile-link block px-4 py-3 rounded-xl text-gray-700 font-semibold hover:bg-brand-50 hover:text-brand-600">Galeri</a>
                <a href="#artists"
                    class="mobile-link block px-4 py-3 rounded-xl text-gray-700 font-semibold hover:bg-brand-50 hover:text-brand-600">Artis</a>
                <a href="#testimonials"
                    class="mobile-link block px-4 py-3 rounded-xl text-gray-700 font-semibold hover:bg-brand-50 hover:text-brand-600">Ulasan</a>
                <div class="pt-3 mt-3 border-t border-gray-100 flex flex-col gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-secondary w-full !py-3">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-ghost w-full border border-gray-200">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost w-full border border-gray-200">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-primary w-full">Daftar Sekarang</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        {{-- =============== HERO SECTION =============== --}}
        <section id="home" class="relative pt-28 sm:pt-36 pb-20 overflow-hidden">
            <div class="absolute inset-0 bg-dots opacity-40"></div>
            <div class="absolute -top-40 -left-32 w-96 h-96 bg-brand-200 rounded-full blur-3xl opacity-50"></div>
            <div
                class="absolute -bottom-40 -right-20 w-[500px] h-[500px] bg-amber-100 rounded-full blur-3xl opacity-60">
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                    {{-- Text --}}
                    <div class="text-center lg:text-left animate-fade-up">
                        <span class="eyebrow mb-5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                            Reservasi Online • 24/7
                        </span>
                        <h1 class="mt-5 section-heading !text-4xl sm:!text-5xl lg:!text-6xl !leading-[1.05]">
                            Kuku Cantik, <br />
                            <span class="text-gradient">Tanpa Antri.</span>
                        </h1>
                        <p class="mt-6 text-lg text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                            Studio nail art premium dengan nail artist tersertifikasi. Pilih layanan favoritmu,
                            tentukan jadwal, dan biarkan kami yang membuat kuku impianmu menjadi nyata <x-lucide-sparkles class="w-4 h-4 inline-block text-amber-400" />
                        </p>
                        <div class="mt-9 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="{{ route('booking.index') }}" class="btn-primary text-base">
                                Booking Sekarang
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="#services" class="btn-secondary text-base">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Lihat Layanan
                            </a>
                        </div>

                        {{-- Trust Row --}}
                        <div
                            class="mt-12 pt-8 border-t border-gray-100 grid grid-cols-3 gap-4 sm:gap-8 text-center lg:text-left max-w-lg mx-auto lg:mx-0">
                            <div>
                                <div class="text-2xl sm:text-3xl font-extrabold text-gradient">5K+</div>
                                <div class="mt-1 text-xs sm:text-sm text-gray-500">Pelanggan Puas</div>
                            </div>
                            <div class="border-x border-gray-100">
                                <div class="text-2xl sm:text-3xl font-extrabold text-gradient">15+</div>
                                <div class="mt-1 text-xs sm:text-sm text-gray-500">Layanan Premium</div>
                            </div>
                            <div>
                                <div class="text-2xl sm:text-3xl font-extrabold text-gradient flex items-center justify-center lg:justify-start gap-1">
                                    <span>4.9</span>
                                    <x-lucide-star class="w-6 h-6 text-amber-400 fill-amber-400 inline-block" />
                                </div>
                                <div class="mt-1 text-xs sm:text-sm text-gray-500">Rating Rata-rata</div>
                            </div>
                        </div>
                    </div>

                    {{-- Hero Visual --}}
                    <div class="relative">
                        {{-- Floating Cards --}}
                        <div class="relative w-full aspect-square max-w-lg mx-auto">
                            <div
                                class="absolute inset-0 bg-gradient-brand rounded-[3rem] rotate-3 opacity-90 shadow-2xl">
                            </div>
                            <div
                                class="absolute inset-2 bg-gradient-to-br from-brand-50 to-amber-50 rounded-[2.7rem] -rotate-2 overflow-hidden shadow-card">
                                <img src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=beautiful%20glamorous%20woman%20hands%20with%20elegant%20pink%20rose%20gold%20nail%20art%20manicure%20design%20in%20luxury%20beauty%20salon%20with%20soft%20lighting&image_size=square_hd"
                                    alt="Nail Art Premium" class="w-full h-full object-cover" loading="lazy">
                            </div>

                            {{-- Floating Badge 1 --}}
                            <div
                                class="absolute -left-4 sm:-left-8 top-10 sm:top-16 bg-white rounded-2xl p-3 sm:p-4 shadow-card animate-float-slow border border-brand-50">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <x-lucide-check-circle-2 class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Status</div>
                                        <div class="font-bold text-gray-900 text-sm">Slot Tersedia</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Floating Badge 2 --}}
                            <div
                                class="absolute -right-2 sm:-right-6 bottom-20 sm:bottom-28 bg-white rounded-2xl p-3 sm:p-4 shadow-card animate-float border border-brand-50">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600">
                                        <x-lucide-sparkles class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <div class="flex gap-0.5 text-amber-400">
                                            @for($i = 0; $i < 5; $i++)
                                                <x-lucide-star class="w-3.5 h-3.5 fill-amber-400" />
                                            @endfor
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">dari 3,200+ ulasan</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Floating Badge 3 --}}
                            <div class="absolute left-4 sm:left-8 -bottom-4 bg-white rounded-2xl p-3 sm:p-4 shadow-card border border-brand-50"
                                style="animation: float 7s ease-in-out infinite;">
                                <div class="flex items-center gap-3">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-brand border-2 border-white"></div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 border-2 border-white">
                                        </div>
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-rose-400 border-2 border-white">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-500">Hari ini</div>
                                        <div class="font-bold text-sm text-gray-900">128 Booking</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- =============== CATEGORY CHIPS =============== --}}
        <section class="py-10 sm:py-14 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-soft rounded-3xl p-6 sm:p-10 shadow-soft border border-white/60">
                    <div class="text-center mb-8">
                        <span class="eyebrow">Kategori Layanan</span>
                        <h2 class="mt-3 text-2xl sm:text-3xl font-bold text-gray-900">Pilih berdasarkan kebutuhanmu</h2>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                        @foreach($categories as $cat)
                            <a href="{{ route('services.index', ['category' => $cat->slug]) }}"
                                class="group bg-white rounded-2xl p-5 text-center hover:-translate-y-1 hover:shadow-card transition-all duration-300 border border-transparent hover:border-brand-200">
                                <div
                                    class="w-14 h-14 mx-auto rounded-2xl bg-brand-50 group-hover:bg-gradient-brand flex items-center justify-center text-brand-600 group-hover:text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-6">
                                    <x-icon name="{{ $cat->icon ?? 'sparkles' }}"
                                        class="w-7 h-7 transition-transform group-hover:scale-110" />
                                </div>
                                <div class="mt-3 font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    {{ $cat->name }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">{{ $cat->services_count }} Layanan</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- =============== FEATURED SERVICES =============== --}}
        <section id="services" class="py-20 sm:py-24 relative">
            <div class="absolute top-0 left-0 w-72 h-72 bg-brand-100 rounded-full blur-3xl opacity-40 -translate-x-1/2">
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Layanan Unggulan</span>
                    <h2 class="mt-3 section-heading">Perawatan untuk Kuku Cantikmu</h2>
                    <p class="section-subheading">Dari manicure klasik hingga nail art bridal mewah, semua tersedia
                        dengan kualitas premium dan harga transparan.</p>
                </div>

                <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach($featuredServices as $svc)
                        <article class="card group flex flex-col">
                            <a href="{{ route('services.show', $svc) }}"
                                class="relative block aspect-[4/3] overflow-hidden">
                                <img src="{{ $svc->image_url }}"
                                    alt="{{ $svc->name }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                                </div>
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="chip bg-white/90 backdrop-blur text-gray-900 !text-xs !font-bold shadow flex items-center gap-1.5">
                                        <x-icon name="{{ $svc->category->icon ?? 'sparkles' }}"
                                            class="w-3.5 h-3.5 text-brand-600" /> {{ $svc->category->name }}
                                    </span>
                                </div>
                                <div class="absolute bottom-4 right-4">
                                    <div class="price-tag bg-white/95 backdrop-blur px-3 py-1.5 rounded-full shadow">
                                        <span class="currency text-brand-600">Rp</span>
                                        <span
                                            class="amount text-gray-900">{{ number_format($svc->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="p-5 sm:p-6 flex flex-col flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <h3
                                        class="font-serif text-xl font-bold text-gray-900 group-hover:text-brand-600 transition">
                                        <a href="{{ route('services.show', $svc) }}">{{ $svc->name }}</a>
                                    </h3>
                                    <span
                                        class="inline-flex items-center gap-1 shrink-0 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                        <x-icon name="clock" class="w-3.5 h-3.5" />
                                        {{ $svc->formatted_duration }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $svc->description }}</p>
                                <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3 mt-auto">
                                    <a href="{{ route('booking.index', ['service' => $svc->id]) }}"
                                        class="btn-primary flex-1 !py-2.5 !text-sm justify-center">
                                        Booking
                                    </a>
                                    <a href="{{ route('services.show', $svc) }}"
                                        class="btn-ghost !py-2.5 border border-gray-200" title="Lihat Detail">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-14 text-center">
                    <a href="{{ route('services.index') }}"
                        class="btn-secondary text-base inline-flex items-center gap-2">
                        Lihat Semua {{ \App\Models\Service::active()->count() }} Layanan
                        <x-icon name="arrow-right" class="w-5 h-5" />
                    </a>
                </div>
            </div>
        </section>

        {{-- =============== HOW IT WORKS =============== --}}
        <section class="py-20 sm:py-24 bg-gradient-to-b from-white via-brand-50/40 to-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Cara Kerja</span>
                    <h2 class="mt-3 section-heading">Booking Cantik dalam 4 Langkah</h2>
                    <p class="section-subheading">Sistem booking kami dirancang agar kamu tidak lagi menghabiskan waktu
                        hanya untuk menunggu antrian.</p>
                </div>
                <div class="mt-16 grid sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 relative">
                    <div
                        class="hidden lg:block absolute top-14 left-[12%] right-[12%] h-0.5 bg-gradient-to-r from-brand-200 via-brand-400 to-brand-200">
                    </div>
                    @php($steps = [
                        ['no' => '1', 'icon' => 'target', 'title' => 'Pilih Layanan', 'desc' => 'Gunakan kategori atau cari layanan favoritmu dari 15+ pilihan perawatan.'],
                        ['no' => '2', 'icon' => 'calendar', 'title' => 'Tentukan Jadwal', 'desc' => 'Pilih tanggal, jam, dan nail artist kesayanganmu secara real-time.'],
                        ['no' => '3', 'icon' => 'credit-card', 'title' => 'Upload DP', 'desc' => 'Upload bukti transfer DP minimum untuk mengunci slot waktumu.'],
                        ['no' => '4', 'icon' => 'sparkles', 'title' => 'Datang & Cantik', 'desc' => 'Tiba di studio sesuai jadwal dan biarkan kami bekerja!'],
                    ])
                    @foreach($steps as $s)
                        <div class="relative">
                            <div
                                class="relative bg-white rounded-3xl p-7 shadow-soft border border-brand-50 h-full hover:shadow-card hover:-translate-y-1 transition-all duration-300">
                                <div
                                    class="absolute -top-4 left-6 w-9 h-9 rounded-full bg-gradient-brand text-white font-extrabold flex items-center justify-center shadow-lg">
                                    {{ $s['no'] }}
                                </div>
                                <div
                                    class="mt-3 w-16 h-16 rounded-2xl bg-gradient-brand/10 text-brand-600 flex items-center justify-center">
                                    <x-icon name="{{ $s['icon'] }}" class="w-8 h-8" />
                                </div>
                                <h3 class="mt-5 font-serif text-xl font-bold text-gray-900">{{ $s['title'] }}</h3>
                                <p class="mt-2 text-gray-600 text-sm leading-relaxed">{{ $s['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- =============== GALLERY =============== --}}
        <section id="gallery" class="py-20 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Galeri Portofolio</span>
                    <h2 class="mt-3 section-heading">Hasil Karya Nail Artist Kami</h2>
                    <p class="section-subheading">Ribuan desain indah telah kami wujudkan. Berikut sebagian portofolio
                        terbaru dari studio kami.</p>
                </div>

                <div class="mt-14 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
                    @if(isset($portfolios) && $portfolios->count() > 0)
                        @foreach($portfolios as $i => $pf)
                            <figure
                                class="group relative rounded-2xl overflow-hidden shadow-soft hover:shadow-card transition-all duration-500 aspect-square @if($i === 0) md:col-span-2 md:row-span-2 @endif">
                                <img src="{{ $pf->image_url }}"
                                    alt="{{ $pf->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy">
                                <figcaption
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4 sm:p-5">
                                    <div class="text-white">
                                        <div class="text-xs uppercase tracking-wider font-bold text-[#F4D7D4]">
                                            {{ $pf->category?->name ?? 'Portofolio' }}
                                        </div>
                                        <div class="mt-1 font-serif text-lg sm:text-xl font-bold leading-tight">{{ $pf->title }}</div>
                                        @if($pf->nailArtist?->user)
                                            <div class="text-xs text-white/80 mt-1">oleh {{ $pf->nailArtist->user->name }}</div>
                                        @endif
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach
                    @else
                        @php($galleryPrompts = [
                            'glamorous pink rose gold french tip nail art design with rhinestones',
                            'elegant bridal nail art with pearls and 3d flowers white ivory',
                            'kawaii anime character cute nail art pastel colors',
                            'marble stone nail art design nude beige colors minimalist',
                            'ombre gradient glitter pink to white nails luxury salon',
                            'chrome silver metallic mirror effect nails trendy',
                            'dark red burgundy nail art with gold foil details elegant',
                            'aesthetic pastel blue butterfly nail art design cute',
                        ])
                        @foreach($galleryPrompts as $i => $prompt)
                            <figure
                                class="group relative rounded-2xl overflow-hidden shadow-soft hover:shadow-card transition-all duration-500 aspect-square @if($i === 0) md:col-span-2 md:row-span-2 @endif">
                                <img src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt={{ urlencode($prompt . ', close up macro shot, luxury beauty salon aesthetic, soft lighting') }}&image_size=square_hd"
                                    alt="Portofolio Nail Art {{ $i + 1 }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    loading="lazy">
                                <figcaption
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4 sm:p-5">
                                    <div class="text-white">
                                        <div class="text-xs uppercase tracking-wider font-bold text-brand-200">Desain
                                            {{ $i + 1 }}
                                        </div>
                                        <div class="mt-1 font-serif text-lg sm:text-xl font-bold">Karya Terbaik</div>
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        {{-- =============== ARTISTS =============== --}}
        <section id="artists" class="py-20 sm:py-24 bg-gradient-soft">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Tim Nail Artist</span>
                    <h2 class="mt-3 section-heading">Ditangan Para Ahli Bersertifikat</h2>
                    <p class="section-subheading">Setiap nail artist kami melalui pelatihan intensif dan ratusan jam
                        terbang sebelum melayani pelanggan.</p>
                </div>
                <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @foreach($artists as $artist)
                    <article class="card !p-0 text-center">
                        <div class="relative h-64 overflow-hidden bg-gradient-brand">
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-brand-400 via-brand-500 to-amber-400 opacity-90">
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="w-32 h-32 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-6xl font-serif text-white font-extrabold border-4 border-white/30 shadow-2xl">
                                    {{ mb_substr($artist->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="absolute top-4 left-4 chip bg-white/90 backdrop-blur text-gray-900 shadow flex items-center gap-1">
                                <x-lucide-star class="w-3.5 h-3.5 text-amber-400 fill-amber-400" />
                                <b>{{ number_format($artist->reviews_avg_rating ?? 4.8, 1) }}</b>
                                <span class="text-gray-500 !text-xs">({{ $artist->reviews_count }})</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="font-serif text-2xl font-bold text-gray-900">{{ $artist->user->name }}</h3>
                            <p class="mt-2 text-sm text-gray-500">{{ $artist->bio ?? 'Nail Artist Profesional' }}</p>

                            <div class="mt-5 grid grid-cols-3 gap-2 text-xs">
                                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $idx => $d)
                                @php($sch = $artist->schedules->where('day_of_week', $idx + 1)->first())
                                <div
                                    class="px-2 py-1.5 rounded-lg @if($sch && !$sch->is_off) bg-brand-50 text-brand-700 font-semibold @else bg-gray-100 text-gray-400 line-through @endif">
                                    {{ $d }}
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('booking.index') }}#artist-{{ $artist->id }}"
                                    class="btn-primary w-full !py-2.5 !text-sm justify-center">
                                    Booking Artis Ini
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- =============== TESTIMONIALS =============== --}}
        <section id="testimonials" class="py-20 sm:py-24 relative overflow-hidden">
            <div class="absolute top-20 right-0 w-96 h-96 bg-amber-100 rounded-full blur-3xl opacity-40"></div>
            <div class="absolute -bottom-20 -left-10 w-96 h-96 bg-brand-100 rounded-full blur-3xl opacity-40"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center max-w-2xl mx-auto">
                    <span class="eyebrow">Kata Mereka</span>
                    <h2 class="mt-3 section-heading">Ribuan Pelanggan Telah Mempercayai Kami</h2>
                    <p class="section-subheading">Kepuasanmu adalah alasan kami untuk terus memberikan yang terbaik di
                        setiap sesi perawatan.</p>
                </div>

                <div class="mt-14 grid md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @php($defaultReviews = [
                        ['name' => 'Anastasya Mentari', 'rating' => 5, 'title' => 'Manicure-nya paling tahan lama!', 'comment' => 'Sudah 3 minggu gel manicurenya masih cantik, tidak pecah sama sekali. Nail artist-nya juga detail banget sesuai request desain dari Pinterest saya. Wajib coba!'],
                        ['name' => 'Dewi Lestari', 'rating' => 5, 'title' => 'Booking online-nya sangat membantu', 'comment' => 'Gak perlu antri lama, pilih jadwal dari rumah dan bayar DP lewat transfer. Pas datang langsung dilayani. Hasil nail art bridal saya bikin nangis, cantik banget!'],
                        ['name' => 'Rina Kartika', 'rating' => 5, 'title' => 'Tempatnya cozy, artists ramah', 'comment' => 'Suka banget sama interior studionya, wangi dan nyaman. Siti sangat sabar ngasih saran desain yang cocok sama warna kulit saya. Recommended!'],
                        ['name' => 'Putri Ayu', 'rating' => 4, 'title' => 'Hasil sesuai ekspektasi', 'comment' => 'Soft gel extension-nya bagus, natural dan tidak mudah patah. Harga agak mahal tapi sebanding sama kualitasnya. Saya pasti repeat order.'],
                        ['name' => 'Salma Nabila', 'rating' => 5, 'title' => 'Pelayanan VIP banget', 'comment' => 'Dapat welcome drink, pijat tangan, dan snack kecil selama perawatan. Pengalaman spa mewah dengan harga yang masih terjangkau. Love it!'],
                        ['name' => 'Jessica Wang', 'rating' => 5, 'title' => 'Booking system TOP!', 'comment' => 'Slot waktunya akurat, tidak bentrok. Reminder via WA juga dapet, jadi tidak pernah lupa jadwal. Nail artist Maya hasilnya selalu sempurna 5/5.'],
                    ])
                    @foreach($defaultReviews as $i => $r)
                        <article class="card p-6 sm:p-7 flex flex-col h-full bg-white/80 backdrop-blur border border-white">
                            <div class="flex items-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <x-lucide-star class="w-4 h-4 {{ $s <= $r['rating'] ? 'text-amber-400 fill-amber-400' : 'text-gray-200' }}" />
                                @endfor
                            </div>
                            <h3 class="mt-3 font-serif text-lg font-bold text-gray-900">{{ $r['title'] }}</h3>
                            <p class="mt-3 text-gray-600 text-sm leading-relaxed flex-1">"{{ $r['comment'] }}"</p>
                            <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3">
                                <div
                                    class="w-11 h-11 rounded-full bg-gradient-to-br @if($i % 3 === 0) from-brand-400 to-purple-400 @elseif($i % 3 === 1) from-amber-400 to-rose-400 @else from-purple-400 to-blue-400 @endif flex items-center justify-center text-white font-bold shadow-soft">
                                    {{ mb_substr($r['name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $r['name'] }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">Verified Customer <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-500 shrink-0" /></div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- =============== CTA SECTION =============== --}}
        <section class="py-20 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative rounded-[2.5rem] overflow-hidden shadow-card">
                    <div class="absolute inset-0 bg-gradient-hero"></div>
                    <div class="absolute inset-0 bg-dots opacity-20"></div>
                    <div
                        class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -translate-y-1/3 translate-x-1/3">
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-80 h-80 bg-amber-300/20 rounded-full blur-3xl translate-y-1/3 -translate-x-1/3">
                    </div>

                    <div class="relative grid lg:grid-cols-2 gap-10 p-8 sm:p-12 lg:p-16 items-center">
                        <div>
                            <span
                                class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur text-white text-xs font-bold uppercase tracking-wider border border-white/20">
                                Promo Spesial • Bulan Ini
                            </span>
                            <h2
                                class="mt-6 text-3xl sm:text-4xl lg:text-5xl font-serif font-extrabold text-white leading-tight">
                                Pertama Kali Booking?<br />
                                Dapatkan <span class="text-amber-300">Diskon 20%</span> <x-lucide-gift class="w-7 h-7 sm:w-9 sm:h-9 inline-block text-amber-300 align-sub" />
                            </h2>
                            <p class="mt-5 text-white/85 text-base sm:text-lg leading-relaxed max-w-lg">
                                Gunakan kode <b
                                    class="text-white bg-white/15 px-2.5 py-1 rounded-lg border border-white/20">NAILCANTIK20</b>
                                di transaksi pertama kamu.
                                Berlaku untuk semua layanan manicure, pedicure, dan nail art custom!
                            </p>

                            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('booking.index') }}"
                                    class="btn-primary !bg-white !text-brand-700 hover:!bg-amber-300 hover:!text-gray-900 text-base shadow-glow flex items-center justify-center gap-2">
                                    Klaim Voucher Saya
                                    <x-icon name="check-circle-2" class="w-5 h-5" />
                                </a>
                                <a href="https://wa.me/6281234567890" target="_blank"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full border-2 border-white/40 text-white font-semibold hover:bg-white/10 transition text-base">
                                    <x-icon name="message-square" class="w-5 h-5" />
                                    Konsultasi via WA
                                </a>
                            </div>
                        </div>

                        <div class="hidden lg:block relative h-80">
                            <div
                                class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-[2.5rem] backdrop-blur-xl border border-white/20 rotate-6 animate-float">
                            </div>
                            <div
                                class="absolute bottom-0 left-0 w-72 h-72 rounded-[2.5rem] overflow-hidden shadow-2xl border-4 border-white/30 -rotate-3">
                                <img src="https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=happy%20asian%20woman%20smiling%20while%20showing%20her%20beautiful%20pink%20nail%20art%20manicure%20at%20luxury%20beauty%20salon&image_size=square_hd"
                                    alt="Promo Booknail" class="w-full h-full object-cover">
                            </div>
                            <div
                                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl px-5 py-3 shadow-card border border-amber-200 rotate-2">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl text-amber-500"><x-icon name="gift" class="w-8 h-8" /></div>
                                    <div>
                                        <div class="text-xs text-gray-500">Kode Promo</div>
                                        <div class="font-extrabold text-gradient text-xl tracking-wide">NAILCANTIK20
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- =============== FOOTER =============== --}}
    <footer
        class="relative bg-gradient-to-br from-gray-900 via-brand-950 to-gray-900 text-white pt-16 pb-8 overflow-hidden">
        <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-brand-700/30 rounded-full blur-3xl -translate-y-1/2">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <x-application-logo class="h-11 w-11" />
                        <span class="font-serif text-2xl font-extrabold">
                            <span class="text-gradient">Book</span><span class="text-white">nail</span>
                        </span>
                    </a>
                    <p class="mt-5 text-white/65 text-sm leading-relaxed max-w-sm">
                        Studio nail art premium pertama di Indonesia dengan sistem booking online terintegrasi.
                    </p>
                    <div class="mt-6 flex gap-3">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand-500 flex items-center justify-center transition"
                            title="Instagram">
                            <x-icon name="instagram" class="w-4 h-4" />
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand-500 flex items-center justify-center transition"
                            title="Facebook">
                            <x-icon name="facebook" class="w-4 h-4" />
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-brand-500 flex items-center justify-center transition"
                            title="TikTok">
                            <x-icon name="video" class="w-4 h-4" />
                        </a>
                        <a href="https://wa.me/62895401922922" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/10 hover:bg-emerald-500 flex items-center justify-center transition"
                            title="WhatsApp">
                            <x-icon name="phone" class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="font-serif text-lg font-bold text-white">Layanan</h4>
                    <ul class="mt-5 space-y-3">
                        @foreach($categories->take(5) as $c)
                            <li>
                                <a href="{{ route('services.index', ['category' => $c->slug]) }}"
                                    class="text-white/65 hover:text-brand-300 text-sm transition flex items-center gap-2">
                                    <x-icon name="{{ $c->icon ?? 'sparkles' }}" class="w-4 h-4" />
                                    {{ $c->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h4 class="font-serif text-lg font-bold text-white">Jelajahi</h4>
                    <ul class="mt-5 space-y-3 text-sm">
                        <li><a href="{{ route('home') }}"
                                class="text-white/65 hover:text-brand-300 transition">Beranda</a></li>
                        <li><a href="{{ route('services.index') }}"
                                class="text-white/65 hover:text-brand-300 transition">Semua Layanan</a></li>
                        <li><a href="#artists" class="text-white/65 hover:text-brand-300 transition">Tim Nail Artist</a>
                        </li>
                        <li><a href="#gallery" class="text-white/65 hover:text-brand-300 transition">Galeri
                                Portofolio</a></li>
                        <li><a href="{{ route('booking.index') }}"
                                class="text-white/65 hover:text-brand-300 transition">Booking Online</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-serif text-lg font-bold text-white">Kontak Studio</h4>
                    <ul class="mt-5 space-y-4 text-sm">
                        <li class="flex gap-3">
                            <div class="w-9 h-9 shrink-0 rounded-xl bg-white/10 flex items-center justify-center text-white">
                                <x-lucide-map-pin class="w-4 h-4" />
                            </div>
                            <div class="text-white/75">
                                Ruko veranda metland puri no.9, RT.004/RW.008, <br />Petir, Cipondoh, Tangerang City,
                                Banten 15147
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-9 h-9 shrink-0 rounded-xl bg-white/10 flex items-center justify-center text-white">
                                <x-lucide-phone class="w-4 h-4" />
                            </div>
                            <div class="text-white/75">
                                <a href="tel:+6281234567890" class="hover:text-brand-300 transition">(021)
                                    123-4567</a><br />
                                <span class="text-xs">WA: +62 895-4019-22922</span>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <div class="w-9 h-9 shrink-0 rounded-xl bg-white/10 flex items-center justify-center text-white">
                                <x-lucide-clock class="w-4 h-4" />
                            </div>
                            <div class="text-white/75">
                                Senin–Minggu: 11.00–19.00<br />
                                <span class="text-xs text-brand-300"></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-14 pt-6 border-t border-white/10 flex flex-col sm:flex-row gap-4 items-center justify-between text-xs sm:text-sm text-white/55">
                <div>© {{ date('Y') }} Booknail Studio. All rights reserved. Made with <x-lucide-heart class="w-3.5 h-3.5 text-rose-400 inline-block fill-rose-400 align-middle" /> for beautiful nails.</div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Mobile Menu JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('icon-open');
            const iconClose = document.getElementById('icon-close');
            btn?.addEventListener('click', () => {
                menu?.classList.toggle('hidden');
                iconOpen?.classList.toggle('hidden');
                iconClose?.classList.toggle('hidden');
            });
            document.querySelectorAll('.mobile-link').forEach(a => {
                a.addEventListener('click', () => {
                    menu?.classList.add('hidden');
                    iconOpen?.classList.remove('hidden');
                    iconClose?.classList.add('hidden');
                });
            });
            const header = document.querySelector('header');
            window.addEventListener('scroll', () => {
                if (!header) return;
                if (window.scrollY > 20) {
                    header.classList.add('shadow-soft');
                } else {
                    header.classList.remove('shadow-soft');
                }
            });
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

</body>

</html>