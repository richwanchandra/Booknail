<?php

// ==========================================================================
// File: SlotAvailabilityService.php
// Fungsi: Service untuk mengecek ketersediaan slot waktu booking artis.
//         Melakukan tiga validasi: jadwal kerja artis (dalam jam operasional
//         11:00 - 19:00 WIB), status cuti/libur, dan tumpang tindih booking.
// ==========================================================================

namespace App\Services;

use App\Models\ArtistLeave;
use App\Models\ArtistSchedule;
use App\Models\Booking;
use Carbon\Carbon;

class SlotAvailabilityService
{
    // Batas jam operasional studio (WIB)
    protected const STUDIO_OPEN = '11:00:00';
    protected const STUDIO_CLOSE = '19:00:00';

    public function check(int $artistId, string $bookingDate, string $startTime, int $totalDurationMinutes): array
    {
        $date = Carbon::parse($bookingDate);
        $dayOfWeek = (int) $date->dayOfWeek;

        $startCarbon = Carbon::parse("{$date->toDateString()} {$startTime}");
        $endCarbon = (clone $startCarbon)->addMinutes($totalDurationMinutes);

        // Langkah 1: Cek jadwal kerja artis & batasan jam operasional 11:00 - 19:00
        $step1 = $this->checkArtistSchedule($artistId, $dayOfWeek, $startCarbon, $endCarbon);
        if (!$step1['available']) {
            return $step1;
        }

        // Langkah 2: Cek status cuti/libur khusus
        $step2 = $this->checkArtistLeave($artistId, $date);
        if (!$step2['available']) {
            return $step2;
        }

        // Langkah 3: Cek bentrok dengan booking aktif lain
        $step3 = $this->checkOverlappingBookings($artistId, $date, $startCarbon, $endCarbon);
        if (!$step3['available']) {
            return $step3;
        }

        return [
            'available' => true,
            'reason' => null,
        ];
    }

    protected function checkArtistSchedule(int $artistId, int $dayOfWeek, Carbon $start, Carbon $end): array
    {
        $schedule = ArtistSchedule::where('artist_id', $artistId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return [
                'available' => false,
                'reason' => 'Artis tidak memiliki jadwal pada hari tersebut.',
            ];
        }

        if ($schedule->is_off) {
            return [
                'available' => false,
                'reason' => 'Artis libur pada hari tersebut (off schedule).',
            ];
        }

        $dateStr = $start->toDateString();

        // Hitung batas efektif: irisan antara jam shift artis dan jam operasional 11:00 - 19:00
        $studioOpen = Carbon::parse("{$dateStr} " . self::STUDIO_OPEN);
        $studioClose = Carbon::parse("{$dateStr} " . self::STUDIO_CLOSE);

        $rawShiftStart = Carbon::parse("{$dateStr} {$schedule->start_time}");
        $rawShiftEnd = Carbon::parse("{$dateStr} {$schedule->end_time}");

        $effectiveStart = $rawShiftStart->gt($studioOpen) ? $rawShiftStart : $studioOpen;
        $effectiveEnd = $rawShiftEnd->lt($studioClose) ? $rawShiftEnd : $studioClose;

        // Validasi: waktu mulai tidak boleh sebelum jam buka / mulai shift
        if ($start->lt($effectiveStart)) {
            return [
                'available' => false,
                'reason' => 'Waktu mulai lebih awal dari jam operasional/kerja (' . $effectiveStart->format('H:i') . ' WIB).',
            ];
        }

        // Validasi: waktu selesai tidak boleh melewati jam tutup (19:00) / selesai shift
        if ($end->gt($effectiveEnd)) {
            return [
                'available' => false,
                'reason' => 'Durasi booking melewati batas jam selesai (' . $effectiveEnd->format('H:i') . ' WIB). Kurangi durasi atau pilih slot lebih awal.',
            ];
        }

        return ['available' => true];
    }

    protected function checkArtistLeave(int $artistId, Carbon $date): array
    {
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

    protected function checkOverlappingBookings(int $artistId, Carbon $date, Carbon $start, Carbon $end): array
    {
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

        if ($overlapping) {
            return [
                'available' => false,
                'reason' => 'Slot waktu bentrok dengan booking lain (Kode: ' . $overlapping->booking_code . ', Jam: ' . substr($overlapping->start_time, 0, 5) . '-' . substr($overlapping->end_time, 0, 5) . ' WIB). Silakan pilih jam lain atau artis lain.',
            ];
        }

        return ['available' => true];
    }

    /**
     * Get all available slots for an artist on a specific date, stepping by $stepMinutes.
     */
    public function getAvailableSlots(int $artistId, string $bookingDate, int $totalDurationMinutes, int $stepMinutes = 60): array
    {
        $slots = [];
        $date = Carbon::parse($bookingDate);
        $dayOfWeek = (int) $date->dayOfWeek;

        $schedule = ArtistSchedule::where('artist_id', $artistId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        $leaveExists = ArtistLeave::where('artist_id', $artistId)
            ->whereDate('leave_date', $date->toDateString())
            ->exists();

        if (!$schedule || $schedule->is_off || $leaveExists) {
            return [];
        }

        $dateStr = $date->toDateString();

        // Batasi start & end slot agar berada di rentang 11:00 - 19:00 WIB
        $studioOpen = Carbon::parse("{$dateStr} " . self::STUDIO_OPEN);
        $studioClose = Carbon::parse("{$dateStr} " . self::STUDIO_CLOSE);

        $rawShiftStart = Carbon::parse("{$dateStr} {$schedule->start_time}");
        $rawShiftEnd = Carbon::parse("{$dateStr} {$schedule->end_time}");

        $shiftStart = $rawShiftStart->gt($studioOpen) ? $rawShiftStart : $studioOpen;
        $shiftEnd = $rawShiftEnd->lt($studioClose) ? $rawShiftEnd : $studioClose;

        // Jika shift artis berada di luar jam 11:00 - 19:00, kembalikan kosong
        if ($shiftStart->gte($shiftEnd)) {
            return [];
        }

        // Loop dari shiftStart selama (cursor + durasi layanan) <= shiftEnd (maks. 19:00)
        $cursor = clone $shiftStart;
        while ((clone $cursor)->addMinutes($totalDurationMinutes)->lte($shiftEnd)) {
            $check = $this->check(
                $artistId,
                $dateStr,
                $cursor->format('H:i:s'),
                $totalDurationMinutes
            );

            $slots[] = [
                'time' => $cursor->format('H:i'),
                'available' => $check['available'],
                'reason' => $check['reason'],
            ];

            $cursor->addMinutes($stepMinutes);
        }

        return $slots;
    }
}