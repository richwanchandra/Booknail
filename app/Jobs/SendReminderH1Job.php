<?php

// ==========================================================================
// File: SendReminderH1Job.php
// Fungsi: Job antrian untuk mengirim pengingat WhatsApp otomatis H-1
//         (sehari sebelum) jadwal booking. Dijalankan sekali sehari
//         (pukul 09:00) oleh scheduler. Mengirim ke booking PENDING atau
//         CONFIRMED untuk besok yang belum pernah menerima pengingat H-1.
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

class SendReminderH1Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Eksekusi job: cari booking untuk besok dan kirim pengingat H-1 via WA
    public function handle(): void
    {
        // Ambil tanggal besok sebagai target booking yang akan diingatkan
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Ambil semua booking besok dengan status PENDING atau CONFIRMED beserta relasi customer dan artis
        $bookings = Booking::with(['customer', 'artist.user'])
            ->whereDate('booking_date', $tomorrow)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->get();

        // Iterasi setiap booking yang memenuhi kriteria
        foreach ($bookings as $b) {
            // Lewati jika customer tidak memiliki data atau nomor telepon
            if (! $b->customer || ! $b->customer->phone) {
                continue;
            }

            $type = WaLog::TYPE_REMINDER_H1;

            // Cek apakah pengingat H-1 sudah pernah dikirim untuk booking ini
            // Mencegah pengiriman duplikat
            $exists = WaLog::where('booking_id', $b->id)->where('message_type', $type)->exists();
            if ($exists) {
                continue;
            }

            // Gunakan transaksi database untuk menjaga konsistensi data
            DB::beginTransaction();
            try {
                // Bangun pesan pengingat H-1 dari template
                $msg = WaTemplateService::buildMessage($type, [
                    'customer_name' => $b->customer->name,
                    'booking_code' => $b->booking_code,
                    'artist_name' => optional($b->artist->user)->name ?? '-',
                    'date' => $b->formatted_booking_date,
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
