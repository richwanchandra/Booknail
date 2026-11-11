@extends('layouts.admin')
@section('title', $category ? 'Edit Kategori' : 'Tambah Kategori')
@section('pageTitle', $category ? 'Edit Kategori: '.$category->name : 'Tambah Kategori Baru')
@section('pageSubtitle', 'Isi detail kategori layanan nail art')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    <form action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if($category) @method('PUT') @endif
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category?->name) }}" required maxlength="100"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition font-semibold bg-gray-50/50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Slug URL (auto jika kosong)</label>
                <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" maxlength="120" placeholder="misal: manicure-pedicure"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition font-semibold bg-gray-50/50">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" rows="4" maxlength="1000" placeholder="Deskripsi singkat kategori..."
                          class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 focus:ring-0 outline-none transition bg-gray-50/50 resize-y">{{ old('description', $category?->description) }}</textarea>
            </div>
            <label class="flex items-center gap-3 p-3 rounded-2xl bg-brand-50/50 border-2 border-brand-100 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}
                       class="w-5 h-5 rounded text-brand-600 focus:ring-brand-500 border-gray-300">
                <div>
                    <div class="font-bold text-gray-900">Aktifkan Kategori</div>
                    <div class="text-xs text-gray-500 mt-0.5">Nonaktifkan sementara untuk menyembunyikan dari halaman frontend.</div>
                </div>
            </label>
            <div class="flex items-center gap-3 pt-3">
                <button type="submit" class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2">
                    <x-lucide-save class="w-4 h-4" />
                    <span>{{ $category ? 'Simpan Perubahan' : 'Buat Kategori' }}</span>
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary !py-3 !px-6 justify-center">Batal</a>
            </div>
        </div>
    </form>
</div>
@endsection
