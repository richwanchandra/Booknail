@extends('layouts.admin')
@section('title', 'Manage Nail Artists')
@section('pageTitle', 'Daftar Nail Artist')
@section('pageSubtitle', 'Kelola data nail artist, jadwal & cuti')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.artists.create') }}" class="btn-primary !py-2.5 !px-5 !text-sm justify-center">+ Tambah Artist</a>
</div>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                    <th class="px-5 py-4">ID</th>
                    <th class="px-5 py-4">Artist</th>
                    <th class="px-5 py-4">Kontak</th>
                    <th class="px-5 py-4">Spesialisasi</th>
                    <th class="px-5 py-4">Rating</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($artists as $a)
                    @php
                        $words = preg_split('/\s+/', trim($a->user->name));
                        $initials = strtoupper(($words[0][0] ?? 'X') . (($words[1][0] ?? '') ?: ''));
                        $avg = number_format($a->reviews_avg_rating ?? 0, 1);
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4 font-mono text-gray-500">#{{ $a->id }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full gradient-brand flex items-center justify-center text-white font-extrabold shadow-card">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $a->user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">Role: <span class="font-bold text-purple-700">Artisan Teknisi</span> · {{ $a->user->email }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-xs text-gray-600 font-semibold flex items-center gap-1.5">
                                <x-lucide-mail class="w-3.5 h-3.5 text-gray-400" />
                                <span>{{ $a->user->email }}</span>
                            </div>
                            @if($a->user->phone)
                                <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                    <x-lucide-phone class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ $a->user->phone }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs font-semibold text-gray-700">{{ $a->specialty ?? 'All Nail Art' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <x-lucide-star class="w-3.5 h-3.5 text-amber-400 fill-amber-400" />
                                <span>{{ $avg }} · {{ $a->reviews_count ?? 0 }} ulasan</span>
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $a->is_active ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-600' }}">{{ $a->is_active ? 'Aktif (On-Duty)' : 'Nonaktif (Akses Ditutup)' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                @if(! $a->is_active)
                                    <form action="{{ route('admin.artists.toggleStatus', $a) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-1 shadow-sm" title="Aktifkan Kembali Akun Teknisi">
                                            <x-icon name="user-check" class="w-3.5 h-3.5" />
                                            <span>Aktifkan</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.artists.toggleStatus', $a) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan akun teknisi {{ $a->user->name }}?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-600 text-xs font-semibold transition flex items-center gap-1 border border-gray-200" title="Nonaktifkan Akses Portal">
                                            <x-icon name="power" class="w-3.5 h-3.5" />
                                            <span>Nonaktif</span>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.schedules.index', ['artist_id' => $a->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-purple-500 hover:bg-purple-600 text-white text-xs font-bold transition">Jadwal</a>
                                <a href="{{ route('admin.leaves.index', ['artist_id' => $a->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition">Cuti</a>
                                <a href="{{ route('admin.artists.edit', $a) }}" class="px-2.5 py-1.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold transition">Edit</a>
                                <form action="{{ route('admin.artists.destroy', $a) }}" method="POST" onsubmit="return confirm('Hapus artist {{ $a->user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2.5 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-16 text-center text-gray-400 font-semibold">Belum ada artist. Klik "Tambah Artist".</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">{{ $artists->links() }}</div>
</div>
@endsection
