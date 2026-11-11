<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Appointment — {{ config('app.name') }}</title>
    <meta name="description" content="Booking appointment nail art premium di Booknail Studio.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-fcm-scripts />
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col">

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
                <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-brand-600 transition">Layanan</a>
                <a href="{{ route('booking.index') }}" class="text-brand-600">Booking</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-brand-600 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-600 transition">Masuk</a>
                @endauth
            </nav>
            <a href="{{ route('booking.index') }}" class="btn-primary !py-2 !px-5 !text-sm">Booking Sekarang</a>
        </div>
    </div>
</header>

<main class="flex-1">

<section class="relative py-12 sm:py-16 overflow-hidden">
    <div class="absolute inset-0 bg-gradient-soft"></div>
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-brand-200 rounded-full blur-3xl opacity-50"></div>
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="eyebrow">Easy Booking in 4 Steps</span>
        <h1 class="mt-3 section-heading">Reservasi Nail Art Premium</h1>
        <p class="section-subheading max-w-2xl mx-auto">
            Pilih layanan favoritmu, tentukan nail artist dan jadwal, upload referensi desain, lalu bayar DP. Mudah & cepat!
        </p>
    </div>
</section>

@php
    $currentStep = session('booking_current_step', 1);
    $hasStep1 = (bool) $step1;
    if (!$hasStep1) $currentStep = 1;
@endphp

<section class="pb-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="card !p-0 overflow-hidden">
            <div class="px-4 sm:px-8 py-6 border-b border-gray-100">
                <div class="relative flex items-center justify-between max-w-3xl mx-auto">
                    <div class="absolute top-5 left-0 right-0 h-1 bg-gray-100 hidden sm:block">
                        <div class="h-full gradient-brand rounded-full transition-all duration-500" style="width: {{ $currentStep * 25 }}%"></div>
                    </div>
                    @foreach(['1' => ['Pilih Layanan', 'palette'], '2' => ['Jadwal', 'calendar'], '3' => ['Desain', 'camera'], '4' => ['Pembayaran', 'credit-card']] as $n => [$label, $icon])
                        <div class="relative flex flex-col items-center gap-2 z-10 w-1/4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-base
                                @if((int)$n <= $currentStep) gradient-brand text-white shadow-glow
                                @else bg-gray-100 text-gray-400 @endif">
                                <span class="sm:hidden flex items-center justify-center"><x-icon name="{{ $icon }}" class="w-4 h-4" /></span>
                                <span class="hidden sm:block">{{ $n }}</span>
                            </div>
                            <div class="text-center">
                                <div class="text-xs sm:text-sm font-bold @if((int)$n <= $currentStep) text-brand-700 @else text-gray-400 @endif">{{ $label }}</div>
                                <div class="text-[10px] sm:text-xs text-gray-400">Step {{ $n }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @if($hasStep1)
            <div class="mt-4 flex justify-center">
                <form action="{{ route('booking.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-gray-500 hover:text-red-600 transition inline-flex items-center gap-1.5">
                        <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                        <span>Reset Flow Booking</span>
                    </button>
                </form>
            </div>
        @endif
    </div>
</section>

@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="card p-4 border-2 border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-semibold flex items-center gap-2">
            <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 space-y-2">
        @foreach($errors->all() as $err)
            <div class="card p-4 border-2 border-red-200 bg-red-50 text-red-800 text-sm font-semibold flex items-center gap-2">
                <x-lucide-alert-triangle class="w-5 h-5 text-red-600 shrink-0" />
                <span>{{ $err }}</span>
            </div>
        @endforeach
    </div>
@endif

<section class="pb-16 -mt-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">

                @if($currentStep === 1)
                    <form action="{{ route('booking.storeStep1') }}" method="POST" id="step1Form">
                        @csrf

                        <div class="card">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h2 class="font-serif text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                                        <x-lucide-sparkles class="w-6 h-6 text-brand-600" />
                                        <span>Pilih Layanan</span>
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-500">Pilih satu atau lebih layanan nail art yang kamu inginkan</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($services as $svc)
                                    <label class="service-card card-option relative block p-4 rounded-2xl border transition-all cursor-pointer select-none
                                        {{ $preselectedService && $preselectedService->id === $svc->id ? 'border-brand-500 bg-brand-50/50' : 'border-gray-100 bg-white hover:bg-gray-50 hover:border-brand-200' }}"
                                        data-price="{{ $svc->price }}"
                                        data-duration="{{ $svc->duration_minutes }}">
                                        <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}" class="peer sr-only svc-checkbox"
                                            {{ $preselectedService && $preselectedService->id === $svc->id ? 'checked' : '' }}>
                                        <div class="absolute top-3 right-3 w-6 h-6 rounded-full border-2 border-gray-200 bg-white flex items-center justify-center transition-all
                                            peer-checked:border-brand-500 peer-checked:bg-brand-500">
                                            <x-icon name="check" class="w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition" />
                                        </div>
                                        <div class="flex gap-3 pr-8">
                                            <div class="w-14 h-14 rounded-xl shrink-0 overflow-hidden gradient-brand flex items-center justify-center text-white">
                                                <x-icon name="{{ $svc->category->icon ?? 'sparkles' }}" class="w-7 h-7" />
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h3 class="font-bold text-gray-900 text-sm leading-tight line-clamp-2">{{ $svc->name }}</h3>
                                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $svc->category->name }}</span>
                                                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold flex items-center gap-1">
                                                        <x-icon name="clock" class="w-3 h-3" /> {{ $svc->formatted_duration }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 font-extrabold text-brand-700">{{ $svc->formatted_price }}</div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="lg:hidden mt-8">
                            @include('booking.partials.summary-step1')
                        </div>

                        <div class="mt-8 lg:hidden">
                            @auth
                                <button type="submit" class="btn-primary w-full !py-3.5 justify-center !text-base">
                                    Lanjut ke Step 2 →
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn-primary w-full !py-3.5 justify-center !text-base inline-flex items-center gap-2">
                                    <x-lucide-lock class="w-4 h-4" />
                                    <span>Login untuk Booking Step 2 →</span>
                                </a>
                            @endauth
                        </div>
                    </form>

                @elseif($currentStep === 2 && $step1)
                    @include('booking.partials.step2-slot')
                @elseif($currentStep === 3 && $step1)
                    @include('booking.partials.step3-design')
                @elseif($currentStep === 4 && $step1)
                    @include('booking.partials.step4-payment')
                @endif

            </div>

            <div class="lg:col-span-1 hidden lg:block">
                <div class="sticky top-24">
                    @if($currentStep === 1)
                        @include('booking.partials.summary-step1', ['showButton' => true])
                    @else
                        @include('booking.partials.summary-multistep')
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<footer class="bg-gradient-to-br from-gray-900 via-brand-950 to-gray-900 text-white pt-14 pb-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-3 items-center">
            <div class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10"/>
                <span class="font-serif text-xl font-extrabold">
                    <span class="text-gradient">Book</span><span class="text-white">nail</span>
                </span>
            </div>
            <div class="text-center text-white/60 text-sm">Studio nail art premium dengan sistem booking online terintegrasi.</div>
            <div class="text-center md:text-right text-xs text-white/50">© {{ date('Y') }} Booknail Studio. Made with <x-lucide-heart class="w-3.5 h-3.5 text-rose-400 inline-block fill-rose-400 align-middle" /></div>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const services = document.querySelectorAll('.svc-checkbox');
    const cards = document.querySelectorAll('.service-card');
    const countEl = document.getElementById('selectedServiceCount');
    const subTotalEl = document.getElementById('subtotalAmount');
    const grandTotalEl = document.getElementById('grandTotal');
    const dpAmountEl = document.getElementById('dpAmount');
    const totalDurationEl = document.getElementById('totalDuration');
    const summaryEl = document.getElementById('summaryServices');
    if (summaryEl) {
        const fmt = (n) => 'Rp ' + n.toLocaleString('id-ID');
        function updateSummary() {
            let total = 0, duration = 0, count = 0, items = [];
            cards.forEach((card, idx) => {
                const cb = services[idx];
                const price = parseFloat(card.dataset.price);
                const dur = parseInt(card.dataset.duration);
                const name = card.querySelector('h3').textContent.trim();
                const cat = card.querySelector('.bg-gray-100')?.textContent.trim() || '';
                if (cb && cb.checked) {
                    total += price; duration += dur; count++;
                    items.push({ name, price, dur, cat });
                    card.classList.remove('border-gray-100','bg-white');
                    card.classList.add('border-brand-500','bg-brand-50/50');
                } else {
                    card.classList.add('border-gray-100','bg-white');
                    card.classList.remove('border-brand-500','bg-brand-50/50');
                }
            });
            if (countEl) countEl.textContent = count;
            if (subTotalEl) subTotalEl.textContent = fmt(total);
            if (grandTotalEl) grandTotalEl.textContent = fmt(total);
            if (dpAmountEl) dpAmountEl.textContent = fmt(Math.ceil(total * 0.3));
            if (totalDurationEl) {
                const h = Math.floor(duration/60), m = duration%60;
                totalDurationEl.textContent = (h>0&&m>0?`${h}j ${m}m`:(h>0?`${h} jam`:`${m} menit`));
            }
            if (count === 0) {
                summaryEl.innerHTML = `<div class="text-center py-6 text-gray-400 text-sm"><div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2Z"/></svg></div>Belum ada layanan dipilih</div>`;
            } else {
                summaryEl.innerHTML = items.map(it => `
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                        <div class="w-8 h-8 rounded-lg gradient-brand text-white flex items-center justify-center shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 text-sm leading-tight">${it.name}</div>
                            <div class="text-xs text-gray-500 mt-0.5">${it.cat} · ${Math.floor(it.dur/60)?Math.floor(it.dur/60)+'j ':''}${it.dur%60}m</div>
                        </div>
                        <div class="font-bold text-brand-700 text-sm shrink-0">${fmt(it.price)}</div>
                    </div>`).join('');
            }
        }
        services.forEach(cb => cb.addEventListener('change', updateSummary));
        updateSummary();
    }

    // Artist selection moved to Step 2

    const loyaltyInput = document.getElementById('redeemInput');
    if (loyaltyInput) {
        const maxPoints = parseInt(loyaltyInput.max || '0');
        const balanceDisplay = document.getElementById('balanceDisplay');
        const discountDisplay = document.getElementById('discountDisplay');
        const discountNominal = document.getElementById('discountNominal');
        const totalFinal = document.getElementById('grandTotalFinal');
        const totalBefore = parseInt(document.getElementById('totalBeforeHidden').value || '0');
        const dpFinal = document.getElementById('dpFinal');
        loyaltyInput.addEventListener('input', function() {
            let v = parseInt(this.value || '0');
            if (isNaN(v)) v = 0;
            if (v > maxPoints) { v = maxPoints; this.value = v; }
            const unit = Math.floor(v / 100);
            const discount = unit * 10000;
            const final = Math.max(0, totalBefore - discount);
            const dp = Math.ceil(final * 0.3);
            if (balanceDisplay) balanceDisplay.textContent = (maxPoints - v).toLocaleString('id-ID');
            if (discountDisplay) discountDisplay.textContent = v.toLocaleString('id-ID');
            if (discountNominal) discountNominal.textContent = 'Rp ' + discount.toLocaleString('id-ID');
            if (totalFinal) totalFinal.textContent = 'Rp ' + final.toLocaleString('id-ID');
            if (dpFinal) dpFinal.textContent = 'Rp ' + dp.toLocaleString('id-ID');
        });
        const useBtn = document.getElementById('redeemAllBtn');
        if (useBtn) useBtn.addEventListener('click', () => { loyaltyInput.value = maxPoints; loyaltyInput.dispatchEvent(new Event('input')); });
        const resetBtn = document.getElementById('redeemResetBtn');
        if (resetBtn) resetBtn.addEventListener('click', () => { loyaltyInput.value = 0; loyaltyInput.dispatchEvent(new Event('input')); });
    }

    const form = document.getElementById('step1Form');
    if (form) {
        form.addEventListener('submit', function(ev) {
            const checked = document.querySelectorAll('.svc-checkbox:checked');
            if (checked.length === 0) {
                ev.preventDefault();
                alert('Pilih minimal 1 layanan untuk booking!');
                return false;
            }
        });
    }
});
</script>

</body>
</html>
