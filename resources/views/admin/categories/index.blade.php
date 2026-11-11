@extends('layouts.admin')
@section('title', 'Manage Categories')
@section('pageTitle', 'Kategori Layanan')
@section('pageSubtitle', 'Kelola kategori layanan nail art')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.categories.create') }}" class="btn-primary !py-2.5 !px-5 !text-sm justify-center">+ Tambah Kategori</a>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                    <th class="px-5 py-4">ID</th>
                    <th class="px-5 py-4">Icon</th>
                    <th class="px-5 py-4">Nama Kategori</th>
                    <th class="px-5 py-4">Slug</th>
                    <th class="px-5 py-4">Jumlah Layanan</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $c)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4 font-mono text-gray-500">#{{ $c->id }}</td>
                        <td class="px-5 py-4">
                            <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600">
                                <x-icon name="{{ $c->icon ?? 'folder' }}" class="w-5 h-5" />
                            </div>
                        </td>
                        <td class="px-5 py-4 font-bold text-gray-900">{{ $c->name }}</td>
                        <td class="px-5 py-4 text-xs text-gray-500">{{ $c->slug }}</td>
                        <td class="px-5 py-4 font-semibold">{{ $c->services_count }} layanan</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $c->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.categories.edit', $c) }}" class="px-3 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold transition">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $c->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400 font-semibold">Belum ada kategori. Klik "Tambah Kategori" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">{{ $categories->links() }}</div>
</div>
@endsection
