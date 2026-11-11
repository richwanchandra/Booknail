<?php

// ==========================================================================
// FrontendController.php
// Controller untuk menangani halaman-halaman publik (frontend) aplikasi.
// Meliputi halaman beranda, daftar layanan, detail layanan, halaman booking,
// serta dashboard untuk admin dan pelanggan.
// ==========================================================================

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Category;
use App\Models\NailArtist;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Service;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    /**
     * Menampilkan halaman beranda (landing page) aplikasi.
     * Memuat data kategori, layanan unggulan, artis, review, dan portofolio.
     */
    public function home()
    {
        // Ambil semua kategori aktif beserta jumlah layanan di masing-masing kategori
        $categories = Category::active()->withCount('services')->get();
        // Ambil 6 layanan aktif secara acak sebagai layanan unggulan
        $featuredServices = Service::active()
            ->with('category')
            ->inRandomOrder()
            ->limit(6)
            ->get();
        // Ambil semua nail artist aktif beserta rata-rata rating dan jumlah review
        $artists = NailArtist::active()
            ->with('user', 'schedules')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->get();
        // Ambil 6 review terbaru untuk ditampilkan di halaman beranda
        $reviews = Review::with('customer')
            ->latest()
            ->limit(6)
            ->get();
        // Ambil 8 portofolio aktif yang sudah diurutkan untuk galeri
        $portfolios = Portfolio::active()
            ->with(['category', 'nailArtist.user'])
            ->ordered()
            ->limit(8)
            ->get();

        return view('welcome', compact(
            'categories',
            'featuredServices',
            'artists',
            'reviews',
            'portfolios'
        ));
    }

    /**
     * Menampilkan halaman daftar layanan dengan fitur filter berdasarkan kategori.
     * Mendukung pagination 9 item per halaman.
     */
    public function services(Request $request)
    {
        // Ambil slug kategori dari query parameter untuk filtering
        $categorySlug = $request->get('category');

        // Ambil semua kategori aktif untuk sidebar filter
        $categories = Category::active()->withCount('services')->get();

        // Query layanan aktif dengan filter kategori jika dipilih
        $services = Service::active()
            ->with('category')
            ->when($categorySlug, function ($q) use ($categorySlug) {
                // Filter layanan berdasarkan slug kategori
                $q->whereHas('category', function ($subQ) use ($categorySlug) {
                    $subQ->where('slug', $categorySlug);
                });
            })
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(9);

        // Ambil objek kategori aktif untuk ditampilkan sebagai judul filter
        $activeCategory = $categorySlug
            ? Category::where('slug', $categorySlug)->first()
            : null;

        return view('services.index', compact(
            'categories',
            'services',
            'activeCategory'
        ));
    }

    /**
     * Menampilkan halaman detail layanan beserta layanan terkait
     * dari kategori yang sama.
     */
    public function serviceShow(Service $service)
    {
        // Muat relasi kategori untuk layanan ini
        $service->load('category');
        // Ambil maksimal 4 layanan terkait dari kategori yang sama (kecuali layanan saat ini)
        $relatedServices = Service::active()
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }

    /**
     * Menampilkan halaman booking (pemesanan) dengan multi-step wizard.
     * Mendukung preselect layanan dari halaman detail layanan.
     */
    public function booking(Request $request)
    {
        // Cek apakah ada layanan yang dipilih dari halaman detail (preselect)
        $serviceId = $request->get('service');
        $preselectedService = $serviceId ? Service::find($serviceId) : null;

        // Ambil semua layanan dan artis aktif untuk ditampilkan di form
        $services = Service::active()->with('category')->orderBy('name')->get();
        $artists = NailArtist::active()->with(['user', 'schedules', 'reviews'])->get();

        // Ambil data step 1 dari session (layanan yang sudah dipilih sebelumnya)
        $step1 = session('booking_step1');

        // Jika ada parameter tanggal dari URL dan step 1 sudah ada, perbarui tanggal
        if ($request->has('date') && $step1) {
            $step1['booking_date'] = $request->get('date');
            session(['booking_step1' => $step1]);
        }

        return view('booking.index', compact(
            'services',
            'artists',
            'preselectedService',
            'step1'
        ));
    }

    /**
     * Menyimpan pilihan layanan pada Step 1 booking ke session.
     * Menghitung total durasi dan harga dari layanan yang dipilih.
     */
    public function storeStep1(Request $request)
    {
        // Validasi: minimal 1 layanan harus dipilih
        $validated = $request->validate([
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'integer|exists:services,id',
        ]);

        // Ambil data layanan dari database berdasarkan ID yang dipilih
        $services = Service::whereIn('id', $validated['service_ids'])->get();
        // Hitung total durasi dari semua layanan yang dipilih
        $totalDuration = $services->sum('duration_minutes');
        // Hitung total harga dari semua layanan yang dipilih
        $totalPrice = $services->sum('price');

        // Susun data step 1 yang akan disimpan ke session
        $step1 = [
            'service_ids' => $validated['service_ids'],
            // Mapping data layanan untuk ditampilkan di ringkasan
            'services' => $services->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'price' => $s->price,
                'duration_minutes' => $s->duration_minutes,
                'category_name' => optional($s->category)->name,
            ])->toArray(),
            'artist_id' => null,
            'booking_date' => now()->addDay()->format('Y-m-d'), // Default: besok
            'start_time' => null,
            'total_duration' => (int) $totalDuration,
            'total_price' => (int) $totalPrice,
            'created_at' => now()->toIso8601String(),
        ];

        // Simpan ke session dan majukan ke step 2 (pilih slot & terapis)
        session([
            'booking_step1' => $step1,
            'booking_current_step' => 2,
        ]);

        return redirect()->route('booking.index')
            ->with('success', 'Layanan berhasil dipilih. Silakan tentukan terapis dan jadwal slot.');
    }

    /**
     * Menampilkan dashboard sesuai peran pengguna.
     * Admin diarahkan ke dashboard admin, artist ke dashboard artist,
     * pelanggan ke dashboard pelanggan.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Jika pengguna adalah admin, tampilkan dashboard admin
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        // Jika pengguna adalah nail artist, redirect ke dashboard artist
        if ($user->isArtist()) {
            return redirect()->route('artist.dashboard');
        }

        // Default: tampilkan dashboard pelanggan
        return $this->customerDashboard();
    }

    /**
     * Menampilkan dashboard khusus admin dengan statistik bisnis.
     * Meliputi jumlah booking hari ini, pending, revenue, dll.
     */
    protected function adminDashboard()
    {
        // Statistik booking hari ini
        $todayBookings = Booking::today()->count();
        // Jumlah booking yang menunggu konfirmasi DP
        $pendingBookings = Booking::pending()->count();
        // Jumlah booking yang sudah dikonfirmasi
        $confirmedBookings = Booking::confirmed()->count();
        // Pendapatan hari ini dari booking yang sudah dikonfirmasi
        $todayRevenue = Booking::confirmed()
            ->today()
            ->sum('total_price');
        // Pendapatan bulan ini dari booking yang sudah dikonfirmasi
        $monthRevenue = Booking::confirmed()
            ->whereMonth('booking_date', now()->month)
            ->whereYear('booking_date', now()->year)
            ->sum('total_price');
        // 8 booking terbaru beserta relasi terkait
        $recentBookings = Booking::with(['customer', 'artist.user', 'details.service'])
            ->latest()
            ->limit(8)
            ->get();
        // Daftar nail artist aktif beserta rata-rata rating
        $artists = NailArtist::active()
            ->with('user')
            ->withAvg('reviews', 'rating')
            ->get();

        return view('dashboard', compact(
            'todayBookings',
            'pendingBookings',
            'confirmedBookings',
            'todayRevenue',
            'monthRevenue',
            'recentBookings',
            'artists'
        ));
    }

    /**
     * Menampilkan dashboard khusus pelanggan dengan riwayat booking,
     * statistik pemesanan, dan jumlah review yang pernah diberikan.
     */
    protected function customerDashboard()
    {
        $user = Auth::user();
        // Ambil semua booking pelanggan beserta relasi terkait, paginasi 5 per halaman
        $myBookings = Booking::with(['artist.user', 'details.service', 'payment', 'payments', 'review'])
            ->where('customer_id', $user->id)
            ->latest()
            ->paginate(5);
        // Hitung jumlah booking yang masih akan datang (pending atau confirmed)
        $upcomingCount = Booking::where('customer_id', $user->id)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->count();
        // Hitung jumlah booking yang sudah selesai
        $completedCount = Booking::where('customer_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();
        // Hitung total pengeluaran pelanggan dari booking yang selesai
        $totalSpent = Booking::where('customer_id', $user->id)
            ->where('status', Booking::STATUS_COMPLETED)
            ->sum('total_price');
        // Hitung jumlah review yang sudah diberikan oleh pelanggan
        $reviewsCount = Review::where('customer_id', $user->id)->count();

        return view('dashboard', compact(
            'myBookings',
            'upcomingCount',
            'completedCount',
            'totalSpent',
            'reviewsCount'
        ));
    }
}
