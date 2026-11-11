<?php

// File: NailArtist.php
// Model Eloquent untuk mengelola data nail artist (teknisi nail art).
// Model ini terpisah dari User karena satu User dengan role artist memiliki data tambahan
// seperti bio, foto, status aktif, jadwal, dan portofolio.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NailArtist extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'user_id',   // ID user yang terkait dengan artist ini
        'bio',       // Biografi/deskripsi singkat tentang artist
        'photo',     // Path foto profil artist / Base64 Data URI
        'is_active', // Status aktif artist (bisa menerima booking atau tidak)
    ];

    /**
     * Accessor: URL foto profil nail artist (mendukung Base64 Data URI maupun storage path).
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        if (str_starts_with($this->photo, 'data:')) {
            return $this->photo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->photo);
    }

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'is_active' => 'boolean', // Konversi ke boolean untuk pengecekan kondisi
    ];

    /**
     * Relasi: Nail artist ini terkait dengan satu User (akun login).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu artist memiliki banyak jadwal mingguan (ArtistSchedule).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(ArtistSchedule::class, 'artist_id');
    }

    /**
     * Relasi: Satu artist memiliki banyak data cuti (ArtistLeave).
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(ArtistLeave::class, 'artist_id');
    }

    /**
     * Relasi: Satu artist menangani banyak booking.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'artist_id');
    }

    /**
     * Relasi: Satu artist menerima banyak ulasan dari pelanggan.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'artist_id');
    }

    /**
     * Accessor: Menghitung rata-rata rating dari semua ulasan artist.
     * Mengembalikan null jika belum ada ulasan.
     */
    public function getAverageRatingAttribute(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Accessor: Menghitung total jumlah ulasan yang diterima artist.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    /**
     * Scope: Filter artist yang sedang aktif saja.
     * Artist tidak aktif tidak akan muncul dalam daftar pemilihan untuk booking.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
