@extends('layouts.admin')
@section('title', $category ? 'Edit Kategori' : 'Tambah Kategori')
@section('pageTitle', $category ? 'Edit Kategori: '.$category->name : 'Tambah Kategori Baru')
@section('pageSubtitle', 'Isi detail kategori layanan nail art dan tentukan icon yang sesuai')

@section('content')
@php
    $icons = [
        'wand-2' => [
            'label' => 'Extension',
            'category' => 'Extension',
            'desc' => 'Tongkat Ajaib / Nail Extension',
        ],
        'sparkles' => [
            'label' => 'Manicure',
            'category' => 'Manicure',
            'desc' => 'Kilau / Manicure Perawatan',
        ],
        'palette' => [
            'label' => 'Nail Art Custom',
            'category' => 'Nail Art Custom',
            'desc' => 'Palet Seni / Custom Art',
        ],
        'footprints' => [
            'label' => 'Pedicure',
            'category' => 'Pedicure',
            'desc' => 'Jejak Kaki / Pedicure Spa',
        ],
        'heart-handshake' => [
            'label' => 'Treatment',
            'category' => 'Treatment',
            'desc' => 'Perawatan Kuku & Relaksasi',
        ],
        'gem' => [
            'label' => 'Gem / Diamond',
            'category' => 'Aksesoris / Glamour',
            'desc' => 'Permata / Glam Nail Art',
        ],
        'crown' => [
            'label' => 'Crown',
            'category' => 'VIP / Premium',
            'desc' => 'Mahkota / Layanan Spesial',
        ],
        'heart' => [
            'label' => 'Heart',
            'category' => 'Beauty & Care',
            'desc' => 'Hati / Perawatan Kasih',
        ],
        'star' => [
            'label' => 'Star',
            'category' => 'Favorit',
            'desc' => 'Bintang / Rekomendasi',
        ],
        'zap' => [
            'label' => 'Zap',
            'category' => 'Express',
            'desc' => 'Kilat / Pengerjaan Cepat',
        ],
        'gift' => [
            'label' => 'Gift',
            'category' => 'Promo & Paket',
            'desc' => 'Paket Hadiah / Bundling',
        ],
        'folder' => [
            'label' => 'Folder',
            'category' => 'Umum',
            'desc' => 'Folder / Kategori Dasar',
        ],
    ];

    $defaultIcon = 'sparkles';
    if ($category) {
        $slug = strtolower($category->slug ?? '');
        if (str_contains($slug, 'extension')) $defaultIcon = 'wand-2';
        elseif (str_contains($slug, 'pedicure')) $defaultIcon = 'footprints';
        elseif (str_contains($slug, 'custom') || str_contains($slug, 'art')) $defaultIcon = 'palette';
        elseif (str_contains($slug, 'treatment')) $defaultIcon = 'heart-handshake';
        elseif (str_contains($slug, 'manicure')) $defaultIcon = 'sparkles';
    }

    $currentIcon = old('icon', $category?->icon ?? $defaultIcon);
    if (!array_key_exists($currentIcon, $icons)) {
        if ($currentIcon === 'extension') $currentIcon = 'wand-2';
        elseif ($currentIcon === 'manicure') $currentIcon = 'sparkles';
        elseif ($currentIcon === 'pedicure') $currentIcon = 'footprints';
        elseif ($currentIcon === 'nail-art-custom') $currentIcon = 'palette';
        elseif ($currentIcon === 'treatment') $currentIcon = 'heart-handshake';
        else $currentIcon = 'sparkles';
    }
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl"
     x-data="{
        iconModalOpen: false,
        selectedIcon: '{{ $currentIcon }}',
        searchQuery: '',
        icons: {{ json_encode($icons) }}
     }">
    <form action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if($category) @method('PUT') @endif
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category?->name) }}" required maxlength="100"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition font-semibold bg-gray-50/50">
                @error('name')
                    <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Slug URL (auto jika kosong)</label>
                <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" maxlength="120" placeholder="misal: manicure, extension, pedicure"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition font-semibold bg-gray-50/50">
                @error('slug')
                    <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Shadcn UI-styled Icon Picker Section --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Icon Kategori (Shadcn UI)</label>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-gray-50/70 border-2 border-gray-100 transition hover:border-gray-200">
                    <div class="flex items-center gap-3.5">
                        <div class="w-13 h-13 rounded-2xl bg-white border border-gray-200/80 shadow-xs flex items-center justify-center text-brand-600 shrink-0 p-3">
                            @foreach($icons as $iconKey => $meta)
                                <div x-show="selectedIcon === '{{ $iconKey }}'" x-cloak>
                                    <x-icon name="{{ $iconKey }}" class="w-7 h-7" />
                                </div>
                            @endforeach
                            <div x-show="!Object.keys(icons).includes(selectedIcon)" x-cloak>
                                <x-icon name="folder" class="w-7 h-7" />
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Icon Terpilih</div>
                            <div class="text-base font-bold text-gray-900 flex items-center gap-2 mt-0.5">
                                <span x-text="icons[selectedIcon]?.label || selectedIcon"></span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-medium bg-brand-50 text-brand-700 border border-brand-200" x-text="selectedIcon"></span>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5" x-text="icons[selectedIcon]?.desc || 'Icon untuk frontend pelanggan'"></div>
                        </div>
                    </div>

                    <button type="button" 
                            @click="iconModalOpen = true"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 hover:border-brand-300 text-gray-700 text-sm font-semibold shadow-xs transition active:scale-98">
                        <x-icon name="sparkles" class="w-4 h-4 text-brand-500" />
                        <span>Ganti Icon</span>
                    </button>
                </div>
                <input type="hidden" name="icon" :value="selectedIcon">
                @error('icon')
                    <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 p-3.5 rounded-2xl bg-brand-50/50 border-2 border-brand-100 cursor-pointer transition hover:bg-brand-50">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}
                       class="w-5 h-5 rounded text-brand-600 focus:ring-brand-500 border-gray-300">
                <div>
                    <div class="font-bold text-gray-900">Aktifkan Kategori</div>
                    <div class="text-xs text-gray-500 mt-0.5">Nonaktifkan sementara untuk menyembunyikan dari halaman frontend.</div>
                </div>
            </label>

            <div class="flex items-center gap-3 pt-3">
                <button type="submit" class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2">
                    <x-icon name="save" class="w-4 h-4" />
                    <span>{{ $category ? 'Simpan Perubahan' : 'Buat Kategori' }}</span>
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary !py-3 !px-6 justify-center">Batal</a>
            </div>
        </div>
    </form>

    {{-- Shadcn UI-styled Modal Popover --}}
    <div x-show="iconModalOpen" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <div class="bg-white rounded-2xl border border-gray-100 shadow-2xl max-w-lg w-full p-6 space-y-4"
             @click.away="iconModalOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
             
            {{-- Header --}}
            <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Pilih Icon Kategori (Shadcn UI)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih icon yang sesuai dengan kategori. Icon otomatis tersinkron ke halaman pelanggan & admin.</p>
                </div>
                <button type="button" @click="iconModalOpen = false" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl hover:bg-gray-100 transition">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            {{-- Grid of Icons --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-80 overflow-y-auto p-1">
                @foreach($icons as $iconKey => $meta)
                    <button type="button"
                            @click="selectedIcon = '{{ $iconKey }}'; iconModalOpen = false"
                            :class="selectedIcon === '{{ $iconKey }}' ? 'border-brand-500 bg-brand-50/80 ring-2 ring-brand-400/30 text-brand-700 shadow-xs' : 'border-gray-200/80 hover:border-brand-300 hover:bg-gray-50/80 text-gray-700'"
                            class="flex flex-col items-center justify-center p-3 rounded-xl border-2 transition text-center group cursor-pointer text-left">
                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center group-hover:scale-110 group-hover:border-brand-200 transition shadow-2xs">
                            <x-icon name="{{ $iconKey }}" class="w-5 h-5 text-brand-600" />
                        </div>
                        <span class="text-xs font-bold text-gray-900 mt-2">{{ $meta['label'] }}</span>
                        <span class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $iconKey }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <span class="text-xs text-gray-400">Klik icon untuk langsung memilih</span>
                <button type="button" @click="iconModalOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
