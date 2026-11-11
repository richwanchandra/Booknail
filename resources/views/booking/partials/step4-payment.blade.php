@php
    // Total harga layanan, diambil dari step pertama atau default 0
    $totalPrice = (int) ($step1['total_price'] ?? 0);
    // Data step2 (desain) dari session, bila ada
    $step2 = session('booking_step2', []);
    // Saldo poin loyalty pengguna, jika terautentikasi
    $balance = Auth::user()?->loyalty_points ?? 0;
@endphp

{{-- ================================
    File: step4-payment.blade.php
    Deskripsi: Tampilan konfirmasi pemesanan dan pembayaran DP.
    Menampilkan ringkasan reservasi, pilihan tukar poin loyalty, dan form upload bukti transfer.
================================ --}}

<div class="space-y-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 p-8 shadow-soft">
        <div class="mb-6">
            <h2 class="font-serif text-3xl font-bold text-[#1C1B1B]">Konfirmasi &amp; Bayar DP</h2>
            <p class="mt-1 text-xs text-[#49454F]">Periksa detail reservasi dan unggah bukti pembayaran DP.</p>
        </div>

        <form action="{{ route('booking.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" id="totalBeforeHidden" value="{{ $totalPrice }}">

            {{-- Loyalty Points Section ---------------------------------------------------}}
            @if($balance > 0)
                <div class="p-6 rounded-2xl bg-[#F6F3F2] border border-[#DCD9D9]/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Tukar Poin Loyalty</h3>
                            <p class="text-xs text-[#49454F]">Saldo kamu: <strong class="text-[#B76E79]">{{ number_format($balance, 0, ',', '.') }} poin</strong> (100 poin = Rp 10.000)</p>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3 items-center">
                        <input id="redeemInput" name="loyalty_points_redeemed" type="number" min="0" max="{{ $balance }}" step="100" value="0"
                               class="px-4 py-2.5 rounded-full border border-[#DCD9D9] text-sm font-semibold focus:outline-none focus:border-[#B76E79]" />
                        <div class="flex gap-2">
                            <button type="button" id="redeemAllBtn" class="btn-secondary !py-2 !px-4 !text-xs">Tukar Semua</button>
                            <button type="button" id="redeemResetBtn" class="btn-secondary !py-2 !px-4 !text-xs">Reset</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-xs pt-1">
                        <div class="p-3 rounded-xl bg-white text-center">
                            <div class="text-[#49454F]">Sisa Saldo</div>
                            <div class="font-bold text-[#1C1B1B] mt-0.5"><span id="balanceDisplay">{{ number_format($balance, 0, ',', '.') }}</span> pts</div>
                        </div>
                        <div class="p-3 rounded-xl bg-white text-center">
                            <div class="text-[#49454F]">Poin Dipakai</div>
                            <div class="font-bold text-[#1C1B1B] mt-0.5"><span id="discountDisplay">0</span> pts</div>
                        </div>
                        <div class="p-3 rounded-xl bg-white text-center">
                            <div class="text-[#49454F]">Diskon</div>
                            <div class="font-bold text-[#B76E79] mt-0.5" id="discountNominal">Rp 0</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Summary Card ------------------------------------------------------------}}
            <div class="p-6 rounded-2xl border border-[#DCD9D9]/80 space-y-4">
                <div class="flex items-center gap-4 border-b border-[#DCD9D9]/60 pb-4">
                    <div class="w-10 h-10 rounded-full bg-[#F4D7D4]/60 flex items-center justify-center text-[#B76E79]">
                        <x-lucide-sparkles class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Detail Reservasi</h3>
                        <p class="text-xs text-[#49454F]">Estimasi durasi perawatan 45 - 90 menit</p>
                    </div>
                </div>
                <div class="space-y-2 text-xs text-[#49454F]">
                    <div class="flex justify-between">
                        <span>Nail Artist</span>
                        <strong class="text-[#1C1B1B]">{{ $step2['artist_name'] ?? 'Sari Dewi' }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Tanggal</span>
                        <strong class="text-[#1C1B1B]">{{ $step2['booking_date'] ?? date('Y-m-d') }}</strong>
                    </div>
                    <div class="flex justify-between border-b border-[#DCD9D9]/40 pb-3">
                        <span>Waktu</span>
                        <strong class="text-[#1C1B1B]">{{ $step2['time_slot'] ?? '10:00 - 10:45 WIB' }}</strong>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span>Total Harga</span>
                        <span class="font-bold text-[#1C1B1B]" id="grandTotalFinal">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F6F3F2] flex justify-between items-center my-2">
                        <span class="font-bold text-[#B76E79] text-sm">DP 30% (bayar sekarang)</span>
                        <span class="font-serif text-xl font-bold text-[#B76E79]" id="dpFinal">Rp {{ number_format(ceil($totalPrice * 0.3), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px]">
                        <span>Sisa bayar saat kunjungan</span>
                        <span class="font-semibold text-[#1C1B1B]">Rp {{ number_format($totalPrice - ceil($totalPrice * 0.3), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Transfer Info Section ---------------------------------------------------}}
            <div class="p-6 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9]/80 space-y-4">
                <div class="text-xs font-bold uppercase tracking-widest text-[#49454F]">INFO TRANSFER BANK</div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span>Bank</span>
                        <strong class="text-[#1C1B1B]">BCA</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>No. Rekening</span>
                        <strong class="text-[#1C1B1B] font-mono text-sm">1234567890</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Atas Nama</span>
                        <strong class="text-[#1C1B1B]">Nail Glam Studio</strong>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-[#DCD9D9]/40">
                        <span>Jumlah Transfer</span>
                        <strong class="text-[#B76E79] text-sm">Rp {{ number_format(ceil($totalPrice * 0.3), 0, ',', '.') }}</strong>
                    </div>
                </div>
                <p class="text-[11px] text-[#49454F] flex items-center gap-1.5">
                    <x-lucide-clock class="w-3.5 h-3.5 text-brand-600 shrink-0" />
                    <span>Selesaikan pembayaran dalam <strong>2 jam</strong>. Slot akan dilepas otomatis jika melewati batas waktu.</span>
                </p>
            </div>

            {{-- Additional Notes Input ---------------------------------------------------}}
            <div>
                <label class="block text-xs font-bold text-[#49454F] mb-1.5">Catatan Tambahan (opsional)</label>
                <textarea name="notes" rows="3" placeholder="Contoh: Ingin bentuk coffin medium, warna pastel blue ombre"
                          class="w-full p-4 rounded-2xl border border-[#DCD9D9] text-xs focus:outline-none focus:border-[#B76E79] bg-white"></textarea>
            </div>

            {{-- Proof File Upload Box ----------------------------------------------------}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-[#49454F]">Upload Bukti Transfer DP *</label>
                    <span class="text-[10px] uppercase font-bold text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">Wajib Diupload</span>
                </div>
                <div class="p-3 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-xs text-amber-900 flex items-start gap-2.5">
                    <x-lucide-alert-triangle class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                    <span class="text-[11px] leading-relaxed"><strong>Perhatian:</strong> Pelanggan wajib mengunggah bukti pembayaran DP. Pesanan <strong>tidak dapat dikonfirmasi booking</strong> oleh admin studio sebelum bukti transfer diunggah.</span>
                </div>
                <div class="relative border-2 border-dashed border-[#DCD9D9] hover:border-[#B76E79] rounded-2xl p-6 text-center bg-white transition cursor-pointer"
                     x-data="{ fileName: '' }">
                    <input type="file" name="dp_proof_file" accept="image/jpeg,image/png,image/webp,application/pdf" required
                           @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                    <div class="w-10 h-10 mx-auto mb-2 rounded-full bg-[#F4D7D4]/40 flex items-center justify-center text-[#B76E79]">
                        <x-lucide-upload-cloud class="w-5 h-5" />
                    </div>
                    <template x-if="fileName">
                        <div>
                            <div class="text-xs font-bold text-emerald-700 flex items-center justify-center gap-1.5">
                                <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                                <span x-text="fileName"></span>
                            </div>
                            <div class="text-[10px] text-gray-500 mt-0.5">Klik lagi jika ingin mengganti file</div>
                        </div>
                    </template>
                    <template x-if="!fileName">
                        <div>
                            <div class="text-xs font-semibold text-[#B76E79]">Pilih atau seret file bukti transfer di sini</div>
                            <div class="text-[10px] text-[#49454F] mt-1">Format JPG, PNG, WEBP, atau PDF (maksimal 3MB)</div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Submit Button -----------------------------------------------------------}}
            <button type="submit" class="w-full btn-primary !py-4 text-base font-bold shadow-soft rounded-full">Konfirmasi Reservasi</button>
            <p class="text-[11px] text-center text-[#49454F]">Dengan menekan tombol ini, kamu setuju dengan syarat &amp; ketentuan Nail Glam Studio.</p>
        </form>
    </div>
</div>
