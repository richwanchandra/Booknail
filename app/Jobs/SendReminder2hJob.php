<?php

// ==========================================================================
// File: SendReminder2hJob.php
// Fungsi: Job antrian untuk mengirim pengingat WhatsApp otomatis 2 jam
//         sebelum jadwal booking. Dijalankan secara berkala (setiap 30 menit)
//         oleh scheduler. Hanya mengirim ke booking CONFIRMED hari ini yang
//         belum pernah menerima pengingat tipe ini.
// ==========================================================================

namespace App\Jobs;

use App\Models\Booking;
use App\Models\WaLog;
use App\Services\WaTemplateService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendReminder2hJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Eksekusi job: cari booking yang dimulai dalam ~2 jam ke depan dan kirim pengingat WA
    public function handle(): void
    {
        $now = Carbon::now();

        // Hitung rentang waktu: 2 jam dari sekarang minus 5 menit hingga plus 15 menit
        // Rentang ini memberi toleransi agar job yang jalan setiap 30 menit tidak melewatkan booking
        $rangeA = (clone $now)->addHours(2)->subMinutes(5);
        $rangeB = (clone $now)->addHours(2)->addMinutes(15);

        // Ambil booking hari ini yang statusnya CONFIRMED dan waktu mulainya dalam rentang 2 jam
        $bookings = Booking::with(['customer', 'artist.user'])
            ->whereDate('booking_date', $now->toDateString())
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereTime('start_time', '>=', $rangeA->format('H:i:s'))
            ->whereTime('start_time', '<=', $rangeB->format('H:i:s'))
            ->get();

        // Iterasi setiap booking yang memenuhi kriteria
        foreach ($bookings as $b) {
            // Lewati jika customer tidak memiliki data atau nomor telepon
            if (! $b->customer || ! $b->customer->phone) {
                continue;
            }

            $type = WaLog::TYPE_REMINDER_2H;

            // Cek apakah pengingat tipe ini sudah pernah dikirim untuk booking ini
            // Mencegah pengiriman duplikat
            $exists = WaLog::where('booking_id', $b->id)->where('message_type', $type)->exists();
            if ($exists) {
                continue;
            }

            // Gunakan transaksi database untuk menjaga konsistensi data
            DB::beginTransaction();
            try {
                // Bangun pesan pengingat dari template
                $msg = WaTemplateService::buildMessage($type, [
                    'customer_name' => $b->customer->name,
                    'booking_code' => $b->booking_code,
                    'artist_name' => optional($b->artist->user)->name ?? '-',
                    'time' => $b->time_range,
                ]);

                // Buat log WhatsApp dengan status QUEUED
                $log = WaLog::create([
                    'booking_id' => $b->id,
                    'phone_number' => $b->customer->phone,
                    'message_type' => $type,
                    'message_body' => $msg,
                    'status' => WaLog::STATUS_QUEUED,
                ]);

                // Dispatch job pengiriman WA secara asinkron
                try {
                    SendWaJob::dispatch($log->id);
                } catch (\Throwable $e) {
                    report($e);
                }

                DB::commit();
            } catch (\Throwable $e) {
                // Rollback jika terjadi error dalam proses pembuatan log atau dispatch
                DB::rollBack();
                report($e);
            }
        }
    }
}
