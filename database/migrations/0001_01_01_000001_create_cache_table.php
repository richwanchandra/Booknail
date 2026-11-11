<?php

// =============================================================================
// Migration: Membuat tabel cache dan cache_locks untuk driver cache database.
// Digunakan oleh Laravel saat konfigurasi cache menggunakan driver 'database'.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel 'cache' untuk menyimpan data cache dan 'cache_locks' untuk mekanisme atomic lock.
     */
    public function up(): void
    {
        // Tabel 'cache' — menyimpan item cache dengan key unik
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary(); // Kunci cache sebagai primary key
            $table->mediumText('value'); // Nilai cache yang di-serialize
            $table->bigInteger('expiration')->index(); // Waktu kedaluwarsa cache (UNIX timestamp), diindeks untuk pembersihan efisien
        });

        // Tabel 'cache_locks' — menyimpan informasi atomic lock untuk mencegah race condition
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary(); // Kunci lock sebagai primary key
            $table->string('owner'); // Pemilik lock (identifier unik proses yang mengakuisisi lock)
            $table->bigInteger('expiration')->index(); // Waktu kedaluwarsa lock (UNIX timestamp)
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus tabel cache dan cache_locks.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
