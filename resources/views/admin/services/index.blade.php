@extends('layouts.admin')
@section('title', 'Manage Services')
@section('pageTitle', 'Daftar Layanan Nail Art')
@section('pageSubtitle', 'Kelola semua layanan, harga & durasi')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.services.create') }}" class="btn-primary !py-2.5 !px-5 !text-sm justify-center">+ Tambah Layanan</a>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                    <th class="px-5 py-4">ID</th>
                    <th class="px-5 py-4">Nama Layanan</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Harga</th>
                    <th class="px-5 py-4">Durasi</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($services as $s)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4 font-mono text-gray-500">#{{ $s->id }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    $sImg = $s->image ?? $s->service_image;
                                    $thumb = null;
                                    if ($sImg) {
                                        $thumb = str_starts_with($sImg, 'data:image') 
                                            ? $sImg 
                                            : (Storage::disk('public')->exists($sImg) ? Storage::disk('public')->url($sImg) : $sImg);
                                    }
                                @endphp
                                @if($thumb)
                                    <img src="{{ $thumb }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100" alt="{{ $s->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-lg gradient-brand flex items-center justify-center text-white">
                                        <x-lucide-sparkles class="w-5 h-5" />
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900">{{ $s->name }}</div>
                                    @if($s->description) <div class="text-xs text-gray-500 truncate max-w-[280px]">{{ $s->description }}</div> @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border border-brand-100 bg-brand-50 text-brand-700">{{ $s->category?->name ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-4 font-extrabold text-gray-900">{{ $s->formatted_price }}</td>
                        <td class="px-5 py-4 font-semibold">{{ $s->formatted_duration }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $s->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-500' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.services.edit', $s) }}" class="px-3 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold transition">Edit</a>
                                <form action="{{ route('admin.services.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus layanan {{ $s->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400 font-semibold">Belum ada layanan. Klik "Tambah Layanan" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">{{ $services->links() }}</div>
</div>
@endsection
