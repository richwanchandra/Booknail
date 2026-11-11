<?php

// File: ArtistLeave.php
// Model Eloquent untuk mengelola data cuti/libur nail artist.
// Setiap record merepresentasikan satu hari libur yang diajukan oleh seorang artist.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistLeave extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'artist_id',    // ID nail artist yang mengajukan cuti
        'leave_date',   // Tanggal cuti yang diambil
        'reason',       // Alasan pengajuan cuti
    ];

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'leave_date' => 'date', // Konversi ke objek Carbon agar mudah dimanipulasi tanggal
    ];

    /**
     * Relasi: Cuti ini dimiliki oleh seorang NailArtist.
     * Menggunakan foreign key 'artist_id' karena nama kolom tidak mengikuti konvensi Laravel.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(NailArtist::class, 'artist_id');
    }
}
