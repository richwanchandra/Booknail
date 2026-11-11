@extends('layouts.admin')
@section('title', $artist ? 'Edit Teknisi Artist' : 'Tambah Teknisi Artist')
@section('pageTitle', $artist ? 'Kelola Akun: '.optional($artist->user)->name : 'Tambah Teknisi Nail Artist Baru')
@section('pageSubtitle', 'Manajemen Kredensial Login, Profil, & Hak Akses (RBAC)')

@section('content')
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 max-w-3xl">
    <form action="{{ $artist ? route('admin.artists.update', $artist) : route('admin.artists.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($artist) @method('PUT') @endif

        <div class="space-y-6">
            {{-- BOX RBAC KREDENSIAL LOGIN --}}
            <div class="p-5 rounded-3xl bg-purple-50/60 border-2 border-purple-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-800">
                        <x-lucide-shield-check class="w-4 h-4 text-purple-600" />
                        <span>Kredensial Login Akun Teknisi (Role: Artist)</span>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">RBAC Guard</span>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Pilih Akun User Terdaftar (Opsional)</label>
                        <select name="user_id" class="w-full px-3.5 py-2.5 rounded-2xl border-2 border-gray-200 focus:border-purple-400 outline-none font-semibold bg-white text-xs">
                            <option value="">Buat akun user baru otomatis</option>
                            @foreach($availableUsers as $u)
                                <option value="{{ $u->id }}" {{ old('user_id', $artist?->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Lengkap Teknisi</label>
                        <input type="text" name="artist_name" value="{{ old('artist_name', optional($artist?->user)->name) }}" placeholder="Contoh: Maya Permata" class="w-full px-3.5 py-2.5 rounded-2xl border-2 border-gray-200 focus:border-purple-400 outline-none text-xs font-semibold bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">Email Login</label>
                        <input type="email" name="artist_email" value="{{ old('artist_email', optional($artist?->user)->email) }}" placeholder="nama@booknail.id" class="w-full px-3.5 py-2.5 rounded-2xl border-2 border-gray-200 focus:border-purple-400 outline-none text-xs font-semibold bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">
                            Password Login
                            @if($artist)
                                <span class="text-[10px] text-gray-400 font-normal">(Kosongkan jika tak diubah)</span>
                            @else
                                <span class="text-[10px] text-purple-600 font-normal">(Default: Artist12345!)</span>
                            @endif
                        </label>
                        <input type="password" name="artist_password" placeholder="Min. 8 karakter..." class="w-full px-3.5 py-2.5 rounded-2xl border-2 border-gray-200 focus:border-purple-400 outline-none text-xs font-semibold bg-white">
                    </div>
                </div>
            </div>

            {{-- DETAIL PROFIL & BIODATA --}}
            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">No. WhatsApp Teknisi</label>
                    <input type="text" name="artist_phone" value="{{ old('artist_phone', optional($artist?->user)?->phone) }}" maxlength="20" placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] outline-none font-semibold bg-gray-50/50 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal Bergabung (Hire Date)</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $artist?->hire_date?->format('Y-m-d')) }}" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] outline-none font-semibold bg-gray-50/50 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Spesialisasi Nail Art</label>
                    <input type="text" name="specialization" maxlength="255" value="{{ old('specialization', $artist?->specialty) }}" placeholder="Contoh: 3D Acrylic, Russian Manicure, French Chrome, Bridal Art" class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] outline-none font-semibold bg-gray-50/50 text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Bio Singkat</label>
                    <textarea name="bio" rows="3" maxlength="2000" placeholder="Ceritakan pengalaman dan keahlian artis ini..." class="w-full px-4 py-3 rounded-2xl border-2 border-gray-100 focus:border-[#B76E79] outline-none bg-gray-50/50 resize-y text-sm">{{ old('bio', $artist?->bio) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Foto Profil Teknisi (JPG/PNG, Max 2MB)</label>
                    <input type="file" name="artist_image" accept="image/jpeg,image/png" class="w-full text-xs file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-[#B76E79] file:text-white file:font-bold hover:file:bg-[#a25a65] cursor-pointer rounded-2xl border-2 border-gray-100 bg-white p-1">
                    @php
                        $artistPhoto = $artist?->photo ?? $artist?->artist_image ?? null;
                        $artistPhotoUrl = $artistPhoto ? (str_starts_with($artistPhoto, 'data:') ? $artistPhoto : (Storage::disk('public')->exists($artistPhoto) ? Storage::disk('public')->url($artistPhoto) : null)) : null;
                    @endphp
                    @if($artistPhotoUrl)
                        <div class="mt-2 flex items-center gap-2">
                            <img src="{{ $artistPhotoUrl }}" alt="Foto" class="w-10 h-10 rounded-xl object-cover border border-gray-200">
                            <span class="text-xs text-emerald-600 font-semibold">Foto tersimpan aktif (Base64/Storage)</span>
                        </div>
                    @endif
                </div>

                {{-- HAK AKSES RBAC / STATUS AKTIF --}}
                <label class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-200 cursor-pointer sm:col-span-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $artist?->is_active ?? true) ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                    <div>
                        <div class="font-bold text-gray-900 text-sm">Akun Teknisi Aktif & Berhak Login (On-Duty)</div>
                        <div class="text-xs text-gray-500 mt-0.5">Jika dinonaktifkan, teknisi tidak dapat masuk ke portal kerja dan otomatis disembunyikan dari pilihan jadwal booking pelanggan.</div>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-6 mt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary !py-3 !px-6 justify-center flex items-center gap-2 shadow-soft">
                <x-lucide-save class="w-4 h-4" />
                <span>{{ $artist ? 'Simpan Perubahan' : 'Buat Akun Teknisi' }}</span>
            </button>
            <a href="{{ route('admin.artists.index') }}" class="btn-secondary !py-3 !px-6 justify-center">Batal</a>
        </div>
    </form>
</div>
@endsection
