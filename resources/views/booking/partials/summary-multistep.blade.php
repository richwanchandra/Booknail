@php
    // Hitung total harga layanan dari step pertama, default 0
    $totalPrice = (int) ($step1['total_price'] ?? 0);
    // Total poin loyalty yang dipakai pada step 3 (jika ada)
    $redeemed = (int) (session('booking_step3')['loyalty_points_used'] ?? 0);
    // Diskon berdasarkan poin (100 poin = Rp 10.000)
    $discount = intdiv($redeemed, 100) * 10000;
    // Total akhir setelah diskon, tidak boleh negatif
    $finalTotal = max(0, $totalPrice - $discount);
    // DP 30% dari total akhir
    $dp = (int) ceil($finalTotal * 0.3);
    // Nama terapis yang dipilih (jika ada)
    $artistName = !empty($step1['artist_id'])
        ? (\App\Models\NailArtist::find($step1['artist_id'])?->user?->name ?? 'Artist tidak ditemukan')
        : 'Belum dipilih';
    // Tanggal booking yang diformat ke bahasa Indonesia
    $bookingDate = !empty($step1['booking_date'])
        ? \Carbon\Carbon::parse($step1['booking_date'])->locale('id')->isoFormat('D MMMM Y')
        : 'Belum dipilih';
    // Waktu slot yang dipilih, format jam - jam WIB
    $timeText = (!empty($step1['booking_date']) && !empty($step1['start_time']))
        ? (\Carbon\Carbon::parse($step1['booking_date'] . ' ' . $step1['start_time'])->format('H:i')
            . ' - '
            . \Carbon\Carbon::parse($step1['booking_date'] . ' ' . $step1['start_time'])->addMinutes($step1['total_duration'] ?? 0)->format('H:i') . ' WIB')
        : 'Belum dipilih';
@endphp
{{-- ================================
    File: summary-multistep.blade.php
    Deskripsi: Ringkasan multi‑step booking, menampilkan detail terapis, tanggal, waktu, layanan, subtotal, diskon loyalty, total akhir, dan DP.
    Digunakan pada halaman konfirmasi sebelum pembayaran.
================================ --}}
<div class="card !border-brand-200 !ring-2 !ring-brand-100 overflow-hidden">
    <div class="gradient-brand !p-0 -m-5 mb-5">
        <div class="px-5 py-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-white/80">Detail Booking</div>
                    <div class="font-serif text-xl font-extrabold mt-1">Step {{ $currentStep }} dari 4</div>
                </div>
                <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center text-white">
                    <x-lucide-sparkles class="w-5 h-5" />
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4 text-sm pb-4 border-b border-gray-100">
        {{-- Baris informasi terapis, tanggal, dan jam --}}
        <div class="flex justify-between">
            <span class="text-gray-500 flex items-center gap-1.5"><x-lucide-users class="w-4 h-4 text-brand-600" /> Nail Artist</span>
            <span class="font-bold text-gray-800">{{ $artistName }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500 flex items-center gap-1.5"><x-lucide-calendar class="w-4 h-4 text-brand-600" /> Tanggal</span>
            <span class="font-bold text-gray-800">{{ $bookingDate }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500 flex items-center gap-1.5"><x-lucide-clock class="w-4 h-4 text-brand-600" /> Jam</span>
            <span class="font-bold text-gray-800">{{ $timeText }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500 flex items-center gap-1.5"><x-lucide-clock class="w-4 h-4 text-brand-600" /> Durasi</span>
            <span class="font-bold text-gray-800">
                {{ intdiv($step1['total_duration']??0, 60) ? intdiv($step1['total_duration']??0, 60).'j ' : '' }}{{ ($step1['total_duration']??0) % 60 }}m
            </span>
        </div>
    </div>

    <div class="py-4 space-y-2 max-h-52 overflow-y-auto border-b border-gray-100">
        {{-- Daftar layanan yang dipilih pada step 1 --}}
        @foreach($step1['services'] ?? [] as $svc)
            <div class="flex items-start gap-3 p-2.5 rounded-xl bg-gray-50">
                <div class="w-7 h-7 rounded-md gradient-brand text-white flex items-center justify-center shrink-0">
                    <x-lucide-check class="w-3.5 h-3.5" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-gray-900 text-xs leading-tight">{{ $svc['name'] }}</div>
                    <div class="text-[10px] text-gray-500 mt-0.5">
                        {{ intdiv($svc['duration_minutes'], 60) ? intdiv($svc['duration_minutes'],60).'j ' : '' }}{{ $svc['duration_minutes']%60 }}m
                    </div>
                </div>
                <div class="font-bold text-brand-700 text-xs shrink-0">Rp {{ number_format($svc['price'], 0, ',', '.') }}</div>
            </div>
        @endforeach
    </div>

    <div class="py-4 text-sm space-y-2 border-b border-gray-100">
        {{-- Subtotal layanan dan diskon loyalty (jika ada) --}}
        <div class="flex justify-between items-center text-[#49454F]">
            <span>Subtotal Layanan</span>
            <span class="font-bold text-gray-900">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
        </div>
        @if($discount > 0)
            <div class="flex justify-between text-emerald-700">
                <span class="flex items-center gap-1.5">
                    <x-lucide-gift class="w-4 h-4 text-emerald-600" />
                    <span>Diskon Loyalty ({{ $redeemed }} poin)</span>
                </span>
                <span class="font-bold">- Rp {{ number_format($discount, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="pt-4">
        {{-- Total akhir dan DP 30% --}}
        <div class="flex justify-between items-baseline">
            <span class="text-gray-700 font-bold text-base">Total Akhir</span>
            <span class="font-serif text-2xl font-extrabold text-gradient">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between items-center mt-2 pt-3 border-t border-dashed border-gray-200">
            <span class="text-xs text-gray-500 flex items-center gap-1.5">
                <x-lucide-credit-card class="w-3.5 h-3.5 text-brand-600" />
                <span>DP Minimum (30%)</span>
            </span>
            <span class="font-extrabold text-amber-600">Rp {{ number_format($dp, 0, ',', '.') }}</span>
        </div>
    </div>
</div>