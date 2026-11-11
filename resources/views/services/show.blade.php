<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $service->name }} — {{ config('app.name') }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($service->description, 150) }}">
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

{{-- BREADCRUMB --}}
<section class="py-6 border-b border-gray-100 bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-brand-600 transition inline-flex items-center gap-1.5">
                <x-lucide-home class="w-4 h-4 text-gray-400" />
                <span>Beranda</span>
            </a>
            <span>/</span>
            <a href="{{ route('services.index') }}" class="hover:text-brand-600 transition">Layanan</a>
            <span>/</span>
            <a href="{{ route('services.index', ['category' => $service->category->slug]) }}" class="hover:text-brand-600 transition">{{ $service->category->name }}</a>
            <span>/</span>
            <span class="text-brand-600 font-semibold truncate">{{ $service->name }}</span>
        </div>
    </div>
</section>

{{-- DETAIL SERVICE --}}
<section class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16">
            {{-- IMAGE --}}
            <div class="relative">
                <div class="card !p-0 overflow-hidden aspect-[4/3]">
                    <img src="{{ $service->image_url }}"
                         alt="{{ $service->name }}"
                         class="w-full h-full object-cover">
                </div>
                <div class="absolute -bottom-5 -left-5 hidden sm:block">
                    <div class="card bg-gradient-brand text-white px-6 py-4 !shadow-glow">
                        <div class="text-xs opacity-90 font-semibold uppercase tracking-wider">Durasi</div>
                        <div class="font-serif text-2xl font-bold">{{ $service->formatted_duration }}</div>
                    </div>
                </div>
            </div>

            {{-- CONTENT --}}
            <div class="flex flex-col">
                <div class="mb-4 flex items-center gap-3">
                    <span class="chip bg-brand-50 text-brand-700 border-brand-200 !font-bold flex items-center gap-1.5">
                        <x-icon name="{{ $service->category->icon ?? 'sparkles' }}" class="w-4 h-4 text-brand-600" /> {{ $service->category->name }}
                    </span>
                    <span class="chip bg-amber-50 text-amber-700 border-amber-200 !font-bold flex items-center gap-1.5">
                        <x-icon name="clock" class="w-4 h-4 text-amber-600" /> {{ $service->formatted_duration }}
                    </span>
                </div>

                <h1 class="font-serif text-4xl sm:text-5xl font-extrabold text-gray-900 leading-tight">
                    {{ $service->name }}
                </h1>

                <div class="mt-6 flex items-baseline gap-3">
                    <span class="text-gray-400 line-through text-lg font-semibold">
                        Rp {{ number_format($service->price * 1.2, 0, ',', '.') }}
                    </span>
                    <span class="price-tag text-3xl sm:text-4xl">
                        <span class="currency text-brand-600">Rp</span>
                        <span class="amount text-gray-900 font-extrabold">{{ number_format($service->price, 0, ',', '.') }}</span>
                    </span>
                    <span class="chip chip-active !text-xs !py-1">
                        20% OFF
                    </span>
                </div>

                <p class="mt-6 text-gray-600 leading-relaxed text-lg">
                    {{ $service->description }}
                </p>

                {{-- FEATURES --}}
                <div class="mt-8 grid sm:grid-cols-2 gap-3">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <x-lucide-check class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="font-bold text-gray-900 text-sm">Produk Premium</div>
                            <div class="text-xs text-gray-500">Menggunakan produk import berkualitas</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <x-lucide-check class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="font-bold text-gray-900 text-sm">Hasil Tahan Lama</div>
                            <div class="text-xs text-gray-500">Hingga 3-4 minggu perawatan</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <x-lucide-check class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="font-bold text-gray-900 text-sm">Artist Berpengalaman</div>
                            <div class="text-xs text-gray-500">Sertifikasi dan ribuan jam terbang</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <x-lucide-check class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="font-bold text-gray-900 text-sm">Garansi 7 Hari</div>
                            <div class="text-xs text-gray-500">Gratis touch up jika bermasalah</div>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="mt-10 flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('booking.index', ['service' => $service->id]) }}" class="btn-primary !py-3.5 !text-base justify-center flex items-center gap-2">
                        <x-lucide-calendar-plus class="w-5 h-5" />
                        <span>Booking Layanan Ini</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="btn-secondary !py-3.5 !text-base justify-center border-2">
                        Lihat Layanan Lain
                    </a>
                </div>

                {{-- INFO --}}
                <div class="mt-6 p-4 rounded-2xl bg-brand-50/50 border border-brand-100 flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                        <x-lucide-lightbulb class="w-5 h-5" />
                    </div>
                    <div class="text-sm text-gray-600">
                        <strong class="text-gray-900">Tips:</strong> Booking minimal 1 hari sebelumnya untuk mendapatkan slot artist favoritmu. Dapatkan 1 poin loyalty setiap Rp 10.000 belanja!
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- RELATED SERVICES --}}
@if($relatedServices && $relatedServices->count())
<section class="py-16 bg-gray-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="eyebrow">Mungkin Kamu Suka</span>
            <h2 class="mt-3 section-heading">Layanan Serupa</h2>
            <p class="section-subheading">Layanan lain dalam kategori {{ $service->category->name }} yang cocok untukmu.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedServices as $svc)
                <article class="card group flex flex-col">
                    <a href="{{ route('services.show', $svc) }}" class="relative block aspect-[4/3] overflow-hidden">
                        <img src="{{ $svc->image_url }}"
                             alt="{{ $svc->name }}"
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 right-3">
                            <div class="price-tag bg-white/95 backdrop-blur px-3 py-1 rounded-full shadow text-sm">
                                <span class="currency text-brand-600">Rp</span>
                                <span class="amount text-gray-900 font-bold">{{ number_format($svc->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="p-5 flex flex-col flex-1">
                        <h3 class="font-serif text-lg font-bold text-gray-900 group-hover:text-brand-600 transition line-clamp-2">
                            <a href="{{ route('services.show', $svc) }}">{{ $svc->name }}</a>
                        </h3>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-xs text-gray-500 font-semibold">⏱ {{ $svc->formatted_duration }}</span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 mt-auto">
                            <a href="{{ route('booking.index', ['service' => $svc->id]) }}" class="btn-primary w-full !py-2 !text-sm justify-center">
                                Booking
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA BANNER --}}
<section class="py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card gradient-hero !p-0 overflow-hidden">
            <div class="relative py-12 px-8 sm:px-12 lg:px-16 text-center">
                <span class="eyebrow !bg-white/15 !text-white/90 !border-white/20">PROMO SPESIAL</span>
                <h2 class="mt-4 font-serif text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                    Booking Pakai Kode <span class="underline decoration-amber-300 decoration-wavy underline-offset-4">NAILCANTIK20</span>
                </h2>
                <p class="mt-4 text-white/80 text-lg max-w-2xl mx-auto">
                    Diskon 20% untuk booking pertama. Berlaku untuk semua layanan. Promo terbatas!
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('booking.index', ['service' => $service->id]) }}" class="bg-white text-brand-700 hover:bg-amber-100 font-bold px-8 py-3.5 rounded-2xl shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                        Booking Sekarang →
                    </a>
                    <a href="{{ route('services.index') }}" class="bg-white/10 text-white hover:bg-white/20 backdrop-blur border border-white/30 font-bold px-8 py-3.5 rounded-2xl transition">
                        Lihat Semua Layanan
                    </a>
                </div>
            </div>
        </div>
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
