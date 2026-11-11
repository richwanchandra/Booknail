<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Katalog Layanan Nail Art — {{ config('app.name') }}</title>
    <meta name="description" content="Jelajahi 15+ layanan nail art premium: Manicure, Pedicure, Nail Art Custom, Extension, dan Treatment dengan harga transparan.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">

{{-- NAVBAR --}}
<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-brand-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-18">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <x-application-logo class="h-9 w-9 transition-transform group-hover:rotate-6 duration-500"/>
                <span class="font-serif text-xl font-extrabold tracking-tight">
                    <span class="text-gradient">Book</span><span class="text-gray-900">nail</span>
                </span>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-brand-600 transition">Beranda</a>
                <a href="{{ route('services.index') }}" class="text-brand-600">Layanan</a>
                <a href="{{ route('booking.index') }}" class="text-gray-600 hover:text-brand-600 transition">Booking</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-brand-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600 transition">Masuk</a>
                @endauth
            </nav>
            <a href="{{ route('booking.index') }}" class="btn-primary !py-2 !px-5 !text-sm">
                Booking Sekarang
            </a>
        </div>
    </div>
</header>

<main class="flex-1">
{{-- PAGE HEADER --}}
<section class="relative py-6 sm:py-8 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-soft"></div>
    <div class="absolute -top-10 -left-10 w-56 h-56 bg-brand-200 rounded-full blur-3xl opacity-50"></div>
    <div class="absolute -bottom-8 -right-8 w-64 h-64 bg-amber-100 rounded-full blur-3xl opacity-60"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="eyebrow">Katalog Lengkap</span>
        <h1 class="mt-4 section-heading flex items-center justify-center gap-2">
            @if($activeCategory)
                <x-icon name="{{ $activeCategory->icon ?? 'sparkles' }}" class="w-8 h-8 text-brand-600" /> {{ $activeCategory->name }}
            @else
                Semua Layanan Nail Art
            @endif
        </h1>
        <p class="section-subheading">
            @if($activeCategory)
                Menampilkan {{ $services->total() }} layanan dalam kategori {{ $activeCategory->name }}.
            @else
                Pilih dari {{ $services->total() }}+ layanan premium untuk perawatan kuku cantikmu.
            @endif
        </p>

        {{-- Breadcrumb --}}
        <div class="mt-6 flex items-center justify-center gap-2 text-xs sm:text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition inline-flex items-center gap-1">
                <x-icon name="home" class="w-3.5 h-3.5" /> Beranda
            </a>
            <span>/</span>
            <a href="{{ route('services.index') }}" class="hover:text-brand-600 transition">Layanan</a>
            @if($activeCategory)
                <span>/</span>
                <span class="text-brand-600 font-semibold">{{ $activeCategory->name }}</span>
            @endif
        </div>
    </div>
</section>

{{-- FILTER + LIST --}}
<section class="pb-20 mt-4 sm:mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Category Filter Chips --}}
        <div class="mb-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('services.index') }}"
               class="chip @if(!$activeCategory) chip-active @else chip-inactive @endif inline-flex items-center gap-1.5">
                <x-icon name="sparkles" class="w-3.5 h-3.5" /> Semua ({{ \App\Models\Service::active()->count() }})
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('services.index', ['category' => $cat->slug]) }}"
                   class="chip @if($activeCategory && $activeCategory->slug === $cat->slug) chip-active @else chip-inactive @endif inline-flex items-center gap-1.5">
                    <x-icon name="{{ $cat->icon ?? 'sparkles' }}" class="w-3.5 h-3.5" /> {{ $cat->name }} ({{ $cat->services_count }})
                </a>
            @endforeach
        </div>

        {{-- Service Grid --}}
        @if($services->count())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($services as $svc)
                    <article class="card group flex flex-col">
                        <a href="{{ route('services.show', $svc) }}" class="relative block aspect-[4/3] overflow-hidden">
                            <img src="{{ $svc->image_url }}"
                                 alt="{{ $svc->name }}"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>
                            <div class="absolute top-4 left-4">
                                <span class="chip bg-white/95 backdrop-blur text-gray-900 !text-xs !font-bold shadow flex items-center gap-1.5">
                                    <x-icon name="{{ $svc->category->icon ?? 'sparkles' }}" class="w-3.5 h-3.5 text-brand-600" /> {{ $svc->category->name }}
                                </span>
                            </div>
                            <div class="absolute bottom-4 right-4">
                                <div class="price-tag bg-white/95 backdrop-blur px-3.5 py-1.5 rounded-full shadow">
                                    <span class="currency text-brand-600">Rp</span>
                                    <span class="amount text-gray-900">{{ number_format($svc->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-serif text-xl font-bold text-gray-900 group-hover:text-brand-600 transition">
                                    <a href="{{ route('services.show', $svc) }}">{{ $svc->name }}</a>
                                </h3>
                                <span class="inline-flex items-center gap-1 shrink-0 text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    {{ $svc->formatted_duration }}
                                </span>
                            </div>
                            <p class="mt-2.5 text-sm text-gray-600 leading-relaxed">{{ $svc->description }}</p>
                            <div class="mt-5 pt-5 border-t border-gray-100 flex items-center gap-3 mt-auto">
                                <a href="{{ route('booking.index', ['service' => $svc->id]) }}" class="btn-primary flex-1 !py-2.5 !text-sm justify-center">
                                    Booking Sekarang
                                </a>
                                <a href="{{ route('services.show', $svc) }}" class="btn-ghost !py-2.5 border border-gray-200">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($services->hasPages())
                <div class="mt-14">
                    {{ $services->onEachSide(1)->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-24">
                <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-500 shadow-soft">
                    <x-lucide-sparkles class="w-8 h-8" />
                </div>
                <h3 class="font-serif text-2xl font-bold text-gray-900">Belum ada layanan</h3>
                <p class="mt-2 text-gray-500">Kategori ini belum memiliki layanan. Silakan pilih kategori lain.</p>
                <div class="mt-6">
                    <a href="{{ route('services.index') }}" class="btn-primary">Tampilkan Semua Layanan</a>
                </div>
            </div>
        @endif
    </div>
</section>
</main>

{{-- FOOTER --}}
<footer class="bg-gradient-to-br from-gray-900 via-brand-950 to-gray-900 text-white pt-14 pb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3 items-center">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10"/>
                <span class="font-serif text-xl font-extrabold">
                    <span class="text-gradient">Book</span><span class="text-white">nail</span>
                </span>
            </div>
            <div class="text-center text-white/60 text-sm">
                Studio nail art premium dengan sistem booking online terintegrasi.
            </div>
            <div class="text-center md:text-right text-xs text-white/50">
                © {{ date('Y') }} Booknail Studio. Made with <x-lucide-heart class="w-3.5 h-3.5 text-rose-400 inline-block fill-rose-400 align-middle" />
            </div>
        </div>
    </div>
</footer>

</body>
</html>
