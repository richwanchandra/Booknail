<?php

// ==========================================================================
// File: CascadeScheduleShiftService.php
// Fungsi: Service untuk menangani efek domino (cascading schedule shift)
//         ketika durasi suatu booking diperpanjang. Booking-booking setelahnya
//         akan otomatis bergeser waktunya agar tidak tumpang tindih.
//         Menyediakan dua metode utama: preview (pratinjau) dan apply (eksekusi).
// ==========================================================================

namespace App\Services;

use App\Jobs\SendWaJob;
use App\Models\ArtistSchedule;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Service;
use App\Models\WaLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CascadeScheduleShiftService
{
    /**
     * Preview the ripple effect (cascading schedule shifts) of extending a booking's duration.
     * Does NOT save anything to database.
     */
    // Metode ini menghitung simulasi pergeseran jadwal secara read-only tanpa menyimpan ke database.
    // Berguna untuk menampilkan konfirmasi ke admin sebelum benar-benar menggeser jadwal.
    public function previewShift(Booking $booking, int $additionalMinutes): array
    {
        // Ambil tanggal booking dalam format string (Y-m-d)
        $date = $booking->booking_date->toDateString();

        // Parse waktu selesai booking saat ini menjadi objek Carbon
        $currentEndCarbon = Carbon::parse("{$date} {$booking->end_time}");

        // Hitung waktu selesai baru setelah ditambah durasi ekstra
        $newEndCarbon = (clone $currentEndCarbon)->addMinutes($additionalMinutes);

        // Get artist's shift end for today
        // Ambil hari dalam angka (0=Minggu, 1=Senin, dst.) untuk mencari jadwal shift artis
        $dayOfWeek = (int) $booking->booking_date->dayOfWeek;

        // Query jadwal kerja artis pada hari tersebut
        $schedule = ArtistSchedule::where('artist_id', $booking->artist_id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Gunakan jam selesai shift artis, dibatasi maksimal jam tutup operasional studio (19:00 WIB)
        $studioCloseHour = '19:00';
        $artistShiftEnd = $schedule ? substr($schedule->end_time, 0, 5) : $studioCloseHour;
        if ($artistShiftEnd > $studioCloseHour) {
            $artistShiftEnd = $studioCloseHour;
        }
        $shiftEndCarbon = Carbon::parse("{$date} {$artistShiftEnd}:00");
        $studioCloseCarbon = Carbon::parse("{$date} {$studioCloseHour}:00");

        // Query subsequent active bookings for the same artist on the same date
        // Ambil semua booking aktif milik artis yang sama pada tanggal yang sama
        // yang dimulai setelah booking saat ini (yang akan diperpanjang)
        $subsequentBookings = Booking::with('customer')
            ->where('artist_id', $booking->artist_id)
            ->whereDate('booking_date', $booking->booking_date)
            ->where('id', '!=', $booking->id)
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_IN_PROGRESS,
                Booking::STATUS_RESCHEDULED,
            ])
            ->where('start_time', '>=', $booking->start_time)
            ->orderBy('start_time', 'asc')
            ->get();

        // Array penampung booking yang terdampak pergeseran
        $affectedBookings = [];

        // rollingEnd melacak waktu selesai terakhir yang digunakan untuk menentukan waktu mulai booking berikutnya
        $rollingEnd = clone $newEndCarbon;

        // Flag apakah ada booking yang melampaui jam shift artis
        $hasExceededShift = false;

        // Iterasi setiap booking berikutnya untuk mensimulasikan pergeseran waktu
        foreach ($subsequentBookings as $sub) {
            // Parse waktu mulai dan selesai booking berikutnya
            $subStart = Carbon::parse("{$date} {$sub->start_time}");
            $subEnd = Carbon::parse("{$date} {$sub->end_time}");

            // Hitung durasi asli booking ini (akan dipertahankan saat bergeser)
            $durationMinutes = $subStart->diffInMinutes($subEnd);

            // Jika waktu mulai booking ini lebih awal dari rollingEnd, perlu digeser
            if ($subStart->lt($rollingEnd)) {
                // Geser waktu mulai ke posisi rollingEnd
                $newSubStart = clone $rollingEnd;
                $newSubEnd = (clone $newSubStart)->addMinutes($durationMinutes);

                // Check delay against original start time if exists
                // Hitung delay terhadap waktu mulai asli (original_start_time) jika sudah pernah digeser sebelumnya
                $origStart = $sub->original_start_time
                    ? Carbon::parse("{$date} {$sub->original_start_time}")
                    : $subStart;
                $delayMinutes = $origStart->diffInMinutes($newSubStart);

                // Periksa apakah waktu selesai baru melampaui batas shift artis
                $exceedsShift = $newSubEnd->gt($shiftEndCarbon);
                if ($exceedsShift) {
                    $hasExceededShift = true;
                }

                // Kumpulkan data booking yang terdampak untuk ditampilkan di preview
                $affectedBookings[] = [
                    'booking_id' => $sub->id,
                    'booking_code' => $sub->booking_code,
                    'customer_name' => $sub->customer_display_name,
                    'customer_phone' => $sub->customer_display_phone,
                    'original_start_time' => substr($sub->original_start_time ?? $sub->start_time, 0, 5),
                    'current_start_time' => substr($sub->start_time, 0, 5),
                    'current_end_time' => substr($sub->end_time, 0, 5),
                    'new_start_time' => $newSubStart->format('H:i'),
                    'new_end_time' => $newSubEnd->format('H:i'),
                    'delay_minutes' => $delayMinutes,
                    'exceeds_artist_shift' => $exceedsShift,
                ];

                // Update rollingEnd agar booking selanjutnya mengacu pada waktu selesai baru ini
                $rollingEnd = clone $newSubEnd;
            } else {
                // Buffer absorbs remaining delay
                // Jarak waktu (buffer) cukup besar, jadi pergeseran tidak berdampak lagi
                $rollingEnd = clone $subEnd;
            }
        }

        // Kembalikan hasil simulasi preview dalam bentuk array
        return [
            'current_booking' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'customer_name' => $booking->customer_display_name,
                'current_start_time' => substr($booking->start_time, 0, 5),
                'current_end_time' => substr($booking->end_time, 0, 5),
                'new_end_time' => $newEndCarbon->format('H:i'),
                'additional_minutes' => $additionalMinutes,
            ],
            'shifted_bookings' => $affectedBookings,
            'total_affected' => count($affectedBookings),
            'has_shift_exceeded' => $hasExceededShift,
            'artist_shift_end' => $artistShiftEnd,
        ];
    }

    /**
     * Apply service upgrade / duration extension and execute cascading schedule shift.
     */
    // Metode ini mengeksekusi upgrade layanan dan pergeseran jadwal secara nyata ke database.
    // Semua perubahan dibungkus dalam DB transaction untuk menjaga konsistensi data.
    public function applyUpgradeAndShift(Booking $booking, array $upgradeData): array
    {
        // Ambil parameter dari data upgrade yang dikirim admin
        $additionalMinutes = (int) ($upgradeData['additional_minutes'] ?? 0);
        $additionalPrice = (int) ($upgradeData['additional_price'] ?? 0);
        $serviceIds = (array) ($upgradeData['service_ids'] ?? []);
        $upgradeNotes = trim((string) ($upgradeData['upgrade_notes'] ?? ''));
        $notifyCustomers = (bool) ($upgradeData['notify_customers'] ?? true);

        // Bungkus seluruh proses dalam transaksi database
        return DB::transaction(function () use ($booking, $additionalMinutes, $additionalPrice, $serviceIds, $upgradeNotes, $notifyCustomers) {
            $date = $booking->booking_date->toDateString();
            $currentEndCarbon = Carbon::parse("{$date} {$booking->end_time}");
            $newEndCarbon = (clone $currentEndCarbon)->addMinutes($additionalMinutes);

            // 1. Update current booking
            // Perbarui waktu selesai dan total harga booking yang sedang dikerjakan
            $booking->end_time = $newEndCarbon->format('H:i:s');
            $booking->total_price += $additionalPrice;

            // Tambahkan catatan upgrade jika ada
            if ($upgradeNotes !== '') {
                $existingNotes = $booking->service_upgrade_notes ? $booking->service_upgrade_notes . "\n" : '';
                $booking->service_upgrade_notes = $existingNotes . '[' . now()->format('H:i') . "] +{$additionalMinutes}m (Rp " . number_format($additionalPrice, 0, ',', '.') . '): ' . $upgradeNotes;
            }

            $booking->save();

            // 2. Attach new services to BookingDetail if selected
            // Lampirkan layanan tambahan ke booking detail jika admin memilih layanan baru
            foreach ($serviceIds as $svcId) {
                $svc = Service::find($svcId);
                if ($svc) {
                    BookingDetail::create([
                        'booking_id' => $booking->id,
                        'service_id' => $svc->id,
                        'price' => $svc->price,
                        'duration_minutes' => $svc->duration_minutes,
                    ]);
                }
            }

            // 3. Find subsequent bookings for artist on that date
            // Ambil booking-booking aktif artis yang sama pada hari tersebut untuk digeser
            $subsequentBookings = Booking::with(['customer', 'artist.user'])
                ->where('artist_id', $booking->artist_id)
                ->whereDate('booking_date', $booking->booking_date)
                ->where('id', '!=', $booking->id)
                ->whereIn('status', [
                    Booking::STATUS_PENDING,
                    Booking::STATUS_CONFIRMED,
                    Booking::STATUS_IN_PROGRESS,
                    Booking::STATUS_RESCHEDULED,
                ])
                ->where('start_time', '>=', $booking->start_time)
                ->orderBy('start_time', 'asc')
                ->get();

            // Array penampung hasil pergeseran yang berhasil disimpan
            $shiftedResults = [];
            $rollingEnd = clone $newEndCarbon;

            // Iterasi setiap booking berikutnya dan terapkan pergeseran
            foreach ($subsequentBookings as $sub) {
                $subStart = Carbon::parse("{$date} {$sub->start_time}");
                $subEnd = Carbon::parse("{$date} {$sub->end_time}");
                $durationMinutes = $subStart->diffInMinutes($subEnd);

                // Jika booking ini bentrok dengan waktu selesai sebelumnya, geser
                if ($subStart->lt($rollingEnd)) {
                    $newSubStart = clone $rollingEnd;
                    $newSubEnd = (clone $newSubStart)->addMinutes($durationMinutes);

                    // Preserve initial original start time
                    // Simpan waktu asli hanya jika belum pernah disimpan sebelumnya
                    if (! $sub->original_start_time) {
                        $sub->original_start_time = $sub->start_time;
                    }
                    if (! $sub->original_end_time) {
                        $sub->original_end_time = $sub->end_time;
                    }

                    // Hitung total menit keterlambatan terhadap waktu asli
                    $origStart = Carbon::parse("{$date} {$sub->original_start_time}");
                    $delayMinutes = $origStart->diffInMinutes($newSubStart);

                    // Perbarui waktu mulai dan selesai booking, serta tandai sebagai delayed
                    $sub->start_time = $newSubStart->format('H:i:s');
                    $sub->end_time = $newSubEnd->format('H:i:s');
                    $sub->is_delayed = true;
                    $sub->delay_minutes = $delayMinutes;
                    $sub->delay_reason = "Perpanjangan durasi pengerjaan tamu sebelumnya (Booking #{$booking->booking_code})";
                    $sub->delay_notified_at = now();
                    $sub->save();

                    // Generate & dispatch notification if customer has phone
                    // Kirim notifikasi WhatsApp ke pelanggan yang terdampak
                    $phone = $sub->customer?->phone ?? $sub->customer_phone;
                    $waLogId = null;

                    // Kirim notifikasi hanya jika flag notifyCustomers aktif dan nomor telepon valid
                    if ($notifyCustomers && ! empty($phone) && $phone !== '-') {
                        // Bangun pesan WhatsApp dari template untuk tipe SCHEDULE_DELAYED
                        $waMsg = WaTemplateService::buildMessage(WaLog::TYPE_SCHEDULE_DELAYED, [
                            'customer_name' => $sub->customer_display_name,
                            'booking_code' => $sub->booking_code,
                            'artist_name' => optional($sub->artist?->user)->name ?? 'Terapis',
                            'date' => $sub->formatted_booking_date,
                            'original_time' => substr($sub->original_start_time, 0, 5) . ' - ' . substr($sub->original_end_time, 0, 5),
                            'new_time' => $newSubStart->format('H:i') . ' - ' . $newSubEnd->format('H:i'),
                            'delay_minutes' => $delayMinutes,
                            'delay_reason' => $sub->delay_reason,
                        ]);

                        // Buat log WhatsApp dengan status QUEUED untuk dikirim oleh job
                        $log = WaLog::create([
                            'booking_id' => $sub->id,
                            'phone_number' => $phone,
                            'message_type' => WaLog::TYPE_SCHEDULE_DELAYED,
                            'message_body' => $waMsg,
                            'status' => WaLog::STATUS_QUEUED,
                        ]);

                        $waLogId = $log->id;

                        // Dispatch job pengiriman WhatsApp secara asinkron
                        if (class_exists(SendWaJob::class)) {
                            try {
                                SendWaJob::dispatch($log->id);
                            } catch (\Throwable $e) {
                                report($e);
                            }
                        }
                    }

                    // Catat hasil pergeseran untuk dikembalikan sebagai response
                    $shiftedResults[] = [
                        'booking_id' => $sub->id,
                        'booking_code' => $sub->booking_code,
                        'customer_name' => $sub->customer_display_name,
                        'new_start_time' => $newSubStart->format('H:i'),
                        'new_end_time' => $newSubEnd->format('H:i'),
                        'delay_minutes' => $delayMinutes,
                        'wa_log_id' => $waLogId,
                    ];

                    $rollingEnd = clone $newSubEnd;
                } else {
                    // Buffer waktu cukup, tidak perlu menggeser booking ini
                    $rollingEnd = clone $subEnd;
                }
            }

            // Kembalikan hasil akhir proses upgrade dan pergeseran
            return [
                'success' => true,
                'current_booking' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'new_end_time' => $booking->end_time,
                    'new_total_price' => $booking->total_price,
                ],
                'shifted_count' => count($shiftedResults),
                'shifted_bookings' => $shiftedResults,
            ];
        });
    }
}
