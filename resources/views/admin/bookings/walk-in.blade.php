@extends('layouts.admin')
@section('title', 'Input Pesanan Walk-In')
@section('pageTitle', 'Input Pesanan Walk-In')
@section('pageSubtitle', 'Registrasi pelanggan langsung di gerai, kunci slot jadwal, dan sinkronkan dengan kasir.')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pb-16"
     x-data="walkInWizard({
         defaultDate: '{{ $defaultDate }}',
         defaultTime: '{{ $defaultTime }}',
         dateOptions: {{ json_encode($dateOptions) }},
         slotsData: {{ json_encode($artistSlots) }},
         artistSummaries: {{ json_encode($artistSummaries) }},
         artistsData: {{ json_encode($artists->map(fn($a) => ['id' => $a->id, 'name' => optional($a->user)->name ?? 'Terapis', 'initials' => $a->initials, 'specialty' => $a->specialty])) }},
         defaultBranch: '{{ old('branch', 'Metland Puri') }}',
         initialArtistId: {{ old('artist_id', $artists->first()?->id ?? 'null') }},
         oldServices: {{ json_encode(old('service_ids', [])) }}
     })">

    <!-- TOP BAR / BREADCRUMB & LIVE CLOCK -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white rounded-3xl p-5 border border-[#DCD9D9]/80 shadow-soft">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bookings.index') }}" class="w-10 h-10 rounded-full border border-[#DCD9D9] bg-[#FBF9F8] flex items-center justify-center text-[#49454F] hover:text-[#1C1B1B] hover:bg-[#F4D7D4]/40 transition">
                <x-icon name="arrow-left" class="w-4 h-4" />
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#B76E79]">Kasir Studio Mode</span>
                </div>
                <h2 class="font-serif text-xl font-bold text-[#1C1B1B]">Form Input Walk-In Langsung</h2>
            </div>
        </div>

        <div class="flex items-center gap-4 bg-[#FBF9F8] px-4 py-2.5 rounded-2xl border border-[#DCD9D9]/60 text-xs">
            <div class="flex items-center gap-2 text-[#49454F]">
                <x-icon name="calendar" class="w-4 h-4 text-[#B76E79]" />
                <span class="font-semibold">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="h-4 w-px bg-[#DCD9D9]"></div>
            <div class="flex items-center gap-2 text-[#1C1B1B] font-mono font-bold">
                <x-icon name="clock" class="w-4 h-4 text-[#B76E79]" />
                <span x-text="liveClock">--:--:--</span> WIB
            </div>
        </div>
    </div>

    <!-- STEPPER PROGRESS BAR (MIRRORS CUSTOMER BOOKING STEPPER) -->
    <div class="bg-white rounded-3xl border border-gray-200/80 p-5 sm:p-7 shadow-soft">
        <div class="relative flex items-center justify-between max-w-3xl mx-auto">
            <!-- Connecting Progress Line -->
            <div class="absolute top-5 left-10 right-10 h-1 bg-gray-100 hidden sm:block -z-0">
                <div class="h-full bg-gradient-to-r from-[#991b1b] to-[#B76E79] rounded-full transition-all duration-500"
                     :style="'width: ' + ((currentStep - 1) * 33.33) + '%'"></div>
            </div>

            <!-- Step 1: Pilih Layanan -->
            <div class="relative flex flex-col items-center gap-2 z-10 cursor-pointer" @click="goToStep(1)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-sm transition-all duration-300"
                     :class="currentStep >= 1 ? 'bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 ring-4 ring-[#991b1b]/15' : 'bg-gray-100 text-gray-400 border border-gray-200'">
                    <template x-if="currentStep > 1">
                        <x-lucide-check class="w-4 h-4 stroke-[3]" />
                    </template>
                    <template x-if="currentStep <= 1">
                        <span>1</span>
                    </template>
                </div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm font-bold transition-colors"
                         :class="currentStep >= 1 ? 'text-[#991b1b]' : 'text-gray-400'">Pilih Layanan</div>
                    <div class="text-[10px] text-gray-400">Step 1</div>
                </div>
            </div>

            <!-- Step 2: Jadwal -->
            <div class="relative flex flex-col items-center gap-2 z-10 cursor-pointer" @click="goToStep(2)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-sm transition-all duration-300"
                     :class="currentStep >= 2 ? 'bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 ring-4 ring-[#991b1b]/15' : 'bg-gray-100 text-gray-400 border border-gray-200'">
                    <template x-if="currentStep > 2">
                        <x-lucide-check class="w-4 h-4 stroke-[3]" />
                    </template>
                    <template x-if="currentStep <= 2">
                        <span>2</span>
                    </template>
                </div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm font-bold transition-colors"
                         :class="currentStep >= 2 ? 'text-[#991b1b]' : 'text-gray-400'">Jadwal</div>
                    <div class="text-[10px] text-gray-400">Step 2</div>
                </div>
            </div>

            <!-- Step 3: Desain -->
            <div class="relative flex flex-col items-center gap-2 z-10 cursor-pointer" @click="goToStep(3)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-sm transition-all duration-300"
                     :class="currentStep >= 3 ? 'bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 ring-4 ring-[#991b1b]/15' : 'bg-gray-100 text-gray-400 border border-gray-200'">
                    <template x-if="currentStep > 3">
                        <x-lucide-check class="w-4 h-4 stroke-[3]" />
                    </template>
                    <template x-if="currentStep <= 3">
                        <span>3</span>
                    </template>
                </div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm font-bold transition-colors"
                         :class="currentStep >= 3 ? 'text-[#991b1b]' : 'text-gray-400'">Desain</div>
                    <div class="text-[10px] text-gray-400">Step 3</div>
                </div>
            </div>

            <!-- Step 4: Pembayaran -->
            <div class="relative flex flex-col items-center gap-2 z-10 cursor-pointer" @click="goToStep(4)">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-sm transition-all duration-300"
                     :class="currentStep >= 4 ? 'bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 ring-4 ring-[#991b1b]/15' : 'bg-gray-100 text-gray-400 border border-gray-200'">
                    <span>4</span>
                </div>
                <div class="text-center">
                    <div class="text-xs sm:text-sm font-bold transition-colors"
                         :class="currentStep >= 4 ? 'text-[#991b1b]' : 'text-gray-400'">Pembayaran</div>
                    <div class="text-[10px] text-gray-400">Step 4</div>
                </div>
            </div>
        </div>

        <!-- Reset Flow Booking -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-center">
            <button type="button" @click="resetFlow()"
                    class="text-xs font-bold text-gray-500 hover:text-[#991b1b] transition inline-flex items-center gap-1.5 cursor-pointer">
                <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                <span>Reset Flow Booking</span>
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-soft">
            <div class="font-bold flex items-center gap-2 mb-2">
                <x-icon name="alert-circle" class="w-5 h-5 text-rose-600" />
                <span>Mohon periksa kembali input form Anda:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-700">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="walkInForm" method="POST" action="{{ route('admin.walk-in.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        @csrf

        <!-- MAIN FORM COLUMN (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- ═══════════════════════════════════════════════════════════════════════
                 STEP 1: PILIH LAYANAN, CABANG & IDENTITAS PELANGGAN
                 ═══════════════════════════════════════════════════════════════════════ -->
            <div x-show="currentStep === 1" x-transition.opacity.duration.200ms class="space-y-6">

                <!-- 1. PILIH GERAI / OUTLET -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Lokasi Gerai / Cabang</h3>
                        </div>
                        <span class="text-xs text-[#49454F]">Pilih outlet tempat tamu dilayani</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($branches as $branch)
                            @if($branch === 'Citra Garden 6')
                                @continue
                            @endif
                            <label class="branch-card relative flex items-center gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="branch === '{{ $branch }}' ? 'border-[#B76E79] bg-[#F4D7D4]/20 shadow-soft' : 'border-[#DCD9D9]/80 bg-[#FBF9F8] hover:border-[#B76E79]/50'"
                                   @click="branch = '{{ $branch }}'">
                                <input type="radio" name="branch" value="{{ $branch }}" class="hidden" :checked="branch === '{{ $branch }}'">
                                <div class="w-10 h-10 rounded-xl bg-white border border-[#DCD9D9] flex items-center justify-center text-[#B76E79] shadow-sm shrink-0">
                                    <x-icon name="map-pin" class="w-5 h-5" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-serif font-bold text-sm text-[#1C1B1B]">{{ $branch }}</div>
                                    <div class="text-[11px] text-[#49454F] truncate">
                                        Ruko Veranda, Cipondoh Tangerang
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 border-[#DCD9D9] flex items-center justify-center checkmark-circle"
                                     :class="branch === '{{ $branch }}' ? '!border-[#B76E79] bg-[#B76E79] text-white' : ''">
                                    <x-icon name="check" class="w-3 h-3" />
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- 2. DATA PELANGGAN -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">2</span>
                            <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Identitas Pelanggan</h3>
                        </div>

                        <!-- Toggle Mode Pelanggan -->
                        <div class="inline-flex p-1 rounded-full bg-[#F6F3F2] border border-[#DCD9D9]/70 text-xs">
                            <button type="button" @click="customerType = 'guest'"
                                    class="px-4 py-1.5 rounded-full font-bold transition cursor-pointer"
                                    :class="customerType === 'guest' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]'">
                                Tamu Walk-In (Manual)
                            </button>
                            <button type="button" @click="customerType = 'member'"
                                    class="px-4 py-1.5 rounded-full font-bold transition cursor-pointer"
                                    :class="customerType === 'member' ? 'bg-[#B76E79] text-white shadow-sm' : 'text-[#49454F] hover:text-[#1C1B1B]'">
                                Member Terdaftar
                            </button>
                        </div>
                        <input type="hidden" name="customer_type" :value="customerType">
                    </div>

                    <!-- FORM GUEST / MANUAL -->
                    <div x-show="customerType === 'guest'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                                    Nama Pelanggan <span class="text-[#B76E79]">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#49454F]">
                                        <x-icon name="user" class="w-4 h-4" />
                                    </div>
                                    <input type="text" name="customer_name" id="customerNameInput"
                                           x-model="customerName"
                                           placeholder="Contoh: Ibu Clara / Melati"
                                           class="w-full pl-10 pr-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79] focus:bg-white transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                                    No. WhatsApp / HP <span class="text-xs font-normal text-[#49454F]">(Opsional struk WA)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#49454F]">
                                        <x-icon name="phone" class="w-4 h-4" />
                                    </div>
                                    <input type="text" name="customer_phone" id="customerPhoneInput"
                                           x-model="customerPhone"
                                           placeholder="08123456789"
                                           class="w-full pl-10 pr-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79] focus:bg-white transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORM MEMBER TERDAFTAR -->
                    <div x-show="customerType === 'member'" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                                Pilih Akun Member Terdaftar <span class="text-[#B76E79]">*</span>
                            </label>
                            <select name="customer_id" id="customerIdSelect"
                                    x-model="customerId"
                                    @change="onMemberSelect($event)"
                                    class="w-full px-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79] focus:bg-white transition">
                                <option value="">-- Cari atau pilih member terdaftar --</option>
                                @foreach($registeredCustomers as $cust)
                                    <option value="{{ $cust->id }}" data-name="{{ $cust->name }}" data-phone="{{ $cust->phone }}" data-points="{{ $cust->loyalty_points }}">
                                        {{ $cust->name }} ({{ $cust->phone ?? 'Tanpa no. HP' }}) — {{ $cust->loyalty_points }} pts
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-[#49454F] mt-1.5">
                                Member yang dipilih dapat otomatis mengumpulkan poin loyalitas setelah perawatan selesai.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. PILIH LAYANAN (KATALOG & LIVE COUNTER) -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">3</span>
                            <div>
                                <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Kategori & Layanan Perawatan</h3>
                                <p class="text-xs text-[#49454F]">Pilih satu atau beberapa layanan yang diinginkan tamu</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 bg-[#F6F3F2] px-3.5 py-1.5 rounded-full text-xs font-semibold text-[#49454F]">
                            <span>Terpilih: <strong class="text-[#B76E79]" x-text="selectedServiceCount">0</strong> layanan</span>
                            <span>·</span>
                            <span><strong x-text="totalDuration">0</strong> mnt</span>
                        </div>
                    </div>

                    <!-- Filter Kategori Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-4 scrollbar-thin">
                        <button type="button" @click="activeCategory = 'all'"
                                class="px-4 py-2 rounded-full text-xs font-bold transition cursor-pointer"
                                :class="activeCategory === 'all' ? 'bg-[#B76E79] text-white shadow-sm' : 'bg-[#FBF9F8] border border-[#DCD9D9] text-[#49454F] hover:bg-[#F6F3F2]'">
                            Semua Kategori
                        </button>
                        @foreach($categories as $cat)
                            <button type="button" @click="activeCategory = '{{ $cat->id }}'"
                                    class="px-4 py-2 rounded-full text-xs font-bold transition cursor-pointer whitespace-nowrap"
                                    :class="activeCategory === '{{ $cat->id }}' ? 'bg-[#B76E79] text-white shadow-sm' : 'bg-[#FBF9F8] border border-[#DCD9D9] text-[#49454F] hover:bg-[#F6F3F2]'">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Grid Kartu Layanan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 max-h-[420px] overflow-y-auto pr-1">
                        @foreach($services as $svc)
                            <label class="service-card relative flex items-start gap-3.5 p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   x-show="activeCategory === 'all' || activeCategory === '{{ $svc->category_id }}'"
                                   :class="selectedServiceIds.includes({{ $svc->id }}) ? 'border-[#B76E79] bg-[#F4D7D4]/15 shadow-soft' : 'border-[#DCD9D9]/70 bg-[#FBF9F8] hover:border-[#B76E79]/40 hover:bg-white'"
                                   data-category-id="{{ $svc->category_id }}"
                                   data-price="{{ (int) $svc->price }}"
                                   data-duration="{{ (int) $svc->duration_minutes }}"
                                   data-name="{{ $svc->name }}">
                                <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}" class="hidden service-checkbox"
                                       :checked="selectedServiceIds.includes({{ $svc->id }})"
                                       @change="toggleService({{ $svc->id }}, {{ (int) $svc->price }}, {{ (int) $svc->duration_minutes }}, '{{ addslashes($svc->name) }}')">
                                
                                <div class="w-5 h-5 rounded-lg border-2 border-[#DCD9D9] mt-0.5 flex items-center justify-center check-box-icon shrink-0 transition"
                                     :class="selectedServiceIds.includes({{ $svc->id }}) ? '!border-[#B76E79] bg-[#B76E79] text-white' : 'bg-white'">
                                    <x-icon name="check" class="w-3 h-3" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#B76E79] bg-[#F4D7D4]/50 px-2 py-0.5 rounded-full truncate">
                                            {{ $svc->category?->name ?? 'Service' }}
                                        </span>
                                        <span class="text-xs font-semibold text-[#49454F] flex items-center gap-1 shrink-0">
                                            <x-icon name="clock" class="w-3 h-3 text-[#B76E79]" /> {{ $svc->duration_minutes }} mnt
                                        </span>
                                    </div>
                                    <div class="font-serif font-bold text-sm text-[#1C1B1B] mt-1 line-clamp-1">{{ $svc->name }}</div>
                                    <div class="font-semibold text-xs text-[#B76E79] mt-1">{{ $svc->formatted_price }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Step 1 Navigation Button -->
                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            Total Durasi: <strong class="text-gray-900" x-text="totalDuration + ' Menit'"></strong> · Total: <strong class="text-[#991b1b]" x-text="formattedTotalPrice"></strong>
                        </div>
                        <button type="button" @click="goToStep(2)"
                                class="px-6 py-3 rounded-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-xs shadow-md shadow-[#991b1b]/20 transition flex items-center gap-2 cursor-pointer">
                            <span>Lanjut ke Jadwal (Step 2)</span>
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </button>
                    </div>
                </div>

            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════
                 STEP 2: JADWAL & PILIH TERAPIS (JAM OPERASIONAL 11:00 - 19:00 WIB)
                 ═══════════════════════════════════════════════════════════════════════ -->
            <div x-show="currentStep === 2" x-transition.opacity.duration.200ms class="space-y-6">

                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">

                    {{-- Operating Hours & Dynamic Duration Banner --}}
                    <div class="mb-5 p-4 rounded-2xl bg-[#FAF8F7] border border-[#DCD9D9]/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#991b1b]/10 text-[#991b1b] flex items-center justify-center shrink-0">
                                <x-lucide-clock class="w-5 h-5 text-[#991b1b]" />
                            </div>
                            <div>
                                <div class="font-bold text-[#1C1B1B]">Jam Operasional Studio: 11:00 – 19:00 WIB</div>
                                <div class="text-[11px] text-[#49454F] mt-0.5">
                                    Slot jam dihitung otomatis berdasarkan total durasi layanan:
                                    <strong class="text-[#991b1b]" x-text="totalDuration + ' menit (' + formattedDurationLabel + ')'"></strong>.
                                </div>
                            </div>
                        </div>

                        {{-- Quick Action: Set Jam Sekarang --}}
                        <button type="button" @click="setNowTime()" class="text-xs font-bold text-[#991b1b] bg-[#991b1b]/10 hover:bg-[#991b1b]/20 px-3.5 py-2 rounded-full flex items-center gap-1.5 transition shrink-0 cursor-pointer">
                            <x-lucide-zap class="w-3.5 h-3.5" />
                            <span>Set Jam Sekarang</span>
                        </button>
                    </div>

                    {{-- HEADER KELOLA TERAPIS & SLOT (Mirrors Customer Reference) --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100 mb-6">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-[#991b1b] flex items-center justify-center text-white shadow-md shadow-[#991b1b]/20 shrink-0">
                                <x-lucide-calendar class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="font-serif text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">Pilih Terapis & Slot Jadwal</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    Estimasi durasi: <strong class="text-gray-800" x-text="formattedDurationLabel">1j 30m</strong> ·
                                    Tanggal: <strong class="text-gray-800">{{ \Carbon\Carbon::parse($defaultDate)->locale('id')->isoFormat('D MMMM Y') }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- HORIZONTAL DATE PICKER CAROUSEL (7 Days Pill Selector) --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-2.5">
                            <div class="flex-1 overflow-x-auto scrollbar-hide py-1">
                                <div class="flex gap-2.5 min-w-max">
                                    @foreach($dateOptions as $d)
                                        <a href="{{ route('admin.walk-in.create', ['date' => $d['date']]) }}"
                                           class="flex flex-col items-center justify-center w-[66px] h-[72px] rounded-2xl text-center transition-all duration-200 shrink-0 select-none {{ $d['active'] ? 'bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 scale-105 ring-2 ring-[#991b1b]/20 font-bold' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200/90 hover:border-gray-300' }}">
                                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $d['active'] ? 'text-white/80' : 'text-gray-400' }}">{{ $d['dayLabel'] }}</span>
                                            <span class="text-xl font-extrabold leading-none my-1">{{ $d['dayNumber'] }}</span>
                                            <span class="text-[10px] font-semibold {{ $d['active'] ? 'text-white/70' : 'text-gray-400' }}">{{ $d['monthLabel'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Quick Calendar Picker Button --}}
                            <div class="relative shrink-0 group" title="Pilih tanggal manual">
                                <input type="date"
                                       value="{{ $defaultDate }}"
                                       class="w-11 h-11 rounded-2xl border border-gray-200 bg-white text-transparent cursor-pointer hover:border-[#991b1b]/40 transition opacity-0 absolute inset-0 z-10"
                                       onchange="window.location.href='{{ route('admin.walk-in.create') }}?date=' + this.value">
                                <div class="w-11 h-11 rounded-2xl border border-gray-200 bg-white flex items-center justify-center text-gray-500 group-hover:text-[#991b1b] group-hover:border-[#991b1b]/40 shadow-sm transition">
                                    <x-lucide-sparkles class="w-4 h-4 text-[#991b1b]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SUBHEADING PILIH TERAPIS --}}
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h4 class="font-serif text-xs font-bold uppercase tracking-wider text-gray-500">
                            PILIH TERAPIS ({{ count($artists) }} TERSEDIA)
                        </h4>
                        <span class="text-xs text-gray-400 flex items-center gap-1">
                            <x-lucide-mouse-pointer-click class="w-3.5 h-3.5" /> Klik kartu untuk membuka slot
                        </span>
                    </div>

                    {{-- BAGIAN UTAMA (KARTU KELOLA TERAPIS & SLOT) --}}
                    <div class="space-y-4">
                        @foreach($artists as $art)
                            @php
                                $summary = $artistSummaries[$art->id] ?? [
                                    'text' => '8 Slot Aktif / 2 Terisi',
                                    'has_available' => true,
                                    'active' => 8,
                                    'booked' => 2,
                                    'available' => 6
                                ];
                            @endphp
                            <div class="rounded-3xl border transition-all duration-300 bg-white overflow-hidden shadow-sm"
                                 :class="{
                                     'ring-2 ring-[#991b1b] border-[#991b1b] shadow-md': selectedArtistId === {{ $art->id }},
                                     'border-gray-200/90 hover:border-gray-300': selectedArtistId !== {{ $art->id }}
                                 }">

                                {{-- Therapist Card Header --}}
                                <div class="p-5 sm:p-6 select-none transition-colors bg-white">
                                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                                        {{-- Left: Foto, Nama, Keahlian, Tombol Slot --}}
                                        <div class="flex items-center gap-4 cursor-pointer" @click="toggleAccordion({{ $art->id }})">
                                            <div class="shrink-0 relative">
                                                @if(!empty($art->photo))
                                                    <img src="{{ str_starts_with($art->photo, 'data:') ? $art->photo : asset('storage/' . $art->photo) }}"
                                                         alt="{{ optional($art->user)->name ?? 'Terapis' }}"
                                                         class="w-16 h-16 sm:w-20 sm:h-20 aspect-square rounded-2xl object-cover ring-2 ring-white shadow-sm">
                                                @else
                                                    <div class="w-16 h-16 sm:w-20 sm:h-20 aspect-square rounded-2xl bg-[#991b1b] flex items-center justify-center text-white font-serif font-extrabold text-2xl ring-2 ring-white shadow-sm">
                                                        {{ $art->initials }}
                                                    </div>
                                                @endif

                                                <div x-show="selectedArtistId === {{ $art->id }}" x-cloak
                                                     class="absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-[#991b1b] text-white flex items-center justify-center shadow-md ring-2 ring-white">
                                                    <x-lucide-check class="w-3.5 h-3.5 stroke-[3]" />
                                                </div>
                                            </div>

                                            <div class="min-w-0">
                                                <h4 class="font-serif text-base sm:text-lg font-bold text-gray-900 leading-tight truncate">
                                                    {{ optional($art->user)->name ?? 'Nail Artist' }}
                                                </h4>
                                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                    Nail Artist · {{ $art->specialty }}
                                                </p>

                                                {{-- Status Ringkasan & Accordion Button (Mirrors Customer Reference) --}}
                                                <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                                    <button type="button" @click.stop="toggleAccordion({{ $art->id }})"
                                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-[#991b1b] text-white shadow-sm shadow-[#991b1b]/20 hover:bg-[#7f1d1d] transition cursor-pointer">
                                                        <x-lucide-calendar class="w-3.5 h-3.5" />
                                                        <span x-text="getArtistSummaryText({{ $art->id }}, '{{ $summary['text'] }}')"></span>
                                                        <span class="inline-flex transition-transform duration-200" :class="expandedArtistId === {{ $art->id }} ? 'rotate-180' : ''">
                                                            <x-lucide-chevron-down class="w-3.5 h-3.5" />
                                                        </span>
                                                    </button>

                                                    <span x-show="selectedArtistId === {{ $art->id }}" x-cloak
                                                          class="text-[11px] font-extrabold text-[#991b1b] bg-[#991b1b]/10 px-2.5 py-1 rounded-lg">
                                                        Terapis Terpilih
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right: Rating & Tombol Aksi Admin --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center lg:flex-col lg:items-end justify-center gap-2.5 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-gray-100">
                                            {{-- Rating Badge --}}
                                            <div class="flex items-center gap-2">
                                                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-100 text-xs font-bold text-amber-700">
                                                    <x-lucide-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                                    <span>{{ $art->avg_rating }}</span>
                                                </div>
                                                <span class="text-xs text-gray-400">({{ $art->review_count }} ulasan)</span>
                                            </div>

                                            {{-- Tombol Aksi Admin --}}
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.artists.edit', $art->id) }}"
                                                   class="px-3 py-1.5 rounded-xl text-xs font-bold bg-[#FBF9F8] border border-[#DCD9D9] text-[#1C1B1B] hover:bg-[#F4D7D4]/40 hover:border-[#B76E79] transition flex items-center gap-1.5 shadow-sm"
                                                   title="Edit profil terapis">
                                                    <x-lucide-user-cog class="w-3.5 h-3.5 text-[#B76E79]" />
                                                    <span>Edit Profil Terapis</span>
                                                </a>

                                                <button type="button"
                                                        @click="openAddSlotModal({{ $art->id }}, '{{ addslashes(optional($art->user)->name ?? 'Terapis') }}')"
                                                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-[#991b1b] hover:bg-[#7f1d1d] text-white shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                                                        title="Tambah slot waktu baru untuk terapis ini">
                                                    <x-lucide-plus-circle class="w-3.5 h-3.5" />
                                                    <span>Tambah Slot Jam baru</span>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- GRID MANAJEMEN SLOT JAM (SLOT MANAGEMENT GRID) --}}
                                <div x-show="expandedArtistId === {{ $art->id }}"
                                     x-cloak
                                     class="border-t border-gray-100 px-5 sm:px-6 py-5 bg-white">

                                    <div class="mb-3.5 flex items-center justify-between">
                                        <span class="text-xs font-bold text-gray-700">Pilih Jam Mulai:</span>
                                        <span class="text-xs text-gray-400">
                                            Durasi: <strong class="text-gray-800" x-text="totalDuration + ' menit'"></strong> (11:00 – 19:00 WIB)
                                        </span>
                                    </div>

                                    {{-- Slot Cards Desktop Grid (4 Columns) --}}
                                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-3.5">
                                        <template x-for="(slot, idx) in (slotsData[{{ $art->id }}] || [])" :key="slot.id || idx">
                                            <div class="rounded-2xl border-2 p-3.5 sm:p-4 transition-all duration-200 select-none relative group bg-white shadow-sm flex flex-col justify-between"
                                                 :class="{
                                                     'border-[#991b1b] bg-[#991b1b]/5 shadow-md shadow-[#991b1b]/10 scale-[1.02] ring-2 ring-[#991b1b]/20': (selectedArtistId === {{ $art->id }} && selectedSlot === slot.value),
                                                     'border-gray-200 hover:border-[#991b1b]/50': (selectedArtistId !== {{ $art->id }} || selectedSlot !== slot.value) && slot.status === 'available',
                                                     'border-rose-200 bg-rose-50/40': slot.status === 'booked',
                                                     'border-gray-200 bg-gray-50/80 opacity-75': slot.status === 'inactive'
                                                 }">

                                                {{-- Top Row: Checkmark Circle & Status Badge --}}
                                                <div class="flex items-center justify-between gap-2 mb-1.5 cursor-pointer"
                                                     @click="slot.status === 'available' ? selectSlot({{ $art->id }}, '{{ addslashes(optional($art->user)->name ?? 'Terapis') }}', slot.value, slot.end_time) : null">
                                                    <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all shrink-0"
                                                         :class="(selectedArtistId === {{ $art->id }} && selectedSlot === slot.value) ? 'border-[#991b1b] bg-[#991b1b]' : 'border-gray-300 bg-white'">
                                                        <svg class="w-2.5 h-2.5 text-white transition"
                                                             :class="(selectedArtistId === {{ $art->id }} && selectedSlot === slot.value) ? 'opacity-100' : 'opacity-0'"
                                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>

                                                    {{-- Indikator Status Badge --}}
                                                    <div class="min-w-0 text-right">
                                                        <template x-if="slot.status === 'available'">
                                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider text-emerald-600 bg-[#E6F9F0] px-2 py-0.5 rounded-md">
                                                                Tersedia
                                                            </span>
                                                        </template>
                                                        <template x-if="slot.status === 'booked'">
                                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-md truncate max-w-[130px]"
                                                                  :title="'Terbooking oleh: ' + (slot.customer_name || 'Pelanggan')">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                                                <span class="truncate" x-text="'Terbooking (' + (slot.customer_name || 'Tamu') + ')'"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="slot.status === 'inactive'">
                                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold uppercase tracking-wider text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                                                                Nonaktif
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Time Range (11:00 - 12:30) --}}
                                                <div class="cursor-pointer mt-1" @click="slot.status === 'available' ? selectSlot({{ $art->id }}, '{{ addslashes(optional($art->user)->name ?? 'Terapis') }}', slot.value, slot.end_time) : null">
                                                    <div class="font-extrabold text-sm sm:text-base leading-tight transition-colors text-gray-900 group-hover:text-[#991b1b]"
                                                         :class="(selectedArtistId === {{ $art->id }} && selectedSlot === slot.value) ? 'text-[#991b1b]' : ''"
                                                         x-text="slot.display">
                                                    </div>
                                                </div>

                                                {{-- Info Harga (dengan Inline Edit) --}}
                                                <div class="mt-1 flex items-center justify-between text-xs">
                                                    <div class="flex-1">
                                                        <template x-if="editingPriceSlotId !== slot.id">
                                                            <div @click.stop="startEditPrice(slot)"
                                                                 class="cursor-pointer text-xs sm:text-sm font-bold text-[#991b1b] flex items-center gap-1 group/price"
                                                                 title="Klik untuk ubah harga slot langsung">
                                                                <span x-text="slot.formatted_price"></span>
                                                                <x-lucide-pencil class="w-3 h-3 text-gray-300 group-hover/price:text-[#991b1b] transition shrink-0" />
                                                            </div>
                                                        </template>

                                                        <template x-if="editingPriceSlotId === slot.id">
                                                            <div class="flex items-center gap-1" @click.stop>
                                                                <input type="number"
                                                                       x-model="tempPriceInput"
                                                                       @keydown.enter="savePrice(slot)"
                                                                       @keydown.escape="cancelEditPrice()"
                                                                       class="w-24 px-2 py-0.5 text-xs rounded border border-[#991b1b] font-mono focus:outline-none">
                                                                <button type="button" @click="savePrice(slot)" class="p-1 rounded bg-[#991b1b] text-white hover:bg-[#7f1d1d] cursor-pointer" title="Simpan Harga">
                                                                    <x-lucide-check class="w-3 h-3" />
                                                                </button>
                                                                <button type="button" @click="cancelEditPrice()" class="p-1 rounded bg-gray-200 text-gray-600 hover:bg-gray-300 cursor-pointer" title="Batal">
                                                                    <x-lucide-x class="w-3 h-3" />
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>

                                                {{-- Tombol / Ikon Aksi Cepat Admin pada Setiap Slot --}}
                                                <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-xs">
                                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Aksi Admin</span>
                                                    <div class="flex items-center gap-1">
                                                        {{-- Ikon Toggle On/Off --}}
                                                        <button type="button"
                                                                @click.stop="toggleSlotStatus(slot, {{ $art->id }})"
                                                                class="p-1.5 rounded-lg transition cursor-pointer"
                                                                :class="slot.status === 'inactive' ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'hover:bg-gray-100 text-gray-400 hover:text-amber-600'"
                                                                :title="slot.status === 'inactive' ? 'Aktifkan Slot Jam Ini' : 'Nonaktifkan Slot Jam Ini'">
                                                            <x-lucide-power class="w-3.5 h-3.5" />
                                                        </button>

                                                        {{-- Ikon Edit Jam --}}
                                                        <button type="button"
                                                                @click.stop="openEditTimeModal(slot, {{ $art->id }})"
                                                                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-blue-600 transition cursor-pointer"
                                                                title="Edit Jam Slot">
                                                            <x-lucide-clock class="w-3.5 h-3.5" />
                                                        </button>

                                                        {{-- Ikon Hapus Slot --}}
                                                        <button type="button"
                                                                @click.stop="deleteSlot(slot, {{ $art->id }})"
                                                                class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-rose-600 transition cursor-pointer"
                                                                title="Hapus Slot Jam">
                                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </template>
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden Inputs for Form Submission --}}
                    <input type="hidden" name="artist_id" id="hiddenArtistId" :value="selectedArtistId">
                    <input type="hidden" name="start_time" id="hiddenStartTime" :value="selectedSlot">
                    <input type="hidden" name="booking_date" id="bookingDateInput" :value="defaultDate">

                    {{-- Step 2 Navigation Buttons --}}
                    <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" @click="goToStep(1)"
                                class="px-5 py-2.5 rounded-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs transition flex items-center gap-2 cursor-pointer">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            <span>Kembali ke Pilih Layanan</span>
                        </button>
                        <button type="button" @click="goToStep(3)"
                                class="px-6 py-3 rounded-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-xs shadow-md shadow-[#991b1b]/20 transition flex items-center gap-2 cursor-pointer">
                            <span>Lanjut ke Desain (Step 3)</span>
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </button>
                    </div>

                </div>

            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════
                 STEP 3: DESAIN KUKU & REQUEST KHUSUS TAMU
                 ═══════════════════════════════════════════════════════════════════════ -->
            <div x-show="currentStep === 3" x-transition.opacity.duration.200ms class="space-y-6">

                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
                    <div class="flex items-center gap-2.5 mb-5 pb-4 border-b border-gray-100">
                        <div class="w-10 h-10 rounded-2xl bg-[#991b1b] flex items-center justify-center text-white shadow-md shadow-[#991b1b]/20 shrink-0">
                            <x-lucide-palette class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Desain Kuku & Catatan Khusus Tamu</h3>
                            <p class="text-xs text-[#49454F]">Lampirkan foto referensi desain dari tamu atau catat preferensi khusus</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Upload Foto Referensi Desain -->
                        <div>
                            <label class="block text-xs font-bold text-[#1C1B1B] mb-2">
                                Foto Referensi Desain Tamu <span class="text-xs font-normal text-gray-400">(Opsional, format JPG/PNG maks 5MB)</span>
                            </label>

                            <div class="border-2 border-dashed border-gray-300 rounded-3xl p-6 text-center hover:border-[#991b1b]/50 transition bg-[#FBF9F8] relative">
                                <template x-if="!photoPreview">
                                    <div class="space-y-2">
                                        <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-[#991b1b] flex items-center justify-center mx-auto shadow-sm">
                                            <x-lucide-image-plus class="w-6 h-6" />
                                        </div>
                                        <div class="text-xs font-bold text-gray-700">Pilih atau Seret Foto Referensi ke Sini</div>
                                        <div class="text-[11px] text-gray-400">Teknisi / Artist dapat melihat referensi motif kuku yang diinginkan tamu</div>
                                        <label class="mt-2 inline-block px-4 py-2 rounded-xl bg-[#991b1b] text-white font-bold text-xs shadow-sm hover:bg-[#7f1d1d] cursor-pointer transition">
                                            <span>Unggah Foto Desain</span>
                                            <input type="file" name="custom_design_photo" accept="image/*" class="hidden" @change="onPhotoSelected($event)">
                                        </label>
                                    </div>
                                </template>

                                <template x-if="photoPreview">
                                    <div class="relative inline-block">
                                        <img :src="photoPreview" class="w-48 h-48 rounded-2xl object-cover border-2 border-[#991b1b] shadow-md mx-auto">
                                        <button type="button" @click="removePhoto()"
                                                class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-lg hover:bg-rose-700 transition cursor-pointer"
                                                title="Hapus foto">
                                            <x-lucide-x class="w-4 h-4" />
                                        </button>
                                        <div class="text-xs text-emerald-700 font-bold mt-2">✓ Foto referensi berhasil dipilih</div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Catatan Khusus Desain / Request Tamu -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold text-[#1C1B1B]">
                                    Catatan Khusus Desain / Request Tamu
                                </label>
                                <span class="text-[11px] text-gray-400">Klik tag cepat di bawah untuk menambahkan</span>
                            </div>

                            <textarea name="design_notes" x-model="designNotes" rows="3"
                                      placeholder="Contoh: Warna nude ombre, bentuk kuku almond, minta aksen glitter halus di jari manis..."
                                      class="w-full px-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79] focus:bg-white transition"></textarea>

                            <!-- Quick Preset Tags -->
                            <div class="mt-2.5 flex flex-wrap gap-1.5">
                                @foreach(['Nude Ombre', 'French Tip Minimalis', 'Glitter Accent', 'Cat Eye Magnet', 'Chrome Powder', 'Kuku Almond', 'Kuku Oval', 'Kuku Square'] as $tag)
                                    <button type="button" @click="appendDesignTag('{{ $tag }}')"
                                            class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-[#F4D7D4]/40 hover:text-[#991b1b] text-[11px] font-semibold text-gray-600 transition cursor-pointer">
                                        + {{ $tag }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Navigation Buttons -->
                    <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-between">
                        <button type="button" @click="goToStep(2)"
                                class="px-5 py-2.5 rounded-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs transition flex items-center gap-2 cursor-pointer">
                            <x-lucide-arrow-left class="w-4 h-4" />
                            <span>Kembali ke Jadwal</span>
                        </button>
                        <button type="button" @click="goToStep(4)"
                                class="px-6 py-3 rounded-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-xs shadow-md shadow-[#991b1b]/20 transition flex items-center gap-2 cursor-pointer">
                            <span>Lanjut ke Pembayaran Kasir (Step 4)</span>
                            <x-lucide-arrow-right class="w-4 h-4" />
                        </button>
                    </div>
                </div>

            </div>

            <!-- ═══════════════════════════════════════════════════════════════════════
                 STEP 4: STATUS OPERASIONAL & PEMBAYARAN KASIR (EXACT IMAGE 2 REPLICA)
                 ═══════════════════════════════════════════════════════════════════════ -->
            <div x-show="currentStep === 4" x-transition.opacity.duration.200ms class="space-y-6">

                <!-- 5. STATUS AWAL & METODE PEMBAYARAN KASIR -->
                <div class="bg-white rounded-3xl p-6 md:p-8 border border-[#DCD9D9]/80 shadow-soft">
                    <div class="flex items-center gap-2.5 mb-5 pb-4 border-b border-gray-100">
                        <span class="w-7 h-7 rounded-full bg-[#F4D7D4] text-[#B76E79] flex items-center justify-center font-bold text-xs">5</span>
                        <div>
                            <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Status Operasional & Pembayaran Kasir</h3>
                            <p class="text-xs text-[#49454F]">Tentukan status pengerjaan dan catat sinkronisasi transaksi kasir</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Pilihan Status Operasional -->
                        <div>
                            <label class="block text-xs font-bold text-[#1C1B1B] mb-2">Status Pesanan</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <label class="status-option flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition"
                                       :class="orderStatus === 'in_progress' ? 'border-[#B76E79] bg-[#F4D7D4]/15 shadow-sm' : 'border-[#DCD9D9]/70 bg-[#FBF9F8]'"
                                       @click="orderStatus = 'in_progress'">
                                    <input type="radio" name="status" value="in_progress" class="mt-1" :checked="orderStatus === 'in_progress'">
                                    <div>
                                        <div class="font-bold text-xs text-[#1C1B1B] flex items-center gap-1.5">
                                            <span class="flex items-center gap-1"><x-lucide-zap class="w-3.5 h-3.5 text-amber-500" /> Sedang Dikerjakan (In-Progress)</span>
                                            <span class="text-[9px] bg-purple-100 text-purple-800 px-1.5 py-0.2 rounded font-bold">Rekomendasi</span>
                                        </div>
                                        <div class="text-[11px] text-[#49454F] mt-0.5">Tamu langsung duduk di meja perawatan saat ini juga.</div>
                                    </div>
                                </label>

                                <label class="status-option flex items-start gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition"
                                       :class="orderStatus === 'confirmed' ? 'border-[#B76E79] bg-[#F4D7D4]/15 shadow-sm' : 'border-[#DCD9D9]/70 bg-[#FBF9F8]'"
                                       @click="orderStatus = 'confirmed'">
                                    <input type="radio" name="status" value="confirmed" class="mt-1" :checked="orderStatus === 'confirmed'">
                                    <div>
                                        <div class="font-bold text-xs text-[#1C1B1B] flex items-center gap-1.5">
                                            <x-lucide-calendar class="w-3.5 h-3.5 text-brand-600" />
                                            <span>Dikonfirmasi (Confirmed)</span>
                                        </div>
                                        <div class="text-[11px] text-[#49454F] mt-0.5">Tamu datang lebih awal / antre untuk sesi berikutnya hari ini.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Metode Pembayaran Kasir (Image 2 Replica) -->
                        <div class="pt-4 border-t border-[#DCD9D9]/50">
                            <label class="block text-xs font-bold text-[#1C1B1B] mb-2">Metode Pembayaran di Kasir</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                @php
                                    $payMethods = [
                                        ['moka_pos', 'Mesin Kasir Moka POS', 'store'],
                                        ['edc', 'Mesin EDC (Debit/Kartu)', 'credit-card'],
                                        ['cash', 'Tunai (Cash)', 'banknote'],
                                        ['qris', 'QRIS Statis/Dinamis', 'qr-code'],
                                        ['transfer', 'Transfer Bank', 'send'],
                                        ['unpaid', 'Bayar Nanti (Selesai)', 'hourglass'],
                                    ];
                                @endphp
                                @foreach($payMethods as [$val, $lbl, $icon])
                                    <label class="pay-method-card flex items-center gap-2.5 p-3 rounded-2xl border cursor-pointer text-xs transition"
                                           :class="paymentMethod === '{{ $val }}' ? 'border-[#B76E79] bg-[#F4D7D4]/25 font-bold text-[#1C1B1B] shadow-sm' : 'border-[#DCD9D9] bg-[#FBF9F8] text-[#49454F]'"
                                           @click="paymentMethod = '{{ $val }}'">
                                        <input type="radio" name="payment_method" value="{{ $val }}" class="hidden" :checked="paymentMethod === '{{ $val }}'">
                                        <x-icon name="{{ $icon }}" class="w-4 h-4 text-[#B76E79] shrink-0" />
                                        <span class="truncate">{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Catatan Kasir / No Ref Struk -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                                    Catatan Kasir / No. Struk Moka POS
                                </label>
                                <input type="text" name="cashier_notes" x-model="cashierNotes"
                                       placeholder="Contoh: INV-MOKA-0921 / Struk EDC #4412"
                                       class="w-full px-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm focus:outline-none focus:border-[#B76E79]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#1C1B1B] mb-1.5">
                                    Catatan Khusus Desain / Request Tamu
                                </label>
                                <input type="text" name="design_notes_step4" :value="designNotes" readonly
                                       placeholder="Contoh: Warna nude ombre, kuku oval"
                                       class="w-full px-4 py-3 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] text-sm text-gray-500 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Step 4 Navigation Buttons -->
                        <div class="mt-8 pt-5 border-t border-gray-100 flex items-center justify-between">
                            <button type="button" @click="goToStep(3)"
                                    class="px-5 py-2.5 rounded-full border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-xs transition flex items-center gap-2 cursor-pointer">
                                <x-lucide-arrow-left class="w-4 h-4" />
                                <span>Kembali ke Desain</span>
                            </button>
                            <button type="submit"
                                    class="px-8 py-3.5 rounded-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-sm shadow-lg shadow-[#991b1b]/30 transition flex items-center gap-2 cursor-pointer">
                                <x-icon name="lock" class="w-4 h-4" />
                                <span>Simpan & Kunci Pesanan Walk-In</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- RIGHT SIDEBAR: ORDER SLIP & SUMMARY (STICKY ACROSS ALL STEPS) -->
        <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-24">

            <div class="bg-white rounded-3xl p-6 border-2 border-[#B76E79]/40 shadow-soft overflow-hidden relative">
                <div class="absolute top-0 right-0 transform translate-x-4 -translate-y-4 w-28 h-28 bg-[#F4D7D4]/30 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Receipt Header -->
                <div class="border-b border-dashed border-[#DCD9D9] pb-4 mb-4 text-center">
                    <div class="w-9 h-9 rounded-full bg-[#B76E79] text-white flex items-center justify-center mx-auto mb-2 shadow-soft">
                        <x-lucide-sparkles class="w-4 h-4" />
                    </div>
                    <h4 class="font-serif text-lg font-bold text-[#1C1B1B]">Ringkasan Pesanan Walk-In</h4>
                    <p class="text-[11px] text-[#49454F] mt-0.5">Nail Glam Studio · Kasir Slip</p>
                    <div class="inline-block mt-2 px-3 py-1 rounded-full bg-[#F4D7D4]/50 text-[#B76E79] text-[10px] font-bold uppercase tracking-wider">
                        <span x-text="branch"></span>
                    </div>
                </div>

                <!-- Customer, Artist & Schedule Info -->
                <div class="space-y-2.5 text-xs border-b border-[#DCD9D9]/60 pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[#49454F]">Pelanggan:</span>
                        <strong class="text-[#1C1B1B] font-serif" x-text="customerName || (customerType === 'guest' ? 'Tamu Walk-In' : 'Member Terdaftar')"></strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#49454F]">Terapis:</span>
                        <strong class="text-[#1C1B1B]" x-text="selectedArtistName"></strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#49454F]">Jadwal:</span>
                        <strong class="text-[#1C1B1B] font-mono" x-text="selectedSlot ? ('Hari ini, ' + selectedSlot + ' s/d ' + (selectedSlotEnd || '--:--') + ' WIB') : 'Belum dipilih'"></strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#49454F]">Total Durasi:</span>
                        <strong class="text-[#B76E79]" x-text="totalDuration + ' Menit'"></strong>
                    </div>
                    <template x-if="designNotes">
                        <div class="flex justify-between items-start gap-2 pt-1 border-t border-gray-100">
                            <span class="text-[#49454F] shrink-0">Catatan:</span>
                            <span class="text-[#1C1B1B] text-right italic text-[11px] line-clamp-2" x-text="designNotes"></span>
                        </div>
                    </template>
                </div>

                <!-- Selected Services List -->
                <div class="mb-4">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-[#49454F] mb-2">Item Layanan:</div>
                    <div class="space-y-1.5 max-h-48 overflow-y-auto text-xs pr-1">
                        <template x-for="item in selectedServicesList" :key="item.id">
                            <div class="flex items-center justify-between py-1 border-b border-[#DCD9D9]/30 text-[11px]">
                                <span class="font-medium text-[#1C1B1B] truncate max-w-[150px]" x-text="item.name"></span>
                                <span class="font-semibold text-[#B76E79]" x-text="item.formatted_price"></span>
                            </div>
                        </template>
                        <template x-if="selectedServicesList.length === 0">
                            <div class="text-[#49454F] italic text-[11px] text-center py-4 bg-[#FBF9F8] rounded-xl">
                                Belum ada layanan yang dipilih
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="pt-4 border-t-2 border-dashed border-[#DCD9D9] mb-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-[#49454F]">Grand Total</div>
                            <div class="text-xs text-[#49454F] font-normal">Termasuk Biaya Perawatan</div>
                        </div>
                        <div class="font-serif text-2xl font-bold text-[#B76E79]" x-text="formattedTotalPrice">
                            Rp 0
                        </div>
                    </div>
                </div>

                <!-- Contextual Dynamic Action Button -->
                <template x-if="currentStep === 1">
                    <button type="button" @click="goToStep(2)"
                            class="w-full py-3.5 px-6 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold text-xs shadow-soft transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Lanjut ke Jadwal (Step 2)</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </button>
                </template>

                <template x-if="currentStep === 2">
                    <button type="button" @click="goToStep(3)"
                            class="w-full py-3.5 px-6 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold text-xs shadow-soft transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Lanjut ke Desain (Step 3)</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </button>
                </template>

                <template x-if="currentStep === 3">
                    <button type="button" @click="goToStep(4)"
                            class="w-full py-3.5 px-6 rounded-full bg-[#B76E79] hover:bg-[#a25a65] text-white font-bold text-xs shadow-soft transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span>Lanjut ke Pembayaran (Step 4)</span>
                        <x-lucide-arrow-right class="w-4 h-4" />
                    </button>
                </template>

                <template x-if="currentStep === 4">
                    <button type="submit"
                            class="w-full py-3.5 px-6 rounded-full bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-xs shadow-soft transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <x-icon name="lock" class="w-4 h-4" />
                        <span>Simpan & Kunci Jadwal Walk-In</span>
                    </button>
                </template>

                <!-- Double Booking Prevention Callout -->
                <div class="mt-4 p-3 rounded-2xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2">
                    <x-icon name="shield-check" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <p class="leading-relaxed">
                        <strong>Anti Double-Booking:</strong> Sistem langsung mengunci slot ini di database (Jam 11:00 – 19:00). Pelanggan online tidak akan bisa memilih slot terapis ini.
                    </p>
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="bg-[#FBF9F8] rounded-3xl p-5 border border-[#DCD9D9]/80 text-xs text-[#49454F] space-y-2">
                <div class="font-bold text-[#1C1B1B] flex items-center gap-1.5">
                    <x-icon name="info" class="w-4 h-4 text-[#B76E79]" /> Alur Kasir 4 Langkah
                </div>
                <ol class="list-decimal list-inside space-y-1 text-[11px] leading-relaxed">
                    <li><strong>Step 1:</strong> Pilih nama tamu & centang layanan.</li>
                    <li><strong>Step 2:</strong> Pilih terapis & jam slot (11:00 – 19:00).</li>
                    <li><strong>Step 3:</strong> Upload referensi desain & preferensi.</li>
                    <li><strong>Step 4:</strong> Pilih status & mesin kasir Moka POS.</li>
                </ol>
            </div>

        </div>
    </form>

    {{-- MODAL TAMBAH SLOT JAM BARU --}}
    <div x-show="isAddModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100"
             @click.outside="isAddModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#991b1b]/10 text-[#991b1b] flex items-center justify-center">
                        <x-lucide-plus-circle class="w-4 h-4" />
                    </div>
                    <h3 class="font-serif font-bold text-base text-gray-900">Tambah Slot Jam Baru</h3>
                </div>
                <button type="button" @click="isAddModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Terapis</label>
                    <input type="text" :value="newSlotArtistName" class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-700 font-semibold" readonly>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jam Mulai <span class="text-[#991b1b]">*</span></label>
                        <input type="time" x-model="newSlotStartTime" min="11:00" max="19:00" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono focus:border-[#991b1b] focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jam Selesai <span class="text-[#991b1b]">*</span></label>
                        <input type="time" x-model="newSlotEndTime" min="11:00" max="19:00" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono focus:border-[#991b1b] focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tarif Slot (Rp)</label>
                    <input type="number" x-model="newSlotPrice" placeholder="220000" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-sm font-mono focus:border-[#991b1b] focus:outline-none">
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-xs cursor-pointer">Batal</button>
                <button type="button" @click="submitAddSlot()" class="px-4 py-2 rounded-xl bg-[#991b1b] hover:bg-[#7f1d1d] text-white font-bold text-xs shadow-sm cursor-pointer">Simpan Slot Baru</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT JAM SLOT --}}
    <div x-show="isEditTimeModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100"
             @click.outside="isEditTimeModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-lucide-clock class="w-4 h-4" />
                    </div>
                    <h3 class="font-serif font-bold text-base text-gray-900">Ubah Jam Slot Waktu</h3>
                </div>
                <button type="button" @click="isEditTimeModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jam Mulai</label>
                        <input type="time" x-model="editStartTime" min="11:00" max="19:00" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Jam Selesai</label>
                        <input type="time" x-model="editEndTime" min="11:00" max="19:00" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm font-mono focus:border-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" @click="isEditTimeModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-xs cursor-pointer">Batal</button>
                <button type="button" @click="submitEditTime()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm cursor-pointer">Simpan Perubahan Jam</button>
            </div>
        </div>
    </div>

    {{-- FLOATING TOAST NOTIFICATION --}}
    <div x-show="toastVisible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-6 right-6 z-50 bg-[#1C1B1B] text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 border border-white/10 text-xs">
        <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-400 shrink-0" />
        <span x-text="toastMessage" class="font-medium"></span>
    </div>

</div>

<!-- JAVASCRIPT & ALPINE WIZARD LOGIC -->
<script>
function walkInWizard(config) {
    return {
        // Step Navigation (1: Layanan, 2: Jadwal, 3: Desain, 4: Pembayaran)
        currentStep: 1,

        // Live Clock
        liveClock: '--:--:--',

        // Branch & Customer
        branch: config.defaultBranch || 'Metland Puri',
        customerType: 'guest',
        customerName: '',
        customerPhone: '',
        customerId: '',

        // Services & Categories
        activeCategory: 'all',
        selectedServiceIds: config.oldServices || [],
        selectedServicesList: [],
        totalDuration: 90,
        totalPrice: 220000,

        // Step 2: Slot & Artist Management (Operational Hours 11:00 - 19:00 WIB)
        defaultDate: config.defaultDate || '',
        selectedArtistId: config.initialArtistId || (config.artistsData[0]?.id || null),
        selectedArtistName: config.artistsData[0]?.name || 'Terapis',
        selectedSlot: '11:00',
        selectedSlotEnd: '12:30',
        expandedArtistId: config.initialArtistId || (config.artistsData[0]?.id || null),
        slotsData: config.slotsData || {},
        artistSummaries: config.artistSummaries || {},
        artistsData: config.artistsData || [],

        // Inline Price Editing
        editingPriceSlotId: null,
        tempPriceInput: 0,

        // Modal Add Slot
        isAddModalOpen: false,
        newSlotArtistId: null,
        newSlotArtistName: '',
        newSlotStartTime: '11:00',
        newSlotEndTime: '12:30',
        newSlotPrice: 220000,

        // Modal Edit Time
        isEditTimeModalOpen: false,
        editingSlotRef: null,
        editSlotArtistId: null,
        editStartTime: '',
        editEndTime: '',

        // Step 3: Design
        designNotes: '',
        photoPreview: null,

        // Step 4: Status & Payment
        orderStatus: 'in_progress',
        paymentMethod: 'moka_pos',
        cashierNotes: '',

        // Toast Feedback
        toastVisible: false,
        toastMessage: '',
        toastTimeout: null,

        init() {
            // Start clock
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Set initial selected artist name
            const a = this.artistsData.find(art => art.id === this.selectedArtistId);
            if (a) this.selectedArtistName = a.name;

            // Recalculate slots with operational hours 11:00 - 19:00
            this.recalculateSlots();
        },

        updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            this.liveClock = `${h}:${m}:${s}`;
        },

        showToast(msg) {
            this.toastMessage = msg;
            this.toastVisible = true;
            clearTimeout(this.toastTimeout);
            this.toastTimeout = setTimeout(() => {
                this.toastVisible = false;
            }, 3000);
            if (window.refreshLucideIcons) window.refreshLucideIcons();
        },

        get selectedServiceCount() {
            return this.selectedServiceIds.length;
        },

        get formattedTotalPrice() {
            return 'Rp ' + Number(this.totalPrice).toLocaleString('id-ID');
        },

        get formattedDurationLabel() {
            const h = Math.floor(this.totalDuration / 60);
            const m = this.totalDuration % 60;
            return (h > 0 ? h + 'j ' : '') + (m > 0 ? m + 'm' : (h > 0 ? '' : '0m'));
        },

        toggleService(id, price, duration, name) {
            const index = this.selectedServiceIds.indexOf(id);
            if (index > -1) {
                this.selectedServiceIds.splice(index, 1);
                this.selectedServicesList = this.selectedServicesList.filter(s => s.id !== id);
            } else {
                this.selectedServiceIds.push(id);
                this.selectedServicesList.push({
                    id: id,
                    name: name,
                    price: price,
                    duration: duration,
                    formatted_price: 'Rp ' + Number(price).toLocaleString('id-ID')
                });
            }

            // Recalculate duration & total price
            let dur = 0;
            let prc = 0;
            this.selectedServicesList.forEach(s => {
                dur += s.duration;
                prc += s.price;
            });

            this.totalDuration = dur > 0 ? dur : 90;
            this.totalPrice = prc > 0 ? prc : 220000;

            // Dynamically recalculate slot start and end times within 11:00 - 19:00
            this.recalculateSlots();
        },

        onMemberSelect(event) {
            const sel = event.target;
            const opt = sel.options[sel.selectedIndex];
            if (opt && opt.value) {
                this.customerId = opt.value;
                this.customerName = opt.dataset.name || '';
                this.customerPhone = opt.dataset.phone || '';
            } else {
                this.customerId = '';
                this.customerName = '';
                this.customerPhone = '';
            }
        },

        // DYNAMIC SLOT RECALCULATION (11:00 - 19:00 WIB)
        recalculateSlots() {
            const dur = this.totalDuration > 0 ? this.totalDuration : 90;
            const price = this.totalPrice > 0 ? this.totalPrice : 220000;
            const formattedPrice = 'Rp ' + Number(price).toLocaleString('id-ID');

            // Jam mulai potensial: 11:00 sampai 17:30
            const startCandidates = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '17:30'];

            this.artistsData.forEach(art => {
                const artistId = art.id;
                const oldSlots = this.slotsData[artistId] || [];
                const newSlots = [];

                startCandidates.forEach(sTime => {
                    const [h, m] = sTime.split(':').map(Number);
                    const startMins = h * 60 + m;
                    const endMins = startMins + dur;

                    // Maksimal jam 19:00 WIB (19 * 60 = 1140 menit)
                    if (endMins > 1140) {
                        return; // Melewati jam tutup salon
                    }

                    const endH = Math.floor(endMins / 60);
                    const endM = endMins % 60;
                    const endTimeStr = String(endH).padStart(2, '0') + ':' + String(endM).padStart(2, '0');

                    const matchOld = oldSlots.find(s => s.value === sTime);
                    let status = 'available';
                    let custName = null;
                    let bCode = null;

                    if (matchOld) {
                        if (matchOld.status === 'booked') {
                            status = 'booked';
                            custName = matchOld.customer_name;
                            bCode = matchOld.booking_code;
                        } else if (matchOld.status === 'inactive') {
                            status = 'inactive';
                        }
                    }

                    const slotPrice = (matchOld && matchOld.price && matchOld.price !== 220000) ? matchOld.price : price;
                    const slotFormattedPrice = (matchOld && matchOld.price && matchOld.price !== 220000) ? matchOld.formatted_price : formattedPrice;

                    newSlots.push({
                        id: 'slot_' + artistId + '_' + sTime.replace(':', ''),
                        value: sTime,
                        start_time: sTime,
                        end_time: endTimeStr,
                        display: `${sTime} – ${endTimeStr}`,
                        price: slotPrice,
                        formatted_price: slotFormattedPrice,
                        status: status,
                        customer_name: custName,
                        booking_code: bCode,
                    });
                });

                this.slotsData[artistId] = newSlots;
            });

            // Update selectedSlotEnd
            if (this.selectedSlot) {
                const [sh, sm] = this.selectedSlot.split(':').map(Number);
                const smins = sh * 60 + sm + dur;
                if (smins <= 1140) {
                    const eh = Math.floor(smins / 60);
                    const em = smins % 60;
                    this.selectedSlotEnd = String(eh).padStart(2, '0') + ':' + String(em).padStart(2, '0');
                } else {
                    const firstAvail = (this.slotsData[this.selectedArtistId] || [])[0];
                    if (firstAvail) {
                        this.selectedSlot = firstAvail.value;
                        this.selectedSlotEnd = firstAvail.end_time;
                    }
                }
            }
        },

        goToStep(targetStep) {
            if (targetStep === 2) {
                if (this.selectedServiceIds.length === 0) {
                    this.showToast('Pilih minimal satu layanan perawatan kuku terlebih dahulu.');
                    return;
                }
                if (this.customerType === 'guest') {
                    if (!this.customerName.trim()) {
                        this.showToast('Ketik nama pelanggan walk-in terlebih dahulu.');
                        return;
                    }
                } else {
                    if (!this.customerId) {
                        this.showToast('Pilih akun member pelanggan terdaftar.');
                        return;
                    }
                }
                this.recalculateSlots();
            }

            if (targetStep === 3) {
                if (!this.selectedArtistId || !this.selectedSlot) {
                    this.showToast('Pilih terapis dan slot jam yang tersedia.');
                    return;
                }
            }

            this.currentStep = targetStep;
            window.scrollTo({ top: 120, behavior: 'smooth' });
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 100);
        },

        resetFlow() {
            if (confirm('Ulangi alur booking walk-in kembali ke Step 1?')) {
                this.currentStep = 1;
                window.scrollTo({ top: 120, behavior: 'smooth' });
            }
        },

        toggleAccordion(artistId) {
            this.expandedArtistId = (this.expandedArtistId === artistId) ? null : artistId;
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 50);
        },

        getArtistSummaryText(artistId, fallbackText) {
            const slots = this.slotsData[artistId] || [];
            if (!slots || slots.length === 0) return fallbackText || 'Tidak Ada Jadwal';
            const total = slots.length;
            const booked = slots.filter(s => s.status === 'booked').length;
            const active = total - slots.filter(s => s.status === 'inactive').length;
            return `${active} Slot Aktif / ${booked} Terisi`;
        },

        selectSlot(artistId, artistName, time, endTime) {
            this.selectedArtistId = artistId;
            this.selectedArtistName = artistName;
            this.selectedSlot = time;
            this.selectedSlotEnd = endTime;

            this.showToast(`Slot jam ${time} (${artistName}) dipilih (Estimasi selesai: ${endTime} WIB).`);
        },

        setNowTime() {
            const now = new Date();
            let h = now.getHours();
            let m = now.getMinutes();

            // Batasi jam operasional salon: 11:00 s/d 19:00
            if (h < 11) {
                h = 11;
                m = 0;
            } else if (h >= 19) {
                h = 17;
                m = 0;
            }

            const nowTime = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            const dur = this.totalDuration > 0 ? this.totalDuration : 90;
            const endMins = (h * 60 + m) + dur;
            const endH = Math.min(19, Math.floor(endMins / 60));
            const endM = endMins % 60;
            const endTime = `${String(endH).padStart(2, '0')}:${String(endM).padStart(2, '0')}`;

            const artistId = this.selectedArtistId || (this.artistsData[0]?.id || null);
            const artist = this.artistsData.find(a => a.id === artistId);
            const artistName = artist ? artist.name : 'Terapis';

            this.selectSlot(artistId, artistName, nowTime, endTime);
            this.showToast(`Jam mulai diatur ke waktu sekarang (${nowTime} WIB).`);
        },

        toggleSlotStatus(slot, artistId) {
            const newStatus = (slot.status === 'inactive') ? 'available' : 'inactive';
            slot.status = newStatus;

            fetch("{{ route('admin.walk-in.toggleSlot') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    artist_id: artistId,
                    slot_id: slot.id,
                    status: newStatus
                })
            }).catch(e => console.log('Toggle slot network log:', e));

            this.showToast(newStatus === 'inactive' ? `Slot ${slot.display} dinonaktifkan.` : `Slot ${slot.display} diaktifkan kembali.`);
        },

        startEditPrice(slot) {
            this.editingPriceSlotId = slot.id;
            this.tempPriceInput = slot.price;
        },

        savePrice(slot) {
            const newPrice = parseInt(this.tempPriceInput) || slot.price;
            slot.price = newPrice;
            slot.formatted_price = 'Rp ' + Number(newPrice).toLocaleString('id-ID');
            this.editingPriceSlotId = null;

            fetch("{{ route('admin.walk-in.updatePrice') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    slot_id: slot.id,
                    price: newPrice
                })
            }).catch(e => console.log('Update price network log:', e));

            this.showToast(`Tarif slot ${slot.display} diubah menjadi ${slot.formatted_price}`);
        },

        cancelEditPrice() {
            this.editingPriceSlotId = null;
        },

        openAddSlotModal(artistId, artistName) {
            this.newSlotArtistId = artistId;
            this.newSlotArtistName = artistName;
            this.newSlotStartTime = '11:00';
            this.newSlotEndTime = '12:30';
            this.newSlotPrice = this.totalPrice || 220000;
            this.isAddModalOpen = true;
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 50);
        },

        submitAddSlot() {
            if (!this.newSlotStartTime || !this.newSlotEndTime) {
                alert('Jam mulai dan jam selesai wajib diisi.');
                return;
            }

            const newSlotObj = {
                id: 'slot_' + this.newSlotArtistId + '_' + this.newSlotStartTime.replace(':', ''),
                value: this.newSlotStartTime,
                start_time: this.newSlotStartTime,
                end_time: this.newSlotEndTime,
                display: `${this.newSlotStartTime} – ${this.newSlotEndTime}`,
                price: parseInt(this.newSlotPrice) || 220000,
                formatted_price: 'Rp ' + Number(parseInt(this.newSlotPrice) || 220000).toLocaleString('id-ID'),
                status: 'available',
                customer_name: null
            };

            if (!this.slotsData[this.newSlotArtistId]) {
                this.slotsData[this.newSlotArtistId] = [];
            }
            this.slotsData[this.newSlotArtistId].push(newSlotObj);
            this.slotsData[this.newSlotArtistId].sort((a, b) => a.start_time.localeCompare(b.start_time));

            fetch("{{ route('admin.walk-in.addSlot') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    artist_id: this.newSlotArtistId,
                    start_time: this.newSlotStartTime,
                    end_time: this.newSlotEndTime,
                    price: this.newSlotPrice
                })
            }).catch(e => console.log('Add slot network log:', e));

            this.isAddModalOpen = false;
            this.showToast(`Slot jam ${newSlotObj.display} berhasil ditambahkan!`);
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 100);
        },

        openEditTimeModal(slot, artistId) {
            this.editingSlotRef = slot;
            this.editSlotArtistId = artistId;
            this.editStartTime = slot.start_time;
            this.editEndTime = slot.end_time;
            this.isEditTimeModalOpen = true;
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 50);
        },

        submitEditTime() {
            if (!this.editStartTime || !this.editEndTime) {
                alert('Jam mulai dan selesai wajib diisi.');
                return;
            }

            if (this.editingSlotRef) {
                this.editingSlotRef.start_time = this.editStartTime;
                this.editingSlotRef.end_time = this.editEndTime;
                this.editingSlotRef.value = this.editStartTime;
                this.editingSlotRef.display = `${this.editStartTime} – ${this.editEndTime}`;
            }

            this.isEditTimeModalOpen = false;
            this.showToast(`Jam slot berhasil diubah menjadi ${this.editStartTime} – ${this.editEndTime}`);
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 100);
        },

        deleteSlot(slot, artistId) {
            if (!confirm(`Hapus slot jam ${slot.display} dari jadwal terapis ini?`)) {
                return;
            }

            if (this.slotsData[artistId]) {
                this.slotsData[artistId] = this.slotsData[artistId].filter(s => s.id !== slot.id);
            }

            fetch("{{ route('admin.walk-in.deleteSlot') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    artist_id: artistId,
                    slot_id: slot.id
                })
            }).catch(e => console.log('Delete slot network log:', e));

            this.showToast(`Slot jam ${slot.display} berhasil dihapus.`);
            setTimeout(() => { if (window.refreshLucideIcons) window.refreshLucideIcons(); }, 100);
        },

        onPhotoSelected(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removePhoto() {
            this.photoPreview = null;
            const input = document.querySelector('input[name="custom_design_photo"]');
            if (input) input.value = '';
        },

        appendDesignTag(tag) {
            if (this.designNotes) {
                this.designNotes += ', ' + tag;
            } else {
                this.designNotes = tag;
            }
        }
    };
}
window.walkInWizard = walkInWizard;
</script>
@endsection
