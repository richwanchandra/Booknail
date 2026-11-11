<?php

// =============================================================================
// AppointmentScheduleController.php
// Controller untuk mengelola jadwal janji temu mingguan (Weekly Appointments Schedule)
// pada dashboard admin Nail Glam Studio.
// Menampilkan jadwal 7 hari (Senin–Minggu) lintas bulan, kapasitas station studio,
// detail klien, teknisi/artist yang ditugaskan, filter multi-mode, dan KPI mingguan.
// =============================================================================

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistLeave;
use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\NailArtist;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentScheduleController extends Controller
{
    /**
     * Menampilkan jadwal janji temu mingguan untuk admin.
     * Mendukung navigasi lintas bulan, filter artist, status, dan mode tampilan (Week, Day, Month, List).
     */
    public function index(Request $request)
    {
        // 1. Tentukan tanggal acuan minggu (default ke hari ini jika tidak ditentukan)
        $dateParam = $request->get('week') ?? $request->get('date');
        try {
            $selectedDate = $dateParam ? Carbon::parse($dateParam) : today();
        } catch (\Throwable $e) {
            $selectedDate = today();
        }

        // Tentukan batas awal (Senin) dan akhir (Minggu) dari minggu yang dipilih
        $startOfWeek = $selectedDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $selectedDate->copy()->endOfWeek(Carbon::SUNDAY);

        // Parameter navigasi minggu sebelumnya, berikutnya, dan hari ini
        $prevWeek = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $startOfWeek->copy()->addWeek()->format('Y-m-d');
        $todayWeek = today()->format('Y-m-d');

        // Parameter filter & pencarian
        $artistId = $request->get('artist_id');
        $status = $request->get('status');
        $search = $request->get('q');
        $viewMode = $request->get('view', 'week'); // week, day, month, list

        // Format judul rentang minggu (misal: "14 – 20 Oktober 2024" atau "28 Oktober – 03 November 2024" lintas bulan)
        $sameMonth = $startOfWeek->month === $endOfWeek->month;
        if ($sameMonth) {
            $weekRangeText = $startOfWeek->locale('id')->isoFormat('D') . ' – ' . $endOfWeek->locale('id')->isoFormat('D MMMM Y');
        } else {
            $weekRangeText = $startOfWeek->locale('id')->isoFormat('D MMM') . ' – ' . $endOfWeek->locale('id')->isoFormat('D MMM Y');
        }

        // 2. Ambil seluruh data teknisi (nail artist) aktif untuk filter & roster
        $artists = NailArtist::with('user')->active()->orderBy('id')->get();
        $totalArtistsCount = $artists->count();

        // 3. Query pemesanan (bookings) untuk 7 hari dalam rentang minggu ini
        $bookingsQuery = Booking::with(['customer', 'artist.user', 'bookingDetails.service', 'payments'])
            ->whereBetween('booking_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->when($artistId, fn ($q) => $q->where('artist_id', $artistId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('booking_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($cu) => $cu->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
                });
            })
            ->orderBy('start_time', 'asc');

        $weekBookings = $bookingsQuery->get();

        // 4. Kalkulasi Kapasitas & Breakdown per Hari (Senin s/d Minggu)
        // Standar studio: 4 station kerja, jam operasional 09:00 - 18:00 (9 jam = 540 menit per station)
        // Total kapasitas studio per hari = 4 station * 540 menit = 2.160 menit
        $studioStationsCount = 4;
        $dailyMaxCapacityMinutes = $studioStationsCount * 540;

        $days = [];
        $peakDayName = '-';
        $peakDayCapacity = 0;
        $peakDayBookingsCount = 0;

        for ($i = 0; $i < 7; $i++) {
            $dayDate = $startOfWeek->copy()->addDays($i);
            $dayDateStr = $dayDate->format('Y-m-d');

            // Ambil booking khusus hari tersebut
            $dayBookings = $weekBookings->filter(fn ($b) => $b->booking_date->format('Y-m-d') === $dayDateStr)->values();

            // Hitung total durasi layanan yang dipesan pada hari ini
            $totalBookedMinutes = $dayBookings->sum(function ($b) {
                return $b->bookingDetails->sum('duration_minutes') ?: 60;
            });

            // Persentase kapasitas station terisi (maks 100%)
            $capacityPct = min(100, round(($totalBookedMinutes / max(1, $dailyMaxCapacityMinutes)) * 100));

            // Petakan station untuk setiap booking (Station 1 - 4)
            foreach ($dayBookings as $b) {
                // Tentukan nomor station (1-4) berdasarkan ID artist atau penugasan
                $stationNum = (($b->artist_id - 1) % $studioStationsCount) + 1;
                $b->assigned_station = "Station " . $stationNum;
                $b->station_number = $stationNum;
            }

            if ($capacityPct > $peakDayCapacity || ($capacityPct === $peakDayCapacity && $dayBookings->count() > $peakDayBookingsCount)) {
                $peakDayCapacity = $capacityPct;
                $peakDayBookingsCount = $dayBookings->count();
                $peakDayName = $dayDate->locale('id')->isoFormat('dddd') . " ({$capacityPct}%)";
            }

            $days[] = [
                'date' => $dayDateStr,
                'carbon' => $dayDate,
                'day_name' => $dayDate->locale('id')->isoFormat('dddd'),
                'day_short' => $dayDate->locale('id')->isoFormat('ddd'),
                'day_num' => $dayDate->format('d'),
                'month_short' => $dayDate->locale('id')->isoFormat('MMM'),
                'is_today' => $dayDate->isToday(),
                'bookings' => $dayBookings,
                'bookings_count' => $dayBookings->count(),
                'total_minutes' => $totalBookedMinutes,
                'capacity_pct' => $capacityPct,
            ];
        }

        // 5. Kalkulasi KPI Mingguan (Weekly KPI Snapshot Bar)
        $totalBookings = $weekBookings->count();
        $confirmedCount = $weekBookings->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_IN_PROGRESS, Booking::STATUS_COMPLETED])->count();
        $pendingDpCount = $weekBookings->where('status', Booking::STATUS_PENDING)->count();
        $inProgressCount = $weekBookings->where('status', Booking::STATUS_IN_PROGRESS)->count();
        $completedCount = $weekBookings->where('status', Booking::STATUS_COMPLETED)->count();
        $cancelledCount = $weekBookings->where('status', Booking::STATUS_CANCELLED)->count();
        $totalRevenue = $weekBookings->where('status', Booking::STATUS_COMPLETED)->sum('total_price');

        // Ketersediaan Staff Roster minggu ini (cek berapa artist yang aktif & tidak cuti hari ini)
        $onLeaveTodayCount = ArtistLeave::whereDate('leave_date', today())->count();
        $staffRosterAvailable = max(0, $totalArtistsCount - $onLeaveTodayCount);

        $kpi = [
            'total_bookings' => $totalBookings,
            'confirmed' => $confirmedCount,
            'pending_dp' => $pendingDpCount,
            'in_progress' => $inProgressCount,
            'completed' => $completedCount,
            'cancelled' => $cancelledCount,
            'staff_roster' => "{$staffRosterAvailable}/{$totalArtistsCount}",
            'peak_day' => $peakDayName !== '-' ? $peakDayName : ($days[0]['day_name'] . ' (0%)'),
            'total_revenue' => $totalRevenue,
        ];

        // Layanan aktif untuk modal quick booking / tambah janji
        $services = Service::active()->orderBy('name')->get();

        return view('admin.appointments.index', compact(
            'days',
            'weekBookings',
            'artists',
            'services',
            'kpi',
            'startOfWeek',
            'endOfWeek',
            'selectedDate',
            'prevWeek',
            'nextWeek',
            'todayWeek',
            'weekRangeText',
            'artistId',
            'status',
            'search',
            'viewMode',
            'studioStationsCount'
        ));
    }

    /**
     * Memblokir slot jadwal atau mencatat cuti/libur artist secara cepat dari halaman jadwal.
     */
    public function blockSlot(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
            'leave_type' => 'nullable|in:annual,sick,personal,other',
        ]);

        ArtistLeave::updateOrCreate(
            [
                'artist_id' => $validated['artist_id'],
                'leave_date' => $validated['date'],
            ],
            [
                'reason' => $validated['reason'],
                'leave_type' => $validated['leave_type'] ?? 'personal',
                'is_paid' => true,
            ]
        );

        return back()->with('success', 'Slot jadwal berhasil diblokir / cuti teknisi berhasil dicatat.');
    }
}

