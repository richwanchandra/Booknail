@extends('layouts.admin')
@section('title', $service ? 'Edit Layanan' : 'Tambah Layanan')
@section('pageTitle', $service ? 'Edit: '.$service->name : 'Tambah Layanan Baru')
@section('pageSubtitle', 'Atur nama, kategori, harga, dan durasi layanan')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-3xl">
    <form action="{{ $service ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($service) @method('PUT') @endif
        <div class="grid sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Layanan *</label>
                <input type="text" name="name" required maxlength="150" value="{{ old('name', $service?->name) }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none font-semibold bg-gray-50/50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Kategori *</label>
                <select name="category_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none font-semibold bg-gray-50/50">
                    <option value="">[Pilih Kategori]</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $service?->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Harga (Rp) *</label>
                <input type="number" name="price" required min="0" step="1000" value="{{ old('price', $service?->price) }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none font-semibold bg-gray-50/50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Durasi (menit) *</label>
                <input type="number" name="duration_minutes" required min="10" max="720" step="5" value="{{ old('duration_minutes', $service?->duration_minutes) }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none font-semibold bg-gray-50/50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Foto Layanan (Format Base64)</label>
                <input type="file" id="service_image_input" name="service_image" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-sm file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-brand-500 file:text-white file:font-bold hover:file:bg-brand-600 cursor-pointer rounded-2xl border-2 border-gray-100 bg-white focus:outline-none p-1">
                <p class="text-[11px] text-gray-400 mt-1">Gambar otomatis dikonversi menjadi data Base64 tanpa menyimpan file PNG/JPG di server storage.</p>

                @php
                    $existingImage = $service?->image ?? $service?->service_image;
                    $existingUrl = null;
                    if ($existingImage) {
                        $existingUrl = str_starts_with($existingImage, 'data:image') 
                            ? $existingImage 
                            : (Storage::disk('public')->exists($existingImage) ? Storage::disk('public')->url($existingImage) : $existingImage);
                    }
                @endphp

                <div id="preview_container" class="mt-2.5 flex items-center gap-3 {{ $existingUrl ? '' : 'hidden' }}">
                    <img id="service_image_preview" src="{{ $existingUrl ?? '' }}" alt="Preview Foto" class="w-14 h-14 rounded-xl object-cover border border-gray-200 shadow-sm bg-gray-50">
                    <div>
                        <div id="preview_status" class="text-xs font-bold text-emerald-600">
                            {{ $existingUrl ? (str_starts_with($existingImage ?? '', 'data:image') ? 'Foto Base64 Tersimpan' : 'Foto Tersimpan') : 'Preview Gambar' }}
                        </div>
                        <div class="text-[11px] text-gray-400">Siap disimpan ke database</div>
                    </div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="3" maxlength="2000" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none bg-gray-50/50 resize-y">{{ old('description', $service?->description) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center gap-3 p-3 rounded-2xl bg-brand-50/50 border-2 border-brand-100 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service?->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-brand-600 focus:ring-brand-500 border-gray-300">
                    <div>
                        <div class="font-bold text-gray-900">Tampilkan di Frontend</div>
                        <div class="text-xs text-gray-500 mt-0.5">Nonaktifkan untuk hidden sementara tanpa hapus data & riwayat.</div>
                    </div>
                </label>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-6 mt-2">
            <button type="submit" class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2">
                <x-lucide-save class="w-4 h-4" />
                <span>{{ $service ? 'Simpan Perubahan' : 'Buat Layanan' }}</span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('service_image_input');
        const preview = document.getElementById('service_image_preview');
        const previewContainer = document.getElementById('preview_container');
        const previewStatus = document.getElementById('preview_status');

        if (input) {
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        preview.src = event.target.result;
                        previewContainer.classList.remove('hidden');
                        if (previewStatus) {
                            previewStatus.textContent = 'Preview Base64 Terbaca (Ukuran: ' + Math.round(file.size / 1024) + ' KB)';
                            previewStatus.className = 'text-xs font-bold text-brand-600';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection
