@php
    // Ambil data langkah 2 dari sesi, bila ada
    $step2 = session('booking_step2', []);
@endphp

{{-- ================================
    File: step3-design.blade.php
    Deskripsi: Formulir unggah referensi desain nail art (opsional).
    Menyimpan foto desain dan catatan tambahan untuk dipilih terapis.
================================ --}}

<div class="card">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-serif text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                <x-lucide-camera class="w-6 h-6 text-brand-600" />
                <span>Step 3: Referensi Desain (Opsional)</span>
            </h2>
            <p class="mt-1 text-sm text-gray-500">Upload foto referensi desain nail art favoritmu &amp; tulis catatan untuk artist.</p>
        </div>
    </div>

    <form action="{{ route('booking.uploadDesign') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Foto Desain (JPG/PNG/PDF, max 2MB)</label>
            <div class="border-2 border-dashed border-brand-200 rounded-2xl p-6 bg-brand-50/30 hover:bg-brand-50 transition cursor-pointer"
                 onclick="document.getElementById('designPhotoInput').click()">
                <input type="file" id="designPhotoInput" name="design_photo"
                       accept="image/jpeg,image/png,application/pdf" class="hidden"
                       onchange="previewDesign(this)" />
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-500 shadow-soft">
                        <x-lucide-image class="w-6 h-6" />
                    </div>
                    <div class="font-bold text-gray-700">Klik untuk upload atau drag file kesini</div>
                    <div class="text-xs text-gray-500 mt-1">JPG, PNG, atau PDF · Maks 2MB</div>
                </div>
                <div id="designPreview" class="mt-4 {{ !empty($step2['design_photo_base64']) ? '' : 'hidden' }}">
                    <div class="bg-white rounded-xl p-3 inline-flex items-center gap-3 shadow-sm border border-gray-100">
                        <img id="designPreviewImg" src="{{ $step2['design_photo_base64'] ?? '' }}" alt="Preview Desain" class="w-12 h-12 rounded-lg object-cover border border-gray-200 {{ !empty($step2['design_photo_base64']) ? '' : 'hidden' }}">
                        <span id="designFileName" class="text-sm font-bold text-brand-700">{{ !empty($step2['design_photo_base64']) ? 'Foto desain tersimpan (Base64)' : '' }}</span>
                    </div>
                </div>
            </div>
            @if(!empty($step2['design_photo_base64']))
                <div class="mt-3 text-xs text-emerald-700 font-semibold inline-flex items-center gap-1.5">
                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                    <span>Foto referensi desain tersimpan (Base64). Siap diproses ke Step 4.</span>
                </div>
            @elseif(!empty($step2['design_photo_path']) && Storage::disk('public')->exists($step2['design_photo_path']))
                <div class="mt-3 text-xs text-emerald-700 font-semibold inline-flex items-center gap-1.5">
                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600" />
                    <span>Sudah upload: {{ basename($step2['design_photo_path']) }}</span>
                </div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Desain (opsional, maks 2000 karakter)</label>
            <textarea name="design_notes" rows="5" maxlength="2000"
                      class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition text-sm bg-gray-50/50 resize-y"
                      placeholder="Contoh: warna pink gradasi + glitter silver, desain 3D kuku telunjuk, tambah stiker bunga mawar, ...">{{ old('design_notes', $step2['design_notes'] ?? '') }}</textarea>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-primary !py-3 px-6 justify-center">Simpan &amp; Lanjut ke Step 4 (Pembayaran) →</button>
            <form action="{{ route('booking.storeStep1') }}" method="POST" onsubmit="event.preventDefault(); window.location='{{ route('booking.index') }}#step2'">
                <a href="{{ route('booking.index') }}" class="btn-secondary !py-3 px-6 justify-center inline-flex">← Kembali Step 2</a>
            </form>
        </div>
    </form>
</div>

<script>
function previewDesign(input) {
    const preview = document.getElementById('designPreview');
    const nameEl = document.getElementById('designFileName');
    const imgEl = document.getElementById('designPreviewImg');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        nameEl.textContent = file.name + ' (' + Math.round(file.size/1024) + ' KB)';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgEl.src = e.target.result;
                imgEl.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imgEl.classList.add('hidden');
        }
        preview.classList.remove('hidden');
    }
}
</script>