<?php

// File: FcmToken.php
// Model Eloquent untuk menyimpan token FCM (Firebase Cloud Messaging) perangkat pengguna.
// Digunakan untuk mengirimkan push notification ke perangkat mobile/web pengguna.
// Satu user bisa memiliki banyak token (multi-device login).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FcmToken extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'user_id',      // ID pengguna pemilik token
        'token',        // Token FCM unik dari perangkat
        'last_used_at', // Waktu terakhir token digunakan untuk mengirim notifikasi
    ];

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'last_used_at' => 'datetime', // Konversi ke objek Carbon
    ];

    /**
     * Relasi: Token ini dimiliki oleh seorang User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
