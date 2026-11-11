@extends('layouts.admin')

@section('title', 'Galeri Portofolio')
@section('pageTitle', 'Galeri Portofolio Nail Art')
@section('pageSubtitle', 'Kelola pameran karya, inspirasi desain kuku, dan katalog artist untuk menarik pelanggan')

@section('content')
<div class="space-y-6">

    {{-- ================= METRICS STATS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-[#DCD9D9]/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F4D7D4]/60 text-[#B76E79] flex items-center justify-center shrink-0">
                <x-icon name="images" class="w-6 h-6" />
            </div>
            <div>
                <p class="text-xs font-semibold text-[#49454F] uppercase tracking-wider">Total Portofolio</p>
                <h3 class="text-2xl font-bold font-serif text-[#1C1B1B] mt-0.5">{{ $stats['total'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#DCD9D9]/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <x-icon name="check-circle-2" class="w-6 h-6" />
            </div>
            <div>
                <p class="text-xs font-semibold text-[#49454F] uppercase tracking-wider">Tampil di Web</p>
                <h3 class="text-2xl font-bold font-serif text-emerald-700 mt-0.5">{{ $stats['active'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#DCD9D9]/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <x-icon name="alert-circle" class="w-6 h-6" />
            </div>
            <div>
                <p class="text-xs font-semibold text-[#49454F] uppercase tracking-wider">Draft / Nonaktif</p>
                <h3 class="text-2xl font-bold font-serif text-amber-700 mt-0.5">{{ $stats['inactive'] }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-[#DCD9D9]/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#F6F3F2] text-[#49454F] flex items-center justify-center shrink-0 border border-[#DCD9D9]">
                <x-icon name="sparkles" class="w-6 h-6 text-[#B76E79]" />
            </div>
            <div>
                <p class="text-xs font-semibold text-[#49454F] uppercase tracking-wider">Kategori Terkait</p>
                <h3 class="text-2xl font-bold font-serif text-[#1C1B1B] mt-0.5">{{ $categories->count() }}</h3>
            </div>
        </div>
    </div>

    {{-- ================= ACTION & FILTER TOOLBAR ================= --}}
    <div class="bg-white p-5 rounded-2xl border border-[#DCD9D9]/80 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.portfolios.index') }}" class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                {{-- Search Input --}}
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, tag, atau deskripsi..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#DCD9D9] text-xs font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none transition bg-[#FBF9F8]">
                    <div class="absolute left-3 top-3 text-[#49454F]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
                            <path d="m21 21-4.35-4.35" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <select name="category_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-[#DCD9D9] text-xs font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Artist Filter --}}
                <div>
                    <select name="nail_artist_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 rounded-xl border border-[#DCD9D9] text-xs font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">Semua Nail Artist</option>
                        @foreach($artists as $art)
                            <option value="{{ $art->id }}" {{ request('nail_artist_id') == $art->id ? 'selected' : '' }}>
                                {{ $art->user?->name ?? 'Artist #'.$art->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="flex gap-2">
                    <select name="status" onchange="this.form.submit()"
                            class="flex-1 px-3 py-2.5 rounded-xl border border-[#DCD9D9] text-xs font-medium focus:border-[#B76E79] focus:ring-1 focus:ring-[#B76E79] outline-none bg-[#FBF9F8]">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @if(request()->anyFilled(['search', 'category_id', 'nail_artist_id', 'status']))
                        <a href="{{ route('admin.portfolios.index') }}" title="Reset Filter"
                           class="px-3 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-[#49454F] text-xs font-semibold flex items-center justify-center transition">
                            <x-icon name="x" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </form>

            {{-- Actions --}}
            <div class="flex items-center gap-3 shrink-0">
                {{-- View Mode Toggle --}}
                <div class="inline-flex rounded-xl p-1 bg-[#F6F3F2] border border-[#DCD9D9]" id="viewToggle">
                    <button type="button" onclick="switchView('grid')" id="btnGrid"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-white text-[#1C1B1B] shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="7" height="7" x="3" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="3" rx="1"></rect>
                            <rect width="7" height="7" x="14" y="14" rx="1"></rect>
                            <rect width="7" height="7" x="3" y="14" rx="1"></rect>
                        </svg>
                        <span>Galeri</span>
                    </button>
                    <button type="button" onclick="switchView('table')" id="btnTable"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition text-[#49454F] hover:text-[#1C1B1B] flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" x2="21" y1="6" y2="6"></line>
                            <line x1="3" x2="21" y1="12" y2="12"></line>
                            <line x1="3" x2="21" y1="18" y2="18"></line>
                        </svg>
                        <span>Tabel</span>
                    </button>
                </div>

                {{-- Add New Portfolio Button --}}
                <a href="{{ route('admin.portfolios.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#B76E79] hover:bg-[#a65d68] text-white text-xs font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>+ Tambah Portofolio</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ================= MAIN VIEW CONTENT ================= --}}

    {{-- 1. GRID GALLERY VIEW (DEFAULT) --}}
    <div id="galleryGridView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @forelse($portfolios as $p)
            <div class="group bg-white rounded-2xl border border-[#DCD9D9]/80 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-all duration-300">
                {{-- Card Image Preview --}}
                <div class="relative aspect-square w-full bg-[#F6F3F2] overflow-hidden cursor-pointer" onclick="openPhotoModal('{{ $p->image_url }}', '{{ addslashes($p->title) }}')">
                    <img src="{{ $p->image_url }}" alt="{{ $p->title }}"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                         loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1632345031435-8727f6897d53?auto=format&fit=crop&w=600&q=80'">
                    
                    {{-- Status Badge (Top-Right) --}}
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold shadow-sm backdrop-blur-md {{ $p->is_active ? 'bg-emerald-500/90 text-white' : 'bg-gray-700/80 text-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $p->is_active ? 'bg-white' : 'bg-gray-400' }}"></span>
                            {{ $p->is_active ? 'Aktif' : 'Draft' }}
                        </span>
                    </div>

                    {{-- Category Badge (Top-Left) --}}
                    @if($p->category)
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/90 text-[#B76E79] shadow-sm backdrop-blur-md">
                                {{ $p->category->name }}
                            </span>
                        </div>
                    @endif

                    {{-- Hover Overlay with Zoom Icon --}}
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                        <div class="w-10 h-10 rounded-full bg-white/90 text-[#1C1B1B] flex items-center justify-center shadow-lg">
                            <x-icon name="eye" class="w-5 h-5 text-[#B76E79]" />
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="font-serif text-base font-bold text-[#1C1B1B] line-clamp-1 group-hover:text-[#B76E79] transition">
                            {{ $p->title }}
                        </h4>

                        @if($p->description)
                            <p class="text-xs text-[#49454F] mt-1 line-clamp-2 leading-relaxed">
                                {{ $p->description }}
                            </p>
                        @endif

                        {{-- Artist Info --}}
                        <div class="mt-3 flex items-center gap-2 text-xs text-[#49454F]">
                            <div class="w-5 h-5 rounded-full bg-[#F4D7D4] text-[#B76E79] font-bold flex items-center justify-center text-[10px]">
                                {{ strtoupper(substr($p->nailArtist?->user?->name ?? 'A', 0, 1)) }}
                            </div>
                            <span class="font-medium truncate">
                                {{ $p->nailArtist?->user?->name ? 'Karya: '.$p->nailArtist->user->name : 'Nail Artist Studio' }}
                            </span>
                        </div>

                        {{-- Tags --}}
                        @if(!empty($p->tag_list))
                            <div class="mt-2.5 flex flex-wrap gap-1">
                                @foreach(array_slice($p->tag_list, 0, 3) as $t)
                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#F6F3F2] text-[#49454F] font-medium">#{{ $t }}</span>
                                @endforeach
                                @if(count($p->tag_list) > 3)
                                    <span class="text-[10px] px-1.5 py-0.5 text-[#49454F]/70 font-semibold">+{{ count($p->tag_list) - 3 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Card Footer Action Buttons --}}
                    <div class="mt-4 pt-3 border-t border-[#DCD9D9]/50 flex items-center justify-between gap-2">
                        {{-- Toggle Status --}}
                        <form action="{{ route('admin.portfolios.toggleStatus', $p) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" 
                                    class="text-[11px] font-semibold px-2.5 py-1 rounded-lg transition {{ $p->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                {{ $p->is_active ? 'Sembunyikan' : 'Tampilkan' }}
                            </button>
                        </form>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.portfolios.edit', $p) }}" 
                               class="px-2.5 py-1 rounded-lg bg-[#F4D7D4]/60 text-[#B76E79] hover:bg-[#F4D7D4] text-[11px] font-bold transition">
                                Edit
                            </a>

                            <form action="{{ route('admin.portfolios.destroy', $p) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto portofolio ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 rounded-lg text-rose-500 hover:bg-rose-50 transition" title="Hapus Portofolio">
                                    <x-icon name="x" class="w-4 h-4" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-[#DCD9D9]">
                <div class="w-16 h-16 mx-auto rounded-full bg-[#F4D7D4]/50 flex items-center justify-center text-[#B76E79] mb-4">
                    <x-icon name="images" class="w-8 h-8" />
                </div>
                <h3 class="font-serif text-lg font-bold text-[#1C1B1B]">Belum Ada Portofolio Ditemukan</h3>
                <p class="text-xs text-[#49454F] mt-1 max-w-sm mx-auto">
                    Mulai upload karya foto nail art terbaik untuk menarik perhatian pengunjung website.
                </p>
                <div class="mt-5">
                    <a href="{{ route('admin.portfolios.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#B76E79] text-white text-xs font-bold hover:bg-[#a65d68] transition shadow-sm">
                        + Tambah Foto Portofolio Pertama
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 2. TABLE VIEW (ALTERNATIVE) --}}
    <div id="galleryTableView" class="hidden bg-white rounded-2xl border border-[#DCD9D9]/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#FBF9F8] border-b border-[#DCD9D9]">
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-[#49454F]">
                        <th class="px-5 py-4">Foto</th>
                        <th class="px-5 py-4">Judul & Tag</th>
                        <th class="px-5 py-4">Kategori</th>
                        <th class="px-5 py-4">Nail Artist</th>
                        <th class="px-5 py-4">Urutan</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DCD9D9]/50">
                    @forelse($portfolios as $p)
                        <tr class="hover:bg-[#FBF9F8]/60 transition">
                            <td class="px-5 py-3">
                                <img src="{{ $p->image_url }}" alt="{{ $p->title }}"
                                     class="w-14 h-14 rounded-xl object-cover border border-[#DCD9D9] cursor-pointer"
                                     onclick="openPhotoModal('{{ $p->image_url }}', '{{ addslashes($p->title) }}')">
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-[#1C1B1B]">{{ $p->title }}</div>
                                @if($p->description)
                                    <div class="text-xs text-[#49454F] truncate max-w-xs mt-0.5">{{ $p->description }}</div>
                                @endif
                                @if(!empty($p->tag_list))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach(array_slice($p->tag_list, 0, 3) as $tag)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-[#F6F3F2] text-[#49454F]">#{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-[#F4D7D4]/40 text-[#B76E79]">
                                    {{ $p->category?->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs font-medium text-[#1C1B1B]">
                                {{ $p->nailArtist?->user?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-[#49454F]">
                                {{ $p->sort_order }}
                            </td>
                            <td class="px-5 py-3">
                                <form action="{{ route('admin.portfolios.toggleStatus', $p) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $p->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                        {{ $p->is_active ? 'Aktif' : 'Draft' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.portfolios.edit', $p) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#B76E79] hover:bg-[#a65d68] text-white text-xs font-bold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.portfolios.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus karya {{ $p->title }}?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-[#49454F] font-medium">
                                Tidak ada data portofolio.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= PAGINATION ================= --}}
    @if($portfolios->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-[#DCD9D9]/80 shadow-sm">
            {{ $portfolios->links() }}
        </div>
    @endif

</div>

{{-- ================= PHOTO LIGHTBOX MODAL ================= --}}
<div id="photoModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closePhotoModal()">
    <div class="relative max-w-3xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <div class="relative bg-black flex items-center justify-center max-h-[75vh]">
            <img id="modalImg" src="" alt="Preview" class="max-h-[75vh] w-auto object-contain">
            <button onclick="closePhotoModal()" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/50 text-white hover:bg-black/80 flex items-center justify-center transition">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        <div class="p-5 flex items-center justify-between bg-white border-t border-[#DCD9D9]">
            <div>
                <h4 id="modalTitle" class="font-serif text-lg font-bold text-[#1C1B1B]"></h4>
                <p class="text-xs text-[#49454F] mt-0.5">Pratinjau Foto Portofolio Kualitas Tinggi</p>
            </div>
            <button onclick="closePhotoModal()" class="px-4 py-2 rounded-xl border border-[#DCD9D9] text-xs font-semibold text-[#49454F] hover:bg-[#F6F3F2] transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function switchView(mode) {
        const grid = document.getElementById('galleryGridView');
        const table = document.getElementById('galleryTableView');
        const btnGrid = document.getElementById('btnGrid');
        const btnTable = document.getElementById('btnTable');

        if (mode === 'grid') {
            grid.classList.remove('hidden');
            table.classList.add('hidden');
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-white text-[#1C1B1B] shadow-sm flex items-center gap-1.5';
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition text-[#49454F] hover:text-[#1C1B1B] flex items-center gap-1.5';
            localStorage.setItem('portfolio_view', 'grid');
        } else {
            grid.classList.add('hidden');
            table.classList.remove('hidden');
            btnTable.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition bg-white text-[#1C1B1B] shadow-sm flex items-center gap-1.5';
            btnGrid.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition text-[#49454F] hover:text-[#1C1B1B] flex items-center gap-1.5';
            localStorage.setItem('portfolio_view', 'table');
        }
    }

    function openPhotoModal(url, title) {
        document.getElementById('modalImg').src = url;
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('photoModal').classList.remove('hidden');
    }

    function closePhotoModal() {
        document.getElementById('photoModal').classList.add('hidden');
    }

    // Restore view preference
    document.addEventListener('DOMContentLoaded', () => {
        const savedView = localStorage.getItem('portfolio_view');
        if (savedView === 'table') {
            switchView('table');
        }
    });
</script>
@endsection
