@extends('layouts.admin')
@section('title', 'Artist Schedules')
@section('pageTitle', 'Jadwal Kerja Nail Artist (7 Hari)')
@section('pageSubtitle', 'Atur jadwal mingguan per artist. 0 = Minggu, 6 = Sabtu')

@section('content')
@php
    $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
@endphp
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-gray-600 mb-1">Pilih Artist</label>
            <select name="artist_id" required class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-100 focus:border-brand-400 outline-none font-semibold bg-white text-sm">
                <option value="">Pilih artist...</option>
                @foreach($artists as $a)
                    <option value="{{ $a->id }}" {{ ($artistId ?? '') == $a->id ? 'selected' : '' }}>{{ $a->user->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary !py-2.5 !px-5 !text-sm justify-center flex items-center gap-1.5">
            <x-lucide-search class="w-4 h-4" />
            <span>Lihat Jadwal</span>
        </button>
    </form>
</div>

@if($artistId)
    @php
        $selArtist = $artists->firstWhere('id', $artistId);
        $cur = $schedules->get($artistId, collect());
        $byDay = [];
        foreach ($cur as $row) $byDay[$row->day_of_week] = $row;
    @endphp
    @if($selArtist)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-extrabold text-xl text-gray-900 mb-4 flex items-center gap-2">
            <x-lucide-clipboard-list class="w-5 h-5 text-brand-600" />
            <span>Jadwal: {{ $selArtist->user->name }}</span>
        </h3>
        <form action="{{ route('admin.schedules.bulkStore') }}" method="POST">
            @csrf
            <input type="hidden" name="artist_id" value="{{ $artistId }}">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($days as $idx => $d)
                    @php
                        $row = $byDay[$idx] ?? null;
                    @endphp
                    <div class="p-4 rounded-2xl border-2 {{ $row?->is_off ? 'bg-gray-50 border-gray-200' : 'bg-brand-50/50 border-brand-100' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-extrabold text-base text-gray-900">{{ $d }}</div>
                            <label class="flex items-center gap-1.5 text-xs font-bold cursor-pointer {{ $row?->is_off ? 'text-gray-600' : 'text-brand-700' }}">
                                <input type="checkbox" name="schedules[{{ $idx }}][is_off]" value="1" class="w-4 h-4 off-day-cb" data-day="{{ $idx }}" {{ $row?->is_off ? 'checked' : '' }}>
                                <span>Off</span>
                            </label>
                        </div>
                        <input type="hidden" name="schedules[{{ $idx }}][day_of_week]" value="{{ $idx }}">
                        <div class="grid grid-cols-2 gap-2 schedule-fields {{ $row?->is_off ? 'opacity-40 pointer-events-none' : '' }}" data-field-day="{{ $idx }}">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Masuk</label>
                                <input type="time" name="schedules[{{ $idx }}][start_time]" value="{{ $row?->is_off ? '00:00' : substr($row?->start_time ?? '09:00:00', 0, 5) }}" class="w-full px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-brand-400 outline-none font-semibold text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pulang</label>
                                <input type="time" name="schedules[{{ $idx }}][end_time]" value="{{ $row?->is_off ? '00:00' : substr($row?->end_time ?? '17:00:00', 0, 5) }}" class="w-full px-2 py-2 rounded-lg border-2 border-gray-100 focus:border-brand-400 outline-none font-semibold text-xs bg-white">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-3 pt-6 mt-3">
                <button class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Simpan Jadwal 7 Hari</span>
                </button>
                <a href="{{ route('admin.schedules.index') }}" class="btn-secondary !py-3 !px-6 justify-center">Reset Pilih</a>
            </div>
        </form>
    </div>
    @endif
@else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-500 shadow-soft">
            <x-lucide-clock class="w-8 h-8" />
        </div>
        <div class="text-gray-500 font-semibold">Pilih nail artist di atas untuk melihat & mengedit jadwal mingguan.</div>
    </div>
@endif

<script>
document.querySelectorAll('.off-day-cb').forEach(cb => {
    cb.addEventListener('change', () => {
        const idx = cb.dataset.day;
        const fields = document.querySelector(`.schedule-fields[data-field-day="${idx}"]`);
        if (!fields) return;
        fields.classList.toggle('opacity-40', cb.checked);
        fields.classList.toggle('pointer-events-none', cb.checked);
        const parent = cb.closest('.border-2');
        if (parent) {
            parent.classList.toggle('bg-gray-50', cb.checked);
            parent.classList.toggle('border-gray-200', cb.checked);
            parent.classList.toggle('bg-brand-50/50', !cb.checked);
            parent.classList.toggle('border-brand-100', !cb.checked);
        }
    });
});
</script>
@endsection
