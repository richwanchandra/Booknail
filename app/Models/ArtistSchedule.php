<?php

// File: ArtistSchedule.php
// Model Eloquent untuk mengelola jadwal kerja mingguan nail artist.
// Setiap record merepresentasikan jadwal satu hari dalam seminggu untuk seorang artist.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistSchedule extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'artist_id',    // ID nail artist pemilik jadwal
        'day_of_week',  // Hari dalam seminggu (0=Minggu, 1=Senin, ..., 6=Sabtu)
        'start_time',   // Jam mulai kerja
        'end_time',     // Jam selesai kerja
        'is_off',       // Penanda apakah hari tersebut libur
    ];

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'day_of_week' => 'integer', // Pastikan tipe integer untuk perbandingan indeks hari
        'is_off' => 'boolean',      // Konversi ke boolean agar mudah digunakan dalam kondisi
    ];

    /**
     * Relasi: Jadwal ini dimiliki oleh seorang NailArtist.
     * Menggunakan foreign key 'artist_id' karena nama kolom tidak mengikuti konvensi Laravel.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(NailArtist::class, 'artist_id');
    }

    /**
     * Accessor: Mengonversi angka hari (day_of_week) menjadi nama hari dalam bahasa Indonesia.
     * Digunakan untuk tampilan di UI agar lebih ramah pengguna.
     */
    public function getDayNameAttribute(): string
    {
        // Daftar nama hari Indonesia, indeks 0 = Minggu sesuai konvensi PHP
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return $days[$this->day_of_week] ?? '-';
    }

    /**
     * Accessor: Menampilkan rentang waktu kerja dalam format "HH:MM - HH:MM".
     * Jika hari tersebut libur (is_off), tampilkan teks "Libur".
     */
    public function getTimeRangeAttribute(): string
    {
        // Jika artist libur pada hari ini, langsung kembalikan label "Libur"
        if ($this->is_off) {
            return 'Libur';
        }

        // Potong string waktu menjadi HH:MM saja (tanpa detik) untuk tampilan yang rapi
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }
}
