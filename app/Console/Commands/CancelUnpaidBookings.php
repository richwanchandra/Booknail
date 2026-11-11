<?php

namespace App\Console\Commands;

use App\Jobs\SendWaJob;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\WaLog;
use App\Services\WaTemplateService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CancelUnpaidBookings extends Command
{
    protected $signature = 'booking:cancel-unpaid';

    protected $description = 'Auto-cancel booking PENDING setelah 60 menit created_at TANPA DP terverifikasi.';

    public function handle(): int
    {
        $threshold = Carbon::now()->subMinutes(60);

        $toCancel = Booking::where('status', Booking::STATUS_PENDING)
            ->where('created_at', '<=', $threshold)
            ->whereDoesntHave('payments', function ($q) {
                $q->where('payment_type', 'dp')
                    ->where('status', Payment::STATUS_VERIFIED);
            })
            ->get();

        if ($toCancel->count() === 0) {
            $this->info('Tidak ada booking pending yang perlu di-cancel.');

            return self::SUCCESS;
        }

        $ok = 0;
        foreach ($toCancel as $booking) {
            DB::beginTransaction();
            try {
                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'cancel_reason' => 'Auto-cancel oleh sistem setelah 60 menit tanpa verifikasi DP.',
                    'cancelled_at' => now(),
                ]);

                if ($booking->loyalty_points_used > 0 && $booking->customer) {
                    $booking->customer->increment('loyalty_points', $booking->loyalty_points_used);
                }

                if ($booking->customer && $booking->customer->phone) {
                    $msg = WaTemplateService::buildMessage(WaLog::TYPE_BOOKING_CANCELLED, [
                        'customer_name' => $booking->customer->name,
                        'booking_code' => $booking->booking_code,
                        'date' => $booking->formatted_booking_date,
                        'payment_instructions' => 'Auto-cancel 60 menit tanpa verifikasi DP. Silakan booking ulang jika masih berkenan.',
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

                DB::commit();
                $ok++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->error("Gagal cancel booking {$booking->booking_code}: {$e->getMessage()}");
                report($e);
            }
        }

        $this->info("Berhasil cancel {$ok} dari {$toCancel->count()} booking pending expired.");

        return self::SUCCESS;
    }
}
