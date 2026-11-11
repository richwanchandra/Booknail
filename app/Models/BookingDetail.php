<?php

// File: BookingDetail.php
// Model Eloquent untuk detail layanan dalam sebuah booking.
// Setiap record merepresentasikan satu layanan (service) yang dipilih dalam satu booking.
// Satu booking bisa memiliki banyak detail layanan.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'booking_id',       // ID booking induk
        'service_id',       // ID layanan yang dipilih
        'price',            // Harga layanan saat booking dibuat (snapshot harga)
        'duration_minutes', // Durasi layanan dalam menit (snapshot durasi)
    ];

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'price' => 'decimal:0',       // Format desimal tanpa angka di belakang koma (Rupiah)
        'duration_minutes' => 'integer', // Durasi sebagai integer
    ];

    /**
     * Relasi: Detail ini milik satu Booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relasi: Detail ini merujuk pada satu Service (layanan).
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Accessor: Format harga ke format mata uang Rupiah (contoh: Rp 75.000).
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    /**
     * Accessor: Format durasi menjadi teks yang mudah dibaca.
     * Contoh output: "1j 30m", "2 jam", "45 menit".
     */
    public function getFormattedDurationAttribute(): string
    {
        // Hitung jam dan sisa menit dari total durasi
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        // Jika ada jam dan menit, tampilkan keduanya dalam format ringkas
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}j {$minutes}m";
        } elseif ($hours > 0) {
            // Jika hanya jam tanpa sisa menit
            return "{$hours} jam";
        }

        // Jika kurang dari 1 jam, tampilkan dalam menit saja
        return "{$minutes} menit";
    }
}
