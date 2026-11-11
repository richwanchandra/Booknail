<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Jobs\SendWaJob;
use App\Models\Booking;
use App\Models\NailArtist;
use App\Models\Review;
use App\Models\WaLog;
use App\Services\WaTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected function getArtist()
    {
        $user = Auth::user();
        $artist = $user->nailArtist;

        if (! $artist && $user->isAdmin()) {
            $artist = NailArtist::first();
        }

        abort_unless($artist, 404, 'Profil Nail Artist tidak ditemukan untuk akun ini.');

        return $artist;
    }

    public function index(Request $request)
    {
        $artist = $this->getArtist();

        // 1. Antrean Hari Ini
        $todayBookings = Booking::forArtist($artist->id)
            ->whereDate('booking_date', today())
            ->with(['customer', 'bookingDetails.service'])
            ->orderBy('start_time')
            ->get();

        // 2. Janji Temu Mendatang (H+1 dst)
        $upcomingBookings = Booking::forArtist($artist->id)
            ->whereDate('booking_date', '>', today())
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->with(['customer', 'bookingDetails.service'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->take(10)
            ->get();

        // 3. Review & Rating Masuk
        $reviews = Review::where('artist_id', $artist->id)
            ->with(['customer', 'booking'])
            ->latest()
            ->paginate(8);

        // 4. Statistik Kinerja Teknisi
        $totalReviews = Review::where('artist_id', $artist->id)->count();
        $avgRating = (float) (Review::where('artist_id', $artist->id)->avg('rating') ?: 5.0);

        $starCounts = [
            5 => Review::where('artist_id', $artist->id)->where('rating', 5)->count(),
            4 => Review::where('artist_id', $artist->id)->where('rating', 4)->count(),
            3 => Review::where('artist_id', $artist->id)->where('rating', 3)->count(),
            2 => Review::where('artist_id', $artist->id)->where('rating', 2)->count(),
            1 => Review::where('artist_id', $artist->id)->where('rating', 1)->count(),
        ];

        $pendingConfirmCount = Booking::forArtist($artist->id)
            ->whereDate('booking_date', today())
            ->where('artist_confirmation_status', 'pending')
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->count();

        $inProgressCount = Booking::forArtist($artist->id)
            ->where('status', Booking::STATUS_IN_PROGRESS)
            ->count();

        $completedTodayCount = Booking::forArtist($artist->id)
            ->whereDate('booking_date', today())
            ->where('status', Booking::STATUS_COMPLETED)
            ->count();

        // 5. Notifikasi Khusus Teknisi (Alert & Notification Center)
        $recentNotifications = collect();

        // Notifikasi booking yang perlu konfirmasi segera
        $needsConfirm = Booking::forArtist($artist->id)
            ->whereDate('booking_date', today())
            ->where('artist_confirmation_status', 'pending')
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->with(['customer', 'bookingDetails.service'])
            ->get();

        foreach ($needsConfirm as $b) {
            $recentNotifications->push([
                'type' => 'confirm',
                'title' => 'Perlu Konfirmasi Janji Temu',
                'body' => "Tamu {$b->customer_display_name} ({$b->time_range})",
                'code' => $b->booking_code,
                'time' => $b->created_at ? $b->created_at->diffForHumans() : 'Hari ini',
                'url' => '#booking-card-'.$b->id,
                'urgent' => true,
            ]);
        }

        // Notifikasi ulasan terbaru masuk
        $latestReviews = Review::where('artist_id', $artist->id)
            ->with('customer')
            ->latest()
            ->take(3)
            ->get();

        foreach ($latestReviews as $rev) {
            $recentNotifications->push([
                'type' => 'review',
                'title' => 'Ulasan Baru (' . str_repeat('★', $rev->rating) . ')',
                'body' => $rev->comment ? '"' . \Illuminate\Support\Str::limit($rev->comment, 40) . '"' : 'Ulasan bintang ' . $rev->rating . ' dari pelanggan',
                'code' => null,
                'time' => $rev->created_at ? $rev->created_at->diffForHumans() : 'Baru saja',
                'url' => '#reviews',
                'urgent' => false,
            ]);
        }

        return view('artist.dashboard', compact(
            'artist',
            'todayBookings',
            'upcomingBookings',
            'reviews',
            'totalReviews',
            'avgRating',
            'starCounts',
            'pendingConfirmCount',
            'inProgressCount',
            'completedTodayCount',
            'recentNotifications'
        ));
    }

    public function confirm(Booking $booking)
    {
        $artist = $this->getArtist();
        abort_unless(Auth::user()->isAdmin() || (int) $booking->artist_id === (int) $artist->id, 403, 'Akses ditolak.');

        $booking->update([
            'artist_confirmation_status' => 'confirmed',
            'artist_confirmed_at' => now(),
            'artist_notes' => null,
        ]);

        return back()->with('success', "Jadwal booking {$booking->booking_code} berhasil Anda konfirmasi dan slot tersinkronisasi.");
    }

    public function reject(Request $request, Booking $booking)
    {
        $artist = $this->getArtist();
        abort_unless(Auth::user()->isAdmin() || (int) $booking->artist_id === (int) $artist->id, 403, 'Akses ditolak.');

        $v = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'artist_confirmation_status' => 'rejected',
            'artist_notes' => $v['reject_reason'],
        ]);

        return back()->with('warning', "Laporan jadwal bentrok/halangan untuk booking {$booking->booking_code} telah disimpan dan diteruskan ke admin studio.");
    }

    public function start(Booking $booking)
    {
        $artist = $this->getArtist();
        abort_unless(Auth::user()->isAdmin() || (int) $booking->artist_id === (int) $artist->id, 403, 'Akses ditolak.');

        $booking->update([
            'status' => Booking::STATUS_IN_PROGRESS,
            'artist_confirmation_status' => 'confirmed',
            'artist_confirmed_at' => $booking->artist_confirmed_at ?: now(),
        ]);

        return back()->with('success', "Status pengerjaan booking {$booking->booking_code} kini SEDANG BERLANGSUNG.");
    }

    public function complete(Booking $booking)
    {
        $artist = $this->getArtist();
        abort_unless(Auth::user()->isAdmin() || (int) $booking->artist_id === (int) $artist->id, 403, 'Akses ditolak.');

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => Booking::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            $earned = (int) floor($booking->total_price / 10000);
            if ($earned > 0 && $booking->customer) {
                $booking->customer->addLoyaltyPoints($earned);
            }

            if ($booking->customer && $booking->customer->phone) {
                $msg = WaTemplateService::buildMessage(WaLog::TYPE_BOOKING_COMPLETED, [
                    'customer_name' => $booking->customer->name,
                    'booking_code' => $booking->booking_code,
                    'total' => $booking->formatted_total_price,
                ]);
                $log = WaLog::create([
                    'booking_id' => $booking->id,
                    'phone_number' => $booking->customer->phone,
                    'message_type' => WaLog::TYPE_BOOKING_COMPLETED,
                    'message_body' => $msg,
                    'status' => WaLog::STATUS_QUEUED,
                ]);
                if (class_exists(SendWaJob::class)) {
                    try {
                        SendWaJob::dispatch($log->id);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['err' => 'Gagal menyelesaikan booking: '.$e->getMessage()]);
        }

        return back()->with('success', "Perawatan booking {$booking->booking_code} selesai! Pelanggan kini dapat memberikan ulasan dan rating.");
    }

    public function toggleDuty(Request $request)
    {
        $artist = $this->getArtist();
        $newStatus = ! $artist->is_active;
        $artist->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'SIAP MELAYANI (ON DUTY)' : 'SEDANG ISTIRAHAT / OFF';

        return back()->with('success', "Status ketersediaan Anda diubah menjadi: {$statusText}.");
    }

    /**
     * Endpoint polling AJAX untuk mendeteksi penugasan janji temu baru secara real-time
     * serta memicu audio chime & browser push notification pada dashboard teknisi.
     */
    public function checkNewBookings(Request $request)
    {
        $artist = $this->getArtist();
        $since = $request->get('since');

        $query = Booking::forArtist($artist->id)
            ->whereDate('booking_date', '>=', today())
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING, Booking::STATUS_IN_PROGRESS]);

        if ($since) {
            try {
                $sinceDate = \Carbon\Carbon::parse($since);
                $query->where('created_at', '>', $sinceDate);
            } catch (\Throwable $e) {
                $query->where('created_at', '>=', now()->subSeconds(30));
            }
        } else {
            return response()->json([
                'has_new' => false,
                'pending_confirm_count' => Booking::forArtist($artist->id)
                    ->whereDate('booking_date', today())
                    ->where('artist_confirmation_status', 'pending')
                    ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                    ->count(),
                'server_time' => now()->toIso8601String(),
            ]);
        }

        $newBookings = $query->with(['customer', 'bookingDetails.service'])->get();

        $pendingConfirmCount = Booking::forArtist($artist->id)
            ->whereDate('booking_date', today())
            ->where('artist_confirmation_status', 'pending')
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->count();

        return response()->json([
            'has_new' => $newBookings->isNotEmpty(),
            'count' => $newBookings->count(),
            'pending_confirm_count' => $pendingConfirmCount,
            'server_time' => now()->toIso8601String(),
            'bookings' => $newBookings->map(function ($b) {
                $detail = $b->bookingDetails->first();
                return [
                    'id' => $b->id,
                    'code' => $b->booking_code,
                    'customer_name' => $b->customer_display_name,
                    'service_name' => $detail->service->name ?? 'Layanan Nail Art',
                    'time_range' => $b->time_range,
                    'date' => $b->formatted_booking_date,
                    'confirm_url' => route('artist.dashboard'),
                ];
            }),
        ]);
    }
}
