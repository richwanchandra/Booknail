@php
    use App\Services\SlotAvailabilityService;
    use Carbon\Carbon;

    $slotSvc = app(SlotAvailabilityService::class);
    $duration = (int) ($step1['total_duration'] ?? 60);
    $bookingDate = $step1['booking_date'] ?? now()->addDay()->format('Y-m-d');
    $totalPrice = (int) ($step1['total_price'] ?? 0);

    // Build 7-day date range starting tomorrow
    $dateRange = [];
    for ($i = 1; $i <= 7; $i++) {
        $d = now()->addDays($i);
        $dateRange[] = [
            'value'     => $d->format('Y-m-d'),
            'day_short' => $d->locale('id')->isoFormat('ddd'),
            'day_num'   => $d->format('d'),
            'month'     => $d->locale('id')->isoFormat('MMM'),
            'active'    => $d->format('Y-m-d') === $bookingDate,
        ];
    }

    // Precalculate slots for each artist on this bookingDate
    $artistData = [];
    foreach ($artists as $art) {
        $slots = $slotSvc->getAvailableSlots($art->id, $bookingDate, $duration, 60);
        $slotCards = [];
        foreach ($slots as $s) {
            $startC = Carbon::parse($bookingDate . ' ' . $s['time']);
            $endC = (clone $startC)->addMinutes($duration);
            $slotCards[] = array_merge($s, [
                'end_time' => $endC->format('H:i'),
            ]);
        }

        $initials = strtoupper(substr($art->user->name ?? 'A', 0, 1));
        $parts = explode(' ', $art->user->name ?? '');
        if (count($parts) > 1) {
            $initials .= strtoupper(substr($parts[1], 0, 1));
        }

        $availableSlotsCount = collect($slots)->where('available', true)->count();

        $artistData[] = [
            'model' => $art,
            'id' => $art->id,
            'name' => $art->user->name ?? 'Nail Artist',
            'specialty' => $art->specialty ?? 'All Nail Art',
            'photo' => $art->photo,
            'initials' => $initials,
            'avg_rating' => number_format($art->average_rating ?? 4.8, 1),
            'review_count' => $art->review_count ?? 0,
            'slots' => $slotCards,
            'has_available' => $availableSlotsCount > 0,
            'available_count' => $availableSlotsCount,
        ];
    }

    $firstAvailableArtist = collect($artistData)->firstWhere('has_available', true);
    $defaultExpandedId = $step1['artist_id'] ?? ($firstAvailableArtist['id'] ?? ($artistData[0]['id'] ?? 'null'));
    $selectedArtistInitialName = !empty($step1['artist_id']) ? (\App\Models\NailArtist::find($step1['artist_id'])?->user?->name ?? '') : '';
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════
     STEP 2 – Pilih Slot Jadwal & Terapis (All Artists View - Responsive Mobile)
     Color Palette: Booknail Signature Dusty Rose (#B76E79 & #F4D7D4)
     ═══════════════════════════════════════════════════════════════════════ --}}
<div x-data="{
    selectedArtistId: {{ !empty($step1['artist_id']) ? $step1['artist_id'] : 'null' }},
    selectedSlot: '{{ !empty($step1['start_time']) ? substr($step1['start_time'], 0, 5) : '' }}',
    selectedArtistName: '{{ addslashes($selectedArtistInitialName) }}',
    expandedArtistId: {{ $defaultExpandedId }},
    bookingDate: '{{ $bookingDate }}',
    toggleArtist(id) {
        this.expandedArtistId = (this.expandedArtistId === id) ? null : id;
    },
    selectSlot(artistId, artistName, time) {
        this.selectedArtistId = artistId;
        this.selectedArtistName = artistName;
        this.selectedSlot = time;
    }
}" class="space-y-5">

    {{-- ───────────────────────────── HEADER ───────────────────────────── --}}
    <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 overflow-hidden shadow-soft">

        {{-- Title & Duration Info --}}
        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#B76E79] flex items-center justify-center text-white shadow-md shadow-[#B76E79]/25 shrink-0">
                    <x-lucide-calendar class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h2 class="font-serif text-lg sm:text-2xl font-extrabold text-[#1C1B1B] leading-tight">Pilih Terapis & Slot Jadwal</h2>
                    <p class="text-xs text-[#49454F] mt-0.5 flex items-center flex-wrap gap-1.5">
                        <span>Estimasi durasi: <strong class="text-[#1C1B1B] font-bold">{{ intdiv($duration, 60) ? intdiv($duration, 60) . 'j ' : '' }}{{ $duration % 60 }}m</strong></span>
                        <span class="text-gray-300 hidden xs:inline">·</span>
                        <span>Tanggal: <strong class="text-[#1C1B1B] font-bold">{{ Carbon::parse($bookingDate)->locale('id')->isoFormat('D MMMM Y') }}</strong></span>
                    </p>
                </div>
            </div>
            <div class="self-start sm:self-auto shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F6F3F2] border border-[#DCD9D9] text-[11px] font-semibold text-[#49454F]">
                    <x-lucide-clock class="w-3.5 h-3.5 text-[#B76E79]" />
                    <span>Jam Operasional: <strong class="text-[#1C1B1B]">11:00 – 19:00 WIB</strong></span>
                </span>
            </div>
        </div>

        {{-- ──── Horizontal Date Picker + Quick Filter (Touch & Mobile Friendly) ──── --}}
        <div class="border-t border-[#DCD9D9]/60 bg-[#FBF9F8]">
            <div class="flex items-center gap-2 px-3 sm:px-4 py-2.5 sm:py-3">
                {{-- Scrollable dates with touch momentum --}}
                <div class="flex-1 overflow-x-auto scrollbar-hide py-1">
                    <div class="flex gap-2 min-w-max">
                        @foreach($dateRange as $d)
                            <button
                                type="button"
                                onclick="window.location.href='{{ route('booking.index') }}?date={{ $d['value'] }}'"
                                class="flex flex-col items-center justify-center w-[54px] sm:w-[64px] h-[64px] sm:h-[70px] rounded-2xl text-center transition-all duration-200 shrink-0 select-none
                                    @if($d['active'])
                                        bg-[#B76E79] text-white shadow-md shadow-[#B76E79]/30 scale-105 ring-2 ring-[#B76E79]/25
                                    @else
                                        bg-white text-[#49454F] hover:bg-[#F6F3F2] border border-[#DCD9D9] hover:border-[#B76E79]/50
                                    @endif"
                            >
                                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider {{ $d['active'] ? 'text-white/80' : 'text-[#79747E]' }}">{{ $d['day_short'] }}</span>
                                <span class="text-base sm:text-lg font-extrabold leading-none mt-1">{{ $d['day_num'] }}</span>
                                <span class="text-[9px] sm:text-[10px] font-semibold mt-0.5 {{ $d['active'] ? 'text-white/80' : 'text-[#79747E]' }}">{{ $d['month'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Quick filter manual calendar picker --}}
                <div class="flex items-center shrink-0">
                    <div class="relative group" title="Pilih tanggal manual">
                        <input type="date"
                               value="{{ $bookingDate }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               max="{{ now()->addWeeks(4)->format('Y-m-d') }}"
                               class="w-10 h-10 rounded-2xl border border-[#DCD9D9] bg-white text-transparent cursor-pointer hover:border-[#B76E79] transition opacity-0 absolute inset-0 z-10"
                               onchange="window.location.href='{{ route('booking.index') }}?date=' + this.value">
                        <div class="w-10 h-10 rounded-2xl border border-[#DCD9D9] bg-white flex items-center justify-center text-[#49454F] group-hover:text-[#B76E79] group-hover:border-[#B76E79] shadow-soft transition">
                            <x-lucide-calendar-search class="w-4 h-4" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ───────────────────── DAFTAR SELURUH TERAPIS ───────────────────── --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-[#49454F]">
                Pilih Terapis ({{ count($artistData) }} Tersedia)
            </h3>
            <span class="text-[11px] sm:text-xs text-[#79747E]">Klik kartu untuk membuka slot</span>
        </div>

        @foreach($artistData as $art)
            <div class="rounded-3xl border transition-all duration-300 bg-white overflow-hidden shadow-soft"
                 :class="{
                     'ring-2 ring-[#B76E79] border-[#B76E79] shadow-md': selectedArtistId === {{ $art['id'] }},
                     'border-[#DCD9D9] hover:border-[#B76E79]/50': selectedArtistId !== {{ $art['id'] }}
                 }">

                {{-- Artist Row / Card Header (Responsive on Mobile) --}}
                <div class="p-3.5 sm:p-5 cursor-pointer select-none transition-colors"
                     :class="expandedArtistId === {{ $art['id'] }} ? 'bg-[#FBF9F8]' : 'bg-white hover:bg-[#FBF9F8]/60'"
                     @click="toggleArtist({{ $art['id'] }})">
                    <div class="flex items-start sm:items-center gap-3 sm:gap-4">

                        {{-- Foto Profile aspect-square rounded --}}
                        <div class="shrink-0 relative">
                            @if($art['photo'])
                                <img src="{{ str_starts_with($art['photo'], 'data:') ? $art['photo'] : asset('storage/' . $art['photo']) }}"
                                     alt="{{ $art['name'] }}"
                                     class="w-14 h-14 sm:w-18 sm:h-18 aspect-square rounded-2xl object-cover ring-2 ring-white shadow-soft">
                            @else
                                <div class="w-14 h-14 sm:w-18 sm:h-18 aspect-square rounded-2xl bg-gradient-to-br from-[#B76E79] to-[#d3938d] flex items-center justify-center text-white font-serif font-extrabold text-lg sm:text-2xl ring-2 ring-white shadow-soft">
                                    {{ $art['initials'] }}
                                </div>
                            @endif

                            {{-- Selected badge overlay --}}
                            <div x-show="selectedArtistId === {{ $art['id'] }}"
                                 x-cloak
                                 class="absolute -top-1.5 -right-1.5 w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-[#B76E79] text-white flex items-center justify-center shadow-md ring-2 ring-white">
                                <x-lucide-check class="w-3 h-3 sm:w-3.5 sm:h-3.5 stroke-[3]" />
                            </div>
                        </div>

                        {{-- Detail Terapis --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <div>
                                    <h4 class="font-serif text-base sm:text-lg font-bold text-[#1C1B1B] leading-tight truncate">
                                        {{ $art['name'] }}
                                    </h4>
                                    <p class="text-xs text-[#49454F] mt-0.5 truncate">
                                        Nail Artist · {{ $art['specialty'] }}
                                    </p>
                                </div>

                                {{-- Rating --}}
                                <div class="flex items-center gap-1.5 mt-0.5 sm:mt-0">
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 border border-amber-200/60 text-[11px] font-bold text-amber-700">
                                        <x-lucide-star class="w-3 h-3 text-amber-500 fill-amber-500" />
                                        <span>{{ $art['avg_rating'] }}</span>
                                    </div>
                                    <span class="text-[11px] text-[#79747E]">({{ $art['review_count'] }} ulasan)</span>
                                </div>
                            </div>

                            {{-- Tombol Status Ketersediaan & Selected Tag --}}
                            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                @if($art['has_available'])
                                    <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-xs font-bold bg-[#B76E79] text-white shadow-sm shadow-[#B76E79]/20 transition hover:bg-[#a35460]">
                                        <x-lucide-calendar-check class="w-3.5 h-3.5" />
                                        <span>{{ $art['available_count'] }} Slot Tersedia</span>
                                        <span class="inline-flex transition-transform duration-300" :class="expandedArtistId === {{ $art['id'] }} ? 'rotate-180' : ''">
                                            <x-lucide-chevron-down class="w-3.5 h-3.5 ml-0.5" />
                                        </span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-xs font-semibold bg-[#F6F3F2] text-[#49454F]">
                                        <x-lucide-calendar-x class="w-3.5 h-3.5" />
                                        <span>Tidak Ada Slot / Libur</span>
                                        <span class="inline-flex transition-transform duration-300" :class="expandedArtistId === {{ $art['id'] }} ? 'rotate-180' : ''">
                                            <x-lucide-chevron-down class="w-3.5 h-3.5 ml-0.5" />
                                        </span>
                                    </span>
                                @endif

                                <span x-show="selectedArtistId === {{ $art['id'] }}" x-cloak class="text-[11px] font-bold text-[#B76E79] bg-[#F4D7D4]/40 border border-[#B76E79]/20 px-2.5 py-1 rounded-xl">
                                    ✓ Terapis Terpilih
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ───────────────────── GRID JADWAL SLOT ───────────────────── --}}
                <div x-show="expandedArtistId === {{ $art['id'] }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     x-cloak>
                    <div class="border-t border-[#DCD9D9]/60 px-3 sm:px-6 py-4 sm:py-5 bg-[#FBF9F8]">

                        @if(empty($art['slots']))
                            <div class="text-center py-6 sm:py-8">
                                <div class="w-11 h-11 mx-auto rounded-2xl bg-[#F6F3F2] flex items-center justify-center mb-2 text-[#79747E]">
                                    <x-lucide-calendar-off class="w-5 h-5" />
                                </div>
                                <p class="font-bold text-[#1C1B1B] text-sm">Tidak Ada Jadwal</p>
                                <p class="text-xs text-[#49454F] mt-0.5">Terapis libur atau jadwal belum diatur untuk tanggal ini.</p>
                            </div>
                        @else
                            <div class="mb-3 flex items-center justify-between text-xs">
                                <span class="font-bold text-[#1C1B1B]">Pilih Jam Mulai:</span>
                                <span class="text-[#79747E]">Durasi: {{ $duration }} menit</span>
                            </div>

                            {{-- Grid 2 kolom di mobile, 3 di tablet, 4 di desktop --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                                @foreach($art['slots'] as $s)
                                    @if($s['available'])
                                        {{-- ✅ Slot Tersedia --}}
                                        <button type="button"
                                                @click="selectSlot({{ $art['id'] }}, '{{ addslashes($art['name']) }}', '{{ $s['time'] }}')"
                                                class="group relative block w-full text-left cursor-pointer rounded-2xl border-2 p-2.5 sm:p-3.5 transition-all duration-200 select-none"
                                                :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}')
                                                    ? 'border-[#B76E79] bg-[#F4D7D4]/25 shadow-md shadow-[#B76E79]/15 scale-[1.02]'
                                                    : 'border-[#DCD9D9] bg-white hover:border-[#B76E79]/60 hover:shadow-soft'">

                                            {{-- Top Status badge & Check Indicator --}}
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                                     :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}')
                                                        ? 'border-[#B76E79] bg-[#B76E79]'
                                                        : 'border-[#DCD9D9] bg-white'">
                                                    <svg class="w-2.5 h-2.5 text-white transition"
                                                         :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}') ? 'opacity-100' : 'opacity-0'"
                                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.5 rounded-md">
                                                    Tersedia
                                                </span>
                                            </div>

                                            {{-- Time Range --}}
                                            <div class="font-extrabold text-xs sm:text-base leading-tight transition-colors"
                                                 :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}') ? 'text-[#B76E79]' : 'text-[#1C1B1B] group-hover:text-[#B76E79]'">
                                                {{ $s['time'] }} – {{ $s['end_time'] }}
                                            </div>

                                            {{-- Price in Booknail primary color --}}
                                            <div class="mt-1 text-xs sm:text-sm font-bold text-[#B76E79]">
                                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            </div>
                                        </button>
                                    @else
                                        {{-- 🚫 Slot Terisi (Booked) --}}
                                        <div class="rounded-2xl border border-[#DCD9D9]/80 bg-[#F6F3F2]/60 p-2.5 sm:p-3.5 text-center opacity-60 cursor-not-allowed select-none">
                                            <div class="flex justify-end mb-1">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#79747E] bg-gray-200/80 px-1.5 py-0.5 rounded-md">Terisi</span>
                                            </div>
                                            <div class="font-bold text-xs sm:text-base text-[#79747E] line-through decoration-gray-400">
                                                {{ $s['time'] }} – {{ $s['end_time'] }}
                                            </div>
                                            <div class="mt-1 text-[11px] sm:text-xs font-semibold text-[#79747E] italic">
                                                Booked
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        @endforeach
    </div>

    {{-- ───────────────────── FORM SUBMIT & STATUS BAR ───────────────────── --}}
    <form action="{{ route('booking.selectSlot') }}" method="POST" id="slotForm" class="space-y-4 pt-1">
        @csrf
        <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
        <input type="hidden" name="artist_id" :value="selectedArtistId || ''">
        <input type="hidden" name="start_time" :value="selectedSlot ? selectedSlot + ':00' : ''">

        {{-- Slot Selection Summary Bar --}}
        <div x-show="selectedArtistId && selectedSlot" x-cloak
             class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 sm:px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-soft">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600" />
                </div>
                <div class="text-xs sm:text-sm text-emerald-950">
                    <div>Terapis: <strong class="font-extrabold" x-text="selectedArtistName"></strong></div>
                    <div class="text-xs text-emerald-700 mt-0.5">
                        Jadwal: Jam <strong class="font-bold" x-text="selectedSlot"></strong> WIB · {{ Carbon::parse($bookingDate)->locale('id')->isoFormat('D MMMM Y') }}
                    </div>
                </div>
            </div>
            <div class="text-xs font-bold text-emerald-700 shrink-0 self-end sm:self-auto">
                ✓ Siap diproses
            </div>
        </div>

        <div x-show="!selectedArtistId || !selectedSlot"
             class="rounded-2xl border border-amber-200 bg-amber-50/70 px-4 py-3 flex items-center gap-2.5 text-xs text-amber-900">
            <x-lucide-info class="w-4 h-4 text-amber-600 shrink-0" />
            <span>Pilih salah satu slot jam pada terapis di atas untuk melanjutkan ke step berikutnya.</span>
        </div>

        {{-- Action Buttons (Mobile-first layout) --}}
        <div class="flex flex-col sm:flex-row gap-3 pt-1">
            <button type="submit"
                    :disabled="!selectedArtistId || !selectedSlot"
                    :class="(!selectedArtistId || !selectedSlot) ? 'opacity-50 cursor-not-allowed bg-stone-300 text-stone-500' : 'bg-[#B76E79] hover:bg-[#a35460] text-white shadow-lg shadow-[#B76E79]/25 hover:shadow-xl hover:shadow-[#B76E79]/35 hover:-translate-y-0.5 active:translate-y-0 cursor-pointer'"
                    class="order-1 sm:order-2 flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-bold text-sm sm:text-base transition-all duration-300 min-h-[48px]">
                <span>Simpan & Lanjut ke Step 3 (Desain)</span>
                <x-lucide-arrow-right class="w-5 h-5" />
            </button>

            <a href="{{ route('booking.reset') }}"
               class="order-2 sm:order-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-[#49454F] border border-[#DCD9D9] bg-white hover:bg-[#F6F3F2] transition-all duration-200 min-h-[48px]">
                <x-lucide-rotate-ccw class="w-4 h-4" />
                <span>Pilih Ulang Layanan</span>
            </a>
        </div>
    </form>
</div>

{{-- ═══ Scrollbar hide utility ═══ --}}
<style>
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
