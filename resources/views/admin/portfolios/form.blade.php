@extends('layouts.admin')

@section('title', $portfolio ? 'Edit Portofolio' : 'Tambah Portofolio')
@section('pageTitle', $portfolio ? 'Edit Foto Portofolio: '.$portfolio->title : 'Tambah Foto Portofolio Baru')
@section('pageSubtitle', 'Upload karya nail art terbaru untuk memperkaya katalog portofolio studio')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-3xl border border-[#DCD9D9]/80 shadow-sm p-6 sm:p-8">
        <form action="{{ $portfolio ? route('admin.portfolios.update', $portfolio) : route('admin.portfolios.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @if($portfolio)
                @method('PUT')
            @endif

            {{-- 1. IMAGE UPLOAD SECTION --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                    Foto Portofolio Nail Art <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    {{-- Preview Container --}}
                    <div class="relative aspect-square w-full rounded-2xl border-2 border-dashed border-[#DCD9D9] bg-[#FBF9F8] overflow-hidden flex flex-col items-center justify-center text-center p-4 group">
                        <img id="imagePreview" 
                             src="{{ $portfolio ? $portfolio->image_url : '' }}" 
                             alt="Preview" 
                             class="absolute inset-0 w-full h-full object-cover {{ $portfolio ? '' : 'hidden' }}">

                        <div id="uploadPlaceholder" class="{{ $portfolio ? 'hidden' : 'flex' }} flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/60 text-[#B76E79] flex items-center justify-center mb-2">
                                <x-icon name="image" class="w-6 h-6" />
                            </div>
                            <span class="text-xs font-bold text-[#1C1B1B]">Belum ada gambar</span>
                            <span class="text-[11px] text-[#49454F] mt-0.5">Pilih file atau seret ke sini</span>
                        </div>

                        {{-- Floating badge if existing --}}
                        <div id="imageBadge" class="{{ $portfolio ? 'block' : 'hidden' }} absolute bottom-3 left-3 right-3 bg-black/60 backdrop-blur-md text-white text-[10px] py-1 px-2 rounded-lg text-center font-medium">
                            Klik atau pilih file baru untuk mengganti
                        </div>
                    </div>

                    {{-- Upload Inputs --}}
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#49454F] mb-1.5">Pilih File dari Perangkat</label>
                            <input type="file" 
                                   name="portfolio_image" 
                                   id="portfolioImageInput"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   onchange="previewSelectedImage(this)"
                                   class="block w-full text-xs text-[#49454F] file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#F4D7D4]/60 file:text-[#B76E79] hover:file:bg-[#F4D7D4] cursor-pointer border border-[#DCD9D9] rounded-2xl bg-[#FBF9F8] p-2">
                            <p class="text-[11px] text-[#49454F] mt-1.5">Format: JPG, PNG, WEBP. Ukuran maksimal 5MB. Disarankan rasio 1:1 (persegi).</p>
                        </div>

                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-[#DCD9D9]"></div>
                            <span class="flex-shrink mx-3 text-[11px] font-bold text-[#49454F] uppercase">atau gunakan URL Gambar</span>
                            <div class="flex-grow border-t border-[#DCD9D9]"></div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#49454F] mb-1.5">Tautan URL Gambar (Opsional)</label>
                            <input type="url" 
                                   name="image_url" 
                                   id="imageUrlInput"
                                   value="{{ old('image_url', (!$portfolio || str_starts_with($portfolio->image ?? '', 'data:') ? '' : $portfolio->image)) }}" 
                                   placeholder="https://example.com/foto-nail-art.jpg"
                                   oninput="previewFromUrl(this.value)"
                                   class="w-full px-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8]">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. TITLE & TAGS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Judul Desain Portofolio <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $portfolio?->title) }}" 
                           required 
                           maxlength="200" 
                           placeholder="Contoh: Rose Gold Chrome French Tip"
                           class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-semibold focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Tag & Gaya Desain (Pisahkan dengan koma)
                    </label>
                    <input type="text" 
                           name="tags" 
                           value="{{ old('tags', $portfolio?->tags) }}" 
                           placeholder="Contoh: French Tip, Glitter, Rose Gold, Minimalist"
                           class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8]">
                    <p class="text-[11px] text-[#49454F] mt-1">Membantu filter pencarian kustom untuk klien studio.</p>
                </div>
            </div>

            {{-- 3. CATEGORY, ARTIST, & SERVICE --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Kategori Layanan
                    </label>
                    <select name="category_id" 
                            class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-semibold focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $portfolio?->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Nail Artist Pembuat
                    </label>
                    <select name="nail_artist_id" 
                            class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-semibold focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">-- Pilih Nail Artist --</option>
                        @foreach($artists as $art)
                            <option value="{{ $art->id }}" {{ old('nail_artist_id', $portfolio?->nail_artist_id) == $art->id ? 'selected' : '' }}>
                                {{ $art->user?->name ?? 'Artist #'.$art->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Layanan Terkait (Opsional)
                    </label>
                    <select name="service_id" 
                            class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-semibold focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">-- Hubungkan Layanan --</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}" {{ old('service_id', $portfolio?->service_id) == $svc->id ? 'selected' : '' }}>
                                {{ $svc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 4. DESCRIPTION & SORT ORDER --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Deskripsi Cerita / Detail Karya
                    </label>
                    <textarea name="description" 
                              rows="3" 
                              maxlength="2000" 
                              placeholder="Ceritakan bahan yang digunakan, waktu pengerjaan, atau keunikan desain kuku ini..."
                              class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8] resize-y">{{ old('description', $portfolio?->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1C1B1B] mb-2">
                        Nomor Urutan
                    </label>
                    <input type="number" 
                           name="sort_order" 
                           value="{{ old('sort_order', $portfolio?->sort_order ?? 0) }}" 
                           min="0" 
                           class="w-full px-4 py-3 rounded-2xl border border-[#DCD9D9] text-sm font-semibold focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8]">
                    <p class="text-[11px] text-[#49454F] mt-1">Angka lebih kecil tampil lebih awal di galeri.</p>
                </div>
            </div>

            {{-- 5. STATUS TOGGLE --}}
            <div class="pt-2">
                <label class="flex items-center gap-3.5 p-4 rounded-2xl bg-[#FBF9F8] border border-[#DCD9D9] cursor-pointer hover:bg-[#F6F3F2] transition">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1" 
                           {{ old('is_active', $portfolio?->is_active ?? true) ? 'checked' : '' }}
                           class="w-5 h-5 rounded text-[#B76E79] focus:ring-[#B76E79] border-gray-300">
                    <div>
                        <div class="font-bold text-sm text-[#1C1B1B]">Publikasikan ke Galeri Website</div>
                        <div class="text-xs text-[#49454F] mt-0.5">Jika dicentang, foto ini akan langsung dapat dilihat oleh calon pelanggan di halaman galeri portofolio.</div>
                    </div>
                </label>
            </div>

            {{-- 6. ACTION BUTTONS --}}
            <div class="pt-4 border-t border-[#DCD9D9] flex items-center gap-4">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-[#B76E79] hover:bg-[#a65d68] text-white text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>{{ $portfolio ? 'Simpan Perubahan Portofolio' : 'Tambahkan ke Portofolio' }}</span>
                </button>
                <a href="{{ route('admin.portfolios.index') }}" 
                   class="px-6 py-3 rounded-full border border-[#DCD9D9] text-xs font-semibold text-[#49454F] hover:bg-[#F6F3F2] transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function previewSelectedImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('imagePreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                const badge = document.getElementById('imageBadge');

                img.src = e.target.result;
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
                badge.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewFromUrl(url) {
        if (url && url.trim().length > 5) {
            const img = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            const badge = document.getElementById('imageBadge');

            img.src = url;
            img.onload = function() {
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
                badge.classList.remove('hidden');
            };
        }
    }
</script>
@endsection
