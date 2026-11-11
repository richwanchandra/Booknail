<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendFcmJob;
use App\Jobs\SendWaJob;
use App\Models\Booking;
use App\Models\NailArtist;
use App\Models\Payment;
use App\Models\Service;
use App\Models\WaLog;
use App\Services\CascadeScheduleShiftService;
use App\Services\WaTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $artistId = $request->get('artist_id');
        $date = $request->get('booking_date');
        $search = $request->get('q');
        $source = $request->get('source');
        $branch = $request->get('branch');
        $timeSlot = $request->get('time_slot');

        $bookings = Booking::with(['customer', 'artist.user', 'bookingDetails.service', 'payments'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($artistId, fn ($q) => $q->where('artist_id', $artistId))
            ->when($date, fn ($q) => $q->whereDate('booking_date', $date))
            ->when($branch, fn ($q) => $q->where('branch', $branch))
            ->when($source === 'walk_in', fn ($q) => $q->walkIn())
            ->when($source === 'online', fn ($q) => $q->online())
            ->when($timeSlot, fn ($q) => $q->where('start_time', 'like', "{$timeSlot}%"))
            ->when($search, fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cu) => $cu->where('name', 'like', "%{$search}%"));
            }))
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        $artists = NailArtist::with('user')->active()->orderBy('id')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $statusOptions = [
            Booking::STATUS_PENDING => 'Pending (Menunggu DP)',
            Booking::STATUS_CONFIRMED => 'Dikonfirmasi',
            Booking::STATUS_IN_PROGRESS => 'Sedang Berlangsung',
            Booking::STATUS_COMPLETED => 'Selesai',
            Booking::STATUS_CANCELLED => 'Dibatalkan',
            Booking::STATUS_RESCHEDULED => 'Reschedule',
        ];

        $counts = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'confirmed' => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
            'today' => Booking::whereDate('booking_date', today())->count(),
            'completed' => Booking::where('status', Booking::STATUS_COMPLETED)->count(),
            'walk_in' => Booking::walkIn()->count(),
            'online' => Booking::online()->count(),
            'revenue' => Booking::where('status', Booking::STATUS_COMPLETED)->sum('total_price'),
        ];

        return view('admin.bookings.index', compact('bookings', 'artists', 'services', 'statusOptions', 'status', 'artistId', 'date', 'search', 'source', 'branch', 'timeSlot', 'counts'));
    }

    public function approvePayment(Request $request, Booking $booking)
    {
        abort_unless($booking->status === Booking::STATUS_PENDING, 400, 'Hanya booking PENDING yang bisa diapprove DP.');
        
        $pendingDp = $booking->payments()->where('payment_type', 'dp')->where('status', Payment::STATUS_PENDING)->first();
        
        // VALIDASI: Pelanggan wajib mengunggah bukti pembayaran sebelum pesanan dapat dikonfirmasi booking
        if (! $pendingDp || empty($pendingDp->proof_file)) {
            return back()->withErrors([
                'payment_proof' => "Pesanan #{$booking->booking_code} TIDAK DAPAT DIKONFIRMASI karena pelanggan belum mengunggah bukti pembayaran DP."
            ]);
        }

        DB::beginTransaction();
        try {
            $pendingDp->update([
                'status' => Payment::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);
            $booking->update(['status' => Booking::STATUS_CONFIRMED]);

            if ($booking->customer && $booking->customer->phone) {
                $msg = WaTemplateService::buildMessage(WaLog::TYPE_DP_CONFIRMED, [
                    'customer_name' => $booking->customer->name,
                    'booking_code' => $booking->booking_code,
                    'artist_name' => optional($booking->artist->user)->name ?? '-',
                    'date' => $booking->formatted_booking_date,
                    'time' => $booking->time_range,
                ]);
                $log = WaLog::create([
                    'booking_id' => $booking->id,
                    'phone_number' => $booking->customer->phone,
                    'message_type' => WaLog::TYPE_DP_CONFIRMED,
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
            // FCM push notification to customer
            if ($booking->customer_id) {
                try {
                    $dashboardUrl = route('dashboard');
                    SendFcmJob::dispatchAfterResponse(
                        $booking->customer_id,
                        false,
                        '✅ Pembayaran DP Dikonfirmasi!',
                        "Pembayaran DP booking {$booking->booking_code} telah diverifikasi. Jadwal Anda pada {$booking->formatted_booking_date} ({$booking->time_range}) telah dikonfirmasi!",
                        [
                            'booking_code' => $booking->booking_code,
                            'url' => $dashboardUrl,
                            'click_action' => $dashboardUrl,
                        ]
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['a' => 'Approve gagal: '.$e->getMessage()]);
        }

        return back()->with('success', "DP Booking {$booking->booking_code} diverifikasi. Status booking menjadi CONFIRMED.");
    }

    public function rejectPayment(Request $request, Booking $booking)
    {
        abort_unless($booking->status === Booking::STATUS_PENDING, 400, 'Hanya booking PENDING yang bisa reject DP.');
        $v = $request->validate(['reject_reason' => 'nullable|string|max:500']);
        $pendingDp = $booking->payments()->where('payment_type', 'dp')->where('status', Payment::STATUS_PENDING)->first();
        if ($pendingDp) {
            $pendingDp->update(['status' => Payment::STATUS_REJECTED, 'verified_by' => Auth::id(), 'verified_at' => now()]);
        }

        // FCM push to customer about rejected payment
        if ($booking->customer_id) {
            try {
                $dashboardUrl = route('dashboard');
                $reasonText = $v['reject_reason'] ?? 'Bukti transfer tidak sesuai atau tidak terbaca.';
                SendFcmJob::dispatchAfterResponse(
                    $booking->customer_id,
                    false,
                    '⚠️ Bukti Pembayaran Perlu Diperiksa',
                    "Pembayaran DP booking {$booking->booking_code} belum dapat diverifikasi ({$reasonText}). Silakan upload ulang bukti transfer Anda.",
                    [
                        'booking_code' => $booking->booking_code,
                        'url' => $dashboardUrl,
                        'click_action' => $dashboardUrl,
                    ]
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('warning', 'Pembayaran DP ditolak. Silakan hubungi customer untuk upload ulang.');
    }

    public function setInProgress(Booking $booking)
    {
        abort_unless($booking->status === Booking::STATUS_CONFIRMED, 400, 'Hanya booking CONFIRMED yang bisa mulai.');
        $booking->update(['status' => Booking::STATUS_IN_PROGRESS]);

        // FCM push to customer
        if ($booking->customer_id) {
            try {
                $dashboardUrl = route('dashboard');
                SendFcmJob::dispatchAfterResponse(
                    $booking->customer_id,
                    false,
                    '💅 Treatment Sedang Berlangsung',
                    "Sesi nail art Anda untuk booking {$booking->booking_code} sedang berlangsung. Selamat menikmati treatment!",
                    [
                        'booking_code' => $booking->booking_code,
                        'url' => $dashboardUrl,
                        'click_action' => $dashboardUrl,
                    ]
                );
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Status booking IN PROGRESS.');
    }

    public function setCompleted(Booking $booking)
    {
        abort_unless($booking->status === Booking::STATUS_IN_PROGRESS, 400, 'Hanya booking IN PROGRESS yang bisa complete.');

        DB::beginTransaction();
        try {
            $booking->update(['status' => Booking::STATUS_COMPLETED, 'completed_at' => now()]);

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
            // FCM push to customer
            if ($booking->customer_id) {
                $pointsMsg = $earned > 0 ? " Anda mendapat +{$earned} poin loyalty!" : '';
                try {
                    $dashboardUrl = route('dashboard');
                    SendFcmJob::dispatchAfterResponse(
                        $booking->customer_id,
                        false,
                        '✨ Treatment Selesai! Terima Kasih',
                        "Booking {$booking->booking_code} telah selesai.{$pointsMsg} Terima kasih telah mempercayakan kuku Anda di Nail Glam Studio!",
                        [
                            'booking_code' => $booking->booking_code,
                            'url' => $dashboardUrl,
                            'click_action' => $dashboardUrl,
                        ]
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['a' => 'Complete gagal: '.$e->getMessage()]);
        }

        $msgExtra = $earned > 0 ? " Loyalty +{$earned} poin diberikan." : '';

        return back()->with('success', "Booking {$booking->booking_code} SELESAI.{$msgExtra} Terima kasih!");
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        abort_if(in_array($booking->status, [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETED], true), 400, 'Booking ini tidak bisa dibatalkan lagi.');
        $reason = $request->input('reason', 'Dibatalkan oleh Admin.');

        DB::beginTransaction();
        try {
            $booking->update(['status' => Booking::STATUS_CANCELLED, 'cancel_reason' => $reason, 'cancelled_at' => now()]);

            if ($booking->loyalty_points_used > 0 && $booking->customer) {
                $booking->customer->increment('loyalty_points', $booking->loyalty_points_used);
            }

            if ($booking->customer && $booking->customer->phone) {
                $msg = WaTemplateService::buildMessage(WaLog::TYPE_BOOKING_CANCELLED, [
                    'customer_name' => $booking->customer->name,
                    'booking_code' => $booking->booking_code,
                    'date' => $booking->formatted_booking_date,
                    'payment_instructions' => $reason,
                ]);
                $log = WaLog::create([
                    'booking_id' => $booking->id,
                    'phone_number' => $booking->customer->phone,
                    'message_type' => WaLog::TYPE_BOOKING_CANCELLED,
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
            // FCM push to customer
            if ($booking->customer_id) {
                try {
                    $dashboardUrl = route('dashboard');
                    SendFcmJob::dispatchAfterResponse(
                        $booking->customer_id,
                        false,
                        '🚫 Booking Dibatalkan',
                        "Booking {$booking->booking_code} telah dibatalkan. Alasan: {$reason}",
                        [
                            'booking_code' => $booking->booking_code,
                            'url' => $dashboardUrl,
                            'click_action' => $dashboardUrl,
                        ]
                    );
                } catch (\Throwable $e) {
                    report($e);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['a' => 'Cancel gagal: '.$e->getMessage()]);
        }

        $refundMsg = $booking->loyalty_points_used > 0 ? " Loyalty {$booking->loyalty_points_used} poin dikembalikan." : '';

        return back()->with('warning', "Booking {$booking->booking_code} DIBATALKAN.{$refundMsg}");
    }

    public function previewUpgrade(Request $request, Booking $booking, CascadeScheduleShiftService $service)
    {
        $validated = $request->validate([
            'additional_minutes' => 'required|integer|min:5|max:240',
        ]);

        $preview = $service->previewShift($booking, (int) $validated['additional_minutes']);

        return response()->json($preview);
    }

    public function applyUpgrade(Request $request, Booking $booking, CascadeScheduleShiftService $service)
    {
        $validated = $request->validate([
            'additional_minutes' => 'required|integer|min:5|max:240',
            'additional_price' => 'nullable|integer|min:0',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'upgrade_notes' => 'nullable|string|max:500',
            'notify_customers' => 'nullable|boolean',
        ]);

        try {
            $result = $service->applyUpgradeAndShift($booking, $validated);

            $shiftedCount = $result['shifted_count'];
            $msg = "Upgrade durasi (+{$validated['additional_minutes']} mnt) untuk booking {$booking->booking_code} berhasil diterapkan.";

            if ($shiftedCount > 0) {
                $msg .= " {$shiftedCount} antrean berikutnya berhasil digeser secara otomatis dan notifikasi keterlambatan telah dikirimkan.";
            } else {
                $msg .= " Tidak ada antrean berikutnya yang terdampak.";
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['error' => 'Gagal menerapkan upgrade layanan: '.$e->getMessage()]);
        }
    }

    public function testNotification(\App\Services\FcmService $fcm)
    {
        $user = Auth::user();
        $tokens = $user->fcmTokens()->pluck('token')->unique()->toArray();

        if (empty($tokens)) {
            return response()->json([
                'success' => false,
                'message' => 'Token Chrome belum terdaftar. Silakan izinkan notifikasi pada banner di atas terlebih dahulu.',
            ], 422);
        }

        $confirmUrl = route('admin.bookings.index');
        $fcm->send(
            $tokens,
            '🔔 Tes Pop-Up Notifikasi Chrome!',
            'Notifikasi Chrome siap menerima order booking! Klik notifikasi ini untuk membuka halaman konfirmasi reservasi.',
            [
                'url' => $confirmUrl,
                'click_action' => $confirmUrl,
                'booking_code' => 'TEST-'.rand(100, 999),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi tes berhasil dikirim ke Chrome Anda!',
        ]);
    }

    public function checkNewBookings(Request $request)
    {
        $since = $request->get('since');
        $query = Booking::with('customer')
            ->where('status', Booking::STATUS_PENDING);

        if ($since) {
            try {
                $sinceDate = \Carbon\Carbon::parse($since);
                $query->where('created_at', '>', $sinceDate);
            } catch (\Throwable $e) {
                $query->where('created_at', '>=', now()->subSeconds(30));
            }
        } else {
            // Initialize with current time if no since param
            return response()->json([
                'has_new' => false,
                'server_time' => now()->toIso8601String(),
            ]);
        }

        $latest = $query->latest('id')->first();

        if ($latest) {
            $customerName = optional($latest->customer)->name ?? $latest->customer_name ?? 'Customer';
            $confirmUrl = route('admin.bookings.index', ['q' => $latest->booking_code]);

            return response()->json([
                'has_new' => true,
                'server_time' => now()->toIso8601String(),
                'booking' => [
                    'id' => $latest->id,
                    'booking_code' => $latest->booking_code,
                    'customer_name' => $customerName,
                    'date' => $latest->formatted_booking_date ?? $latest->booking_date,
                    'time' => $latest->start_time,
                    'created_at' => $latest->created_at->toIso8601String(),
                    'url' => $confirmUrl,
                ],
            ]);
        }

        return response()->json([
            'has_new' => false,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
