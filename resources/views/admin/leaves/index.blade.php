@extends('layouts.admin')
@section('title', 'Artist Leaves')
@section('pageTitle', 'Data Cuti / Libur Khusus Artist')
@section('pageSubtitle', 'Atur cuti artist agar slot availability otomatis tertolak')

@section('content')
<div class="flex flex-wrap justify-between items-end gap-3 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('admin.leaves.index') }}" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Filter Artist</label>
                <select name="artist_id" class="px-3 py-2 rounded-xl border-2 border-gray-100 focus:border-brand-400 outline-none font-semibold text-sm bg-white">
                    <option value="">[Semua Artist]</option>
                    @foreach($artists as $a)
                        <option value="{{ $a->id }}" {{ ($artistId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-secondary !py-2 !px-4 !text-sm justify-center">Filter</button>
            <a href="{{ route('admin.leaves.index') }}" class="text-xs font-bold text-gray-500 underline">Reset</a>
        </form>
    </div>
    <a href="{{ route('admin.leaves.create') }}" class="btn-primary !py-2.5 !px-5 !text-sm justify-center">+ Tambah Cuti</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                    <th class="px-5 py-4">Artist</th>
                    <th class="px-5 py-4">Tanggal Cuti</th>
                    <th class="px-5 py-4">Tipe</th>
                    <th class="px-5 py-4">Paid?</th>
                    <th class="px-5 py-4">Alasan</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($leaves as $l)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4 font-bold text-gray-900">{{ optional($l->artist->user)->name ?? 'Artist #'.$l->artist_id }}</td>
                        <td class="px-5 py-4 font-semibold">{{ \Carbon\Carbon::parse($l->leave_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border border-amber-100 bg-amber-50 text-amber-700">{{ $l->leave_type_human ?? ucfirst($l->leave_type ?? 'personal') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $l->is_paid ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 bg-gray-50 text-gray-600' }}">{{ $l->is_paid ? 'Paid' : 'Unpaid' }}</span>
                        </td>
                        <td class="px-5 py-4 text-xs text-gray-600 max-w-[300px]">{{ $l->reason ?? '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <form action="{{ route('admin.leaves.destroy', $l) }}" method="POST" onsubmit="return confirm('Hapus cuti ini?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 rounded-lg bg-red-500 hover:bg-red-600 text-white text-xs font-bold transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400 font-semibold">Tidak ada data cuti untuk artist ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100">{{ $leaves->withQueryString()->links() }}</div>
</div>
@endsection
