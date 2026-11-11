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
     STEP 2 – Pilih Slot Jadwal & Terapis (All Artists View)
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
}" class="space-y-6">

    {{-- ───────────────────────────── HEADER ───────────────────────────── --}}
    <div class="card !p-0 !shadow-none !border-0 bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">

        {{-- Title --}}
        <div class="px-5 pt-5 pb-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#991b1b] flex items-center justify-center text-white shadow-md shadow-[#991b1b]/20">
                    <x-lucide-calendar class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h2 class="font-serif text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">Pilih Terapis & Slot Jadwal</h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Estimasi durasi: <strong class="text-gray-800">{{ intdiv($duration, 60) ? intdiv($duration, 60) . 'j ' : '' }}{{ $duration % 60 }}m</strong> ·
                        Tanggal: <strong class="text-gray-800">{{ Carbon::parse($bookingDate)->locale('id')->isoFormat('D MMMM Y') }}</strong>
                    </p>
                </div>
            </div>
        </div>

        {{-- ──── Horizontal Date Picker + Quick Filter ──── --}}
        <div class="border-t border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-2 px-4 py-3">
                {{-- Scrollable dates --}}
                <div class="flex-1 overflow-x-auto scrollbar-hide">
                    <div class="flex gap-2 min-w-max py-1">
                        @foreach($dateRange as $d)
                            <button
                                type="button"
                                onclick="window.location.href='{{ route('booking.index') }}?date={{ $d['value'] }}'"
                                class="flex flex-col items-center justify-center w-[64px] h-[70px] rounded-2xl text-center transition-all duration-200 shrink-0 select-none
                                    @if($d['active'])
                                        bg-[#991b1b] text-white shadow-md shadow-[#991b1b]/30 scale-105 ring-2 ring-[#991b1b]/20
                                    @else
                                        bg-white text-gray-700 hover:bg-gray-100 border border-gray-200/80 hover:border-gray-300
                                    @endif"
                            >
                                <span class="text-[10px] font-bold uppercase tracking-wider {{ $d['active'] ? 'text-white/80' : 'text-gray-400' }}">{{ $d['day_short'] }}</span>
                                <span class="text-lg font-extrabold leading-none mt-1">{{ $d['day_num'] }}</span>
                                <span class="text-[10px] font-semibold mt-0.5 {{ $d['active'] ? 'text-white/70' : 'text-gray-400' }}">{{ $d['month'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Quick filter dropdown/input --}}
                <div class="flex items-center gap-1.5 shrink-0 ml-2">
                    <div class="relative group" title="Pilih tanggal manual">
                        <input type="date"
                               value="{{ $bookingDate }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               max="{{ now()->addWeeks(4)->format('Y-m-d') }}"
                               class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-transparent cursor-pointer hover:border-[#991b1b]/40 transition opacity-0 absolute inset-0 z-10"
                               onchange="window.location.href='{{ route('booking.index') }}?date=' + this.value">
                        <div class="w-10 h-10 rounded-xl border border-gray-200 bg-white flex items-center justify-center text-gray-600 group-hover:text-[#991b1b] group-hover:border-[#991b1b]/40 shadow-sm transition">
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
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">
                Pilih Terapis ({{ count($artistData) }} Tersedia)
            </h3>
            <span class="text-xs text-gray-400">Klik kartu untuk membuka slot</span>
        </div>

        @foreach($artistData as $art)
            <div class="rounded-3xl border transition-all duration-300 bg-white overflow-hidden shadow-sm"
                 :class="{
                     'ring-2 ring-[#991b1b] border-[#991b1b] shadow-md': selectedArtistId === {{ $art['id'] }},
                     'border-gray-200 hover:border-gray-300': selectedArtistId !== {{ $art['id'] }}
                 }">

                {{-- Artist Row / Card Header --}}
                <div class="p-4 sm:p-5 cursor-pointer select-none transition-colors"
                     :class="expandedArtistId === {{ $art['id'] }} ? 'bg-gray-50/70' : 'bg-white hover:bg-gray-50/40'"
                     @click="toggleArtist({{ $art['id'] }})">
                    <div class="flex items-center gap-4">

                        {{-- Foto Profile aspect-square rounded --}}
                        <div class="shrink-0 relative">
                            @if($art['photo'])
                                <img src="{{ str_starts_with($art['photo'], 'data:') ? $art['photo'] : asset('storage/' . $art['photo']) }}"
                                     alt="{{ $art['name'] }}"
                                     class="w-16 h-16 sm:w-20 sm:h-20 aspect-square rounded-2xl object-cover ring-2 ring-white shadow-sm">
                            @else
                                <div class="w-16 h-16 sm:w-20 sm:h-20 aspect-square rounded-2xl bg-gradient-to-br from-[#991b1b] to-[#b74545] flex items-center justify-center text-white font-extrabold text-xl sm:text-2xl ring-2 ring-white shadow-sm">
                                    {{ $art['initials'] }}
                                </div>
                            @endif

                            {{-- Selected badge overlay --}}
                            <div x-show="selectedArtistId === {{ $art['id'] }}"
                                 x-cloak
                                 class="absolute -top-1.5 -right-1.5 w-6 h-6 rounded-full bg-[#991b1b] text-white flex items-center justify-center shadow-md ring-2 ring-white">
                                <x-lucide-check class="w-3.5 h-3.5 stroke-[3]" />
                            </div>
                        </div>

                        {{-- Detail Terapis --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <div>
                                    <h4 class="font-serif text-base sm:text-lg font-bold text-gray-900 leading-tight truncate">
                                        {{ $art['name'] }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        Nail Artist · {{ $art['specialty'] }}
                                    </p>
                                </div>

                                {{-- Rating --}}
                                <div class="flex items-center gap-2 mt-1 sm:mt-0">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-100 text-xs font-bold text-amber-700">
                                        <x-lucide-star class="w-3.5 h-3.5 text-amber-500 fill-amber-500" />
                                        <span>{{ $art['avg_rating'] }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">({{ $art['review_count'] }} ulasan)</span>
                                </div>
                            </div>

                            {{-- Tombol Status Ketersediaan --}}
                            <div class="mt-3 flex items-center gap-2">
                                @if($art['has_available'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#991b1b] text-white shadow-sm shadow-[#991b1b]/20">
                                        <x-lucide-calendar-check class="w-3.5 h-3.5" />
                                        <span>{{ $art['available_count'] }} Slot Tersedia</span>
                                        <span class="inline-flex transition-transform duration-300" :class="expandedArtistId === {{ $art['id'] }} ? 'rotate-180' : ''">
                                            <x-lucide-chevron-down class="w-3.5 h-3.5 ml-0.5" />
                                        </span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 text-gray-500">
                                        <x-lucide-calendar-x class="w-3.5 h-3.5" />
                                        <span>Tidak Ada Slot / Libur</span>
                                        <span class="inline-flex transition-transform duration-300" :class="expandedArtistId === {{ $art['id'] }} ? 'rotate-180' : ''">
                                            <x-lucide-chevron-down class="w-3.5 h-3.5 ml-0.5" />
                                        </span>
                                    </span>
                                @endif

                                <span x-show="selectedArtistId === {{ $art['id'] }}" x-cloak class="text-xs font-extrabold text-[#991b1b] bg-[#991b1b]/10 px-2.5 py-1 rounded-lg">
                                    Terapis Terpilih
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
                    <div class="border-t border-gray-100 px-4 sm:px-6 py-5 bg-gray-50/60">

                        @if(empty($art['slots']))
                            <div class="text-center py-8">
                                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-2.5 text-gray-400">
                                    <x-lucide-calendar-off class="w-6 h-6" />
                                </div>
                                <p class="font-bold text-gray-700 text-sm">Tidak Ada Jadwal</p>
                                <p class="text-xs text-gray-500 mt-1">Terapis libur atau jadwal belum diatur untuk tanggal ini.</p>
                            </div>
                        @else
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700">Pilih Jam Mulai:</span>
                                <span class="text-xs text-gray-400">Durasi: {{ $duration }} menit</span>
                            </div>

                            {{-- Grid 4 kolom desktop, 2 kolom HP --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-3">
                                @foreach($art['slots'] as $s)
                                    @if($s['available'])
                                        {{-- ✅ Slot Tersedia --}}
                                        <button type="button"
                                                @click="selectSlot({{ $art['id'] }}, '{{ addslashes($art['name']) }}', '{{ $s['time'] }}')"
                                                class="group relative block w-full text-left cursor-pointer rounded-2xl border-2 p-3 sm:p-3.5 transition-all duration-200 select-none"
                                                :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}')
                                                    ? 'border-[#991b1b] bg-[#991b1b]/5 shadow-md shadow-[#991b1b]/10 scale-[1.02]'
                                                    : 'border-gray-200 bg-white hover:border-[#991b1b]/40 hover:shadow-sm'">

                                            {{-- Top Status badge & Check Indicator --}}
                                            <div class="flex items-center justify-between mb-1.5">
                                                <div class="w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all"
                                                     :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}')
                                                        ? 'border-[#991b1b] bg-[#991b1b]'
                                                        : 'border-gray-300 bg-white'">
                                                    <svg class="w-2.5 h-2.5 text-white transition"
                                                         :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}') ? 'opacity-100' : 'opacity-0'"
                                                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                                                    Tersedia
                                                </span>
                                            </div>

                                            {{-- Time Range --}}
                                            <div class="font-extrabold text-sm sm:text-base leading-tight transition-colors"
                                                 :class="(selectedArtistId === {{ $art['id'] }} && selectedSlot === '{{ $s['time'] }}') ? 'text-[#991b1b]' : 'text-gray-900 group-hover:text-[#991b1b]'">
                                                {{ $s['time'] }} – {{ $s['end_time'] }}
                                            </div>

                                            {{-- Price in maroon --}}
                                            <div class="mt-1.5 text-xs sm:text-sm font-bold text-[#991b1b]">
                                                Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                            </div>
                                        </button>
                                    @else
                                        {{-- 🚫 Slot Terisi (Booked) --}}
                                        <div class="rounded-2xl border-2 border-gray-100 bg-gray-100/80 p-3 sm:p-3.5 text-center opacity-60 cursor-not-allowed select-none">
                                            <div class="flex justify-end mb-1">
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400 bg-gray-200 px-1.5 py-0.5 rounded-md">Terisi</span>
                                            </div>
                                            <div class="font-extrabold text-sm sm:text-base text-gray-400 line-through decoration-gray-300">
                                                {{ $s['time'] }} – {{ $s['end_time'] }}
                                            </div>
                                            <div class="mt-1 text-xs sm:text-sm font-bold text-gray-400 italic">
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
    <form action="{{ route('booking.selectSlot') }}" method="POST" id="slotForm" class="space-y-4 pt-2">
        @csrf
        <input type="hidden" name="booking_date" value="{{ $bookingDate }}">
        <input type="hidden" name="artist_id" :value="selectedArtistId || ''">
        <input type="hidden" name="start_time" :value="selectedSlot ? selectedSlot + ':00' : ''">

        {{-- Slot Selection Summary Bar --}}
        <div x-show="selectedArtistId && selectedSlot" x-cloak
             class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 sm:px-5 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                    <x-lucide-check-circle-2 class="w-5 h-5 text-emerald-600" />
                </div>
                <div class="text-sm text-emerald-950">
                    <div>Terapis: <strong class="font-extrabold" x-text="selectedArtistName"></strong></div>
                    <div class="text-xs text-emerald-700 mt-0.5">
                        Jadwal: Jam <strong class="font-bold" x-text="selectedSlot"></strong> WIB · {{ Carbon::parse($bookingDate)->locale('id')->isoFormat('D MMMM Y') }}
                    </div>
                </div>
            </div>
            <div class="text-xs font-semibold text-emerald-700 shrink-0">
                ✓ Siap diproses
            </div>
        </div>

        <div x-show="!selectedArtistId || !selectedSlot"
             class="rounded-2xl border border-amber-200 bg-amber-50/70 px-4 py-3 flex items-center gap-2.5 text-xs text-amber-800">
            <x-lucide-info class="w-4 h-4 text-amber-600 shrink-0" />
            <span>Pilih salah satu slot jam pada terapis di atas untuk melanjutkan ke step berikutnya.</span>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    :disabled="!selectedArtistId || !selectedSlot"
                    :class="(!selectedArtistId || !selectedSlot) ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-[#991b1b] hover:bg-[#7f1717] shadow-lg shadow-[#991b1b]/20 hover:shadow-xl hover:shadow-[#991b1b]/30 hover:-translate-y-0.5'"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-bold text-white text-base active:translate-y-0 transition-all duration-300">
                <x-lucide-arrow-right class="w-5 h-5" />
                <span>Simpan & Lanjut ke Step 3 (Desain)</span>
            </button>

            <a href="{{ route('booking.reset') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-semibold text-gray-600 border border-gray-200 bg-white hover:bg-gray-50 transition-all duration-200">
                <x-lucide-rotate-ccw class="w-4 h-4" />
                <span>Kembali Step 1</span>
            </a>
        </div>
    </form>
</div>

{{-- ═══ Scrollbar hide utility ═══ --}}
<style>
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
