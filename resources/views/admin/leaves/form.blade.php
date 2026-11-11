@extends('layouts.admin')
@section('title', 'Tambah Cuti Artist')
@section('pageTitle', 'Tambah Catatan Cuti Artist')
@section('pageSubtitle', 'Slot pada tanggal ini otomatis ditolak availability-nya')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    <form action="{{ route('admin.leaves.store') }}" method="POST">
        @csrf
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Pilih Artist *</label>
                <select name="artist_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none font-semibold bg-gray-50/50">
                    <option value="">Pilih artist...</option>
                    @foreach($artists as $a)
                        <option value="{{ $a->id }}" {{ old('artist_id') == $a->id ? 'selected' : '' }}>{{ $a->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Tanggal Cuti *</label>
                    <input type="date" name="leave_date" required min="{{ now()->format('Y-m-d') }}" value="{{ old('leave_date') }}" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none font-semibold bg-gray-50/50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Tipe Cuti</label>
                    <select name="leave_type" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none font-semibold bg-gray-50/50">
                        @foreach(['annual'=>'Annual (Tahunan)','sick'=>'Sakit','personal'=>'Pribadi','other'=>'Lainnya'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('leave_type','personal')===$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-50 border-2 border-emerald-100 cursor-pointer">
                <input type="checkbox" name="is_paid" value="1" {{ old('is_paid', true) ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                <div>
                    <div class="font-bold text-gray-900">Cuti Dibayar (Paid Leave)</div>
                </div>
            </label>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Alasan / Catatan</label>
                <textarea name="reason" rows="3" maxlength="500" placeholder="Opsional: alasan cuti..." class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-brand-500 outline-none bg-gray-50/50 resize-y">{{ old('reason') }}</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-6">
            <button type="submit" class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2">
                <x-lucide-save class="w-4 h-4" />
                <span>Simpan Cuti</span>
            </button>
            <a href="{{ route('admin.leaves.index') }}" class="btn-secondary !py-3 !px-6 justify-center">Batal</a>
        </div>
    </form>
</div>
@endsection
