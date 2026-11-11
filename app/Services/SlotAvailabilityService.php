<?php

// ==========================================================================
// File: SlotAvailabilityService.php
// Fungsi: Service untuk mengecek ketersediaan slot waktu booking artis.
//         Melakukan tiga validasi: jadwal kerja artis, status cuti/libur,
//         dan tumpang tindih dengan booking lain. Juga menyediakan fungsi
//         untuk mendapatkan semua slot tersedia pada tanggal tertentu.
// ==========================================================================

namespace App\Services;

use App\Models\ArtistLeave;
use App\Models\ArtistSchedule;
use App\Models\Booking;
use Carbon\Carbon;

class SlotAvailabilityService
{
    // Metode utama untuk mengecek apakah slot waktu tertentu tersedia untuk artis.
    // Menjalankan tiga tahap pengecekan secara berurutan.
    public function check(int $artistId, string $bookingDate, string $startTime, int $totalDurationMinutes): array
    {
        // Parse tanggal booking dan tentukan hari dalam minggu (0-6)
        $date = Carbon::parse($bookingDate);
        $dayOfWeek = (int) $date->dayOfWeek;

        // Hitung waktu mulai dan selesai berdasarkan durasi total layanan
        $startCarbon = Carbon::parse("{$date->toDateString()} {$startTime}");
        $endCarbon = (clone $startCarbon)->addMinutes($totalDurationMinutes);

        // Langkah 1: Cek apakah artis memiliki jadwal kerja dan slot sesuai jam shift
        $step1 = $this->checkArtistSchedule($artistId, $dayOfWeek, $startCarbon, $endCarbon);
        if (! $step1['available']) {
            return $step1;
        }

        // Langkah 2: Cek apakah artis sedang cuti/libur khusus pada tanggal tersebut
        $step2 = $this->checkArtistLeave($artistId, $date);
        if (! $step2['available']) {
            return $step2;
        }

        // Langkah 3: Cek apakah slot waktu bentrok dengan booking aktif lain
        $step3 = $this->checkOverlappingBookings($artistId, $date, $startCarbon, $endCarbon);
        if (! $step3['available']) {
            return $step3;
        }

        // Semua pengecekan lolos, slot tersedia
        return [
            'available' => true,
            'reason' => null,
        ];
    }

    // Memeriksa apakah artis memiliki jadwal kerja pada hari tersebut
    // dan apakah waktu booking berada dalam rentang jam shift artis.
    protected function checkArtistSchedule(int $artistId, int $dayOfWeek, Carbon $start, Carbon $end): array
    {
        // Cari jadwal artis berdasarkan hari dalam minggu
        $schedule = ArtistSchedule::where('artist_id', $artistId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Jika jadwal tidak ditemukan, artis tidak beroperasi di hari itu
        if (! $schedule) {
            return [
                'available' => false,
                'reason' => 'Artis tidak memiliki jadwal pada hari tersebut.',
            ];
        }

        // Jika hari tersebut ditandai sebagai hari libur (is_off)
        if ($schedule->is_off) {
            return [
                'available' => false,
                'reason' => 'Artis libur pada hari tersebut (off schedule).',
            ];
        }

        // Parse waktu mulai dan selesai shift artis
        $shiftStart = Carbon::parse($start->toDateString().' '.$schedule->start_time);
        $shiftEnd = Carbon::parse($start->toDateString().' '.$schedule->end_time);

        // Validasi: waktu mulai booking tidak boleh lebih awal dari jam mulai shift
        if ($start->lt($shiftStart)) {
            return [
                'available' => false,
                'reason' => 'Waktu mulai lebih awal dari jam kerja artis ('.substr($schedule->start_time, 0, 5).' WIB).',
            ];
        }

        // Validasi: waktu selesai booking tidak boleh melewati jam selesai shift
        if ($end->gt($shiftEnd)) {
            return [
                'available' => false,
                'reason' => 'Durasi booking melewati jam selesai artis ('.substr($schedule->end_time, 0, 5).' WIB). Kurangi durasi atau pilih slot lebih awal.',
            ];
        }

        return ['available' => true];
    }

    // Memeriksa apakah artis memiliki jadwal cuti/libur khusus pada tanggal tertentu
    protected function checkArtistLeave(int $artistId, Carbon $date): array
    {
        // Cek keberadaan data cuti artis pada tanggal yang diminta
        $leaveExists = ArtistLeave::where('artist_id', $artistId)
            ->whereDate('leave_date', $date->toDateString())
            ->exists();

        if ($leaveExists) {
            return [
                'available' => false,
                'reason' => 'Artis sedang cuti/libur khusus pada tanggal tersebut.',
            ];
        }

        return ['available' => true];
    }

    // Memeriksa apakah terdapat booking aktif lain yang tumpang tindih
    // dengan rentang waktu yang diminta
    protected function checkOverlappingBookings(int $artistId, Carbon $date, Carbon $start, Carbon $end): array
    {
        // Cari booking dengan status aktif yang waktunya overlap
        // Logika overlap: start_time booking lain < end_time request DAN end_time booking lain > start_time request
        $overlapping = Booking::where('artist_id', $artistId)
            ->whereDate('booking_date', $date->toDateString())
            ->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_IN_PROGRESS,
            ])
            ->where(function ($q) use ($start, $end) {
                $q->whereRaw('TIME(start_time) < ?', [$end->format('H:i:s')])
                    ->whereRaw('TIME(end_time) > ?', [$start->format('H:i:s')]);
            })
            ->first();

        // Jika ditemukan booking yang bentrok, kembalikan informasi booking tersebut
        if ($overlapping) {
            return [
                'available' => false,
                'reason' => 'Slot waktu bentrok dengan booking lain (Kode: '.$overlapping->booking_code.', Jam: '.substr($overlapping->start_time, 0, 5).'-'.substr($overlapping->end_time, 0, 5).' WIB). Silakan pilih jam lain atau artis lain.',
            ];
        }

        return ['available' => true];
    }

    /**
     * Get all available slots for an artist on a specific date, stepping by $stepMinutes.
     */
    // Menghasilkan daftar semua slot waktu yang tersedia untuk artis pada tanggal tertentu.
    // Setiap slot diperiksa ketersediaannya menggunakan metode check().
    public function getAvailableSlots(int $artistId, string $bookingDate, int $totalDurationMinutes, int $stepMinutes = 60): array
    {
        // Array penampung slot yang akan dikembalikan
        $slots = [];
        $date = Carbon::parse($bookingDate);
        $dayOfWeek = (int) $date->dayOfWeek;

        // Ambil jadwal artis pada hari tersebut
        $schedule = ArtistSchedule::where('artist_id', $artistId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        // Cek apakah artis cuti pada tanggal tersebut
        $leaveExists = ArtistLeave::where('artist_id', $artistId)
            ->whereDate('leave_date', $date->toDateString())
            ->exists();

        // Jika tidak ada jadwal, hari libur, atau sedang cuti, kembalikan array kosong
        if (! $schedule || $schedule->is_off || $leaveExists) {
            return [];
        }

        // Parse waktu mulai dan selesai shift artis
        $shiftStart = Carbon::parse("{$date->toDateString()} {$schedule->start_time}");
        $shiftEnd = Carbon::parse("{$date->toDateString()} {$schedule->end_time}");

        // Mulai dari awal shift, geser per stepMinutes hingga sisa waktu cukup untuk durasi layanan
        $cursor = clone $shiftStart;
        while ($cursor->diffInMinutes($shiftEnd) >= $totalDurationMinutes) {
            // Cek ketersediaan setiap slot menggunakan metode check()
            $check = $this->check(
                $artistId,
                $date->toDateString(),
                $cursor->format('H:i:s'),
                $totalDurationMinutes
            );

            $slots[] = [
                'time' => $cursor->format('H:i'),
                'available' => $check['available'],
                'reason' => $check['reason'],
            ];

            // Geser cursor ke slot berikutnya
            $cursor->addMinutes($stepMinutes);
        }

        return $slots;
    }
}
