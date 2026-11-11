@php
    // Flag untuk menampilkan tombol submit; dapat di‑override dari view lain
    $showButton = $showButton ?? false;
@endphp

{{-- ================================
    File: summary-step1.blade.php
    Deskripsi: Ringkasan pemilihan layanan pada langkah pertama booking.
    Menampilkan daftar layanan yang dipilih, total harga, estimasi waktu, serta tombol konfirmasi bila di‑aktifkan.
================================ --}}
<div class="bg-white rounded-3xl border border-[#DCD9D9]/80 p-6 shadow-soft space-y-5">
    {{-- Header Ringkasan -------------------------------------------------------}}
    <div class="border-b border-[#DCD9D9]/60 pb-4 flex items-center justify-between">
        <div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-[#49454F]">Ringkasan Reservasi</div>
            <div class="font-serif text-xl font-bold text-[#1C1B1B] mt-0.5">Langkah {{ $currentStep ?? 1 }} dari 4</div>
        </div>
        <div class="w-9 h-9 rounded-full bg-[#F4D7D4]/60 flex items-center justify-center text-[#B76E79]">
            <x-lucide-sparkles class="w-4 h-4" />
        </div>
    </div>

    {{-- Daftar layanan yang belum dipilih (placeholder) ------------------------}}
    <div id="summaryServices" class="space-y-3 pb-4 border-b border-[#DCD9D9]/60">
        <div class="text-center py-6 text-[#49454F] text-xs">
            <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-500">
                <x-lucide-sparkles class="w-5 h-5" />
            </div>
            Belum ada layanan dipilih
        </div>
    </div>

    {{-- Total harga layanan & estimasi waktu ------------------------------------}}
    <div class="space-y-2 text-xs">
        <div class="flex justify-between items-center text-[#49454F]">
            <span>Total Harga Layanan</span>
            <span id="subtotalAmount" class="font-semibold text-[#1C1B1B]">Rp 0</span>
        </div>
        <div class="flex justify-between items-center text-[#49454F]">
            <span>Total Estimasi Waktu</span>
            <span id="totalDuration" class="font-semibold text-[#1C1B1B]">0 mnt</span>
        </div>
    </div>

    {{-- Grand total & DP --------------------------------------------------------}}
    <div class="p-4 rounded-2xl bg-[#F6F3F2] space-y-2">
        <div class="flex justify-between items-baseline">
            <span class="text-xs font-semibold text-[#49454F]">Total Keseluruhan</span>
            <span id="grandTotal" class="font-serif text-xl font-bold text-[#1C1B1B]">Rp 0</span>
        </div>
        <div class="pt-2 border-t border-[#DCD9D9]/60 flex justify-between items-center">
            <span class="text-xs font-bold text-[#B76E79]">DP 30% (bayar sekarang)</span>
            <span id="dpAmount" class="font-serif text-lg font-bold text-[#B76E79]">Rp 0</span>
        </div>
    </div>

    {{-- Tombol aksi (submit / login) -------------------------------------------}}
    @if($showButton)
        <div class="pt-2 space-y-3">
            @auth
                <button type="submit" form="step1Form" class="w-full btn-primary !py-3.5 text-sm font-bold shadow-soft">Konfirmasi &amp; Lanjut →</button>
            @else
                <a href="{{ route('login') }}" class="w-full btn-primary !py-3.5 text-sm font-bold shadow-soft justify-center inline-flex items-center gap-2">
                    <x-lucide-lock class="w-4 h-4" />
                    <span>Masuk untuk Reservasi →</span>
                </a>
                <div class="text-center">
                    <a href="{{ route('register') }}" class="text-xs text-[#B76E79] font-bold hover:underline">Belum punya akun? Daftar gratis</a>
                </div>
            @endauth
        </div>
    @endif

    {{-- Fitur tambahan (ikon penjelas) ----------------------------------------}}
    <div class="pt-2 grid grid-cols-3 gap-2 text-center text-[10px] text-[#49454F] font-semibold">
        <div class="p-2.5 rounded-xl bg-[#FBF9F8] border border-[#DCD9D9]/60 flex flex-col items-center justify-center gap-1">
            <x-lucide-shield-check class="w-3.5 h-3.5 text-emerald-600" />
            <span>100% Higienis</span>
        </div>
        <div class="p-2.5 rounded-xl bg-[#FBF9F8] border border-[#DCD9D9]/60 flex flex-col items-center justify-center gap-1">
            <x-lucide-clock class="w-3.5 h-3.5 text-brand-600" />
            <span>Garansi Slot</span>
        </div>
        <div class="p-2.5 rounded-xl bg-[#FBF9F8] border border-[#DCD9D9]/60 flex flex-col items-center justify-center gap-1">
            <x-lucide-message-square class="w-3.5 h-3.5 text-[#B76E79]" />
            <span>WA Reminders</span>
        </div>
    </div>
</div>