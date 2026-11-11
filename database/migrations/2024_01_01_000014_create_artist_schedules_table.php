<?php

// =============================================================================
// Migration: Membuat tabel artist_schedules untuk jadwal kerja mingguan nail artist.
// Setiap artist memiliki satu jadwal per hari dalam seminggu (Senin-Minggu).
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'artist_schedules' untuk mengatur jam kerja nail artist per hari.
     */
    public function up(): void
    {
        Schema::create('artist_schedules', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('artist_id')->constrained('nail_artists')->cascadeOnDelete(); // Foreign key ke tabel nail_artists, hapus jadwal jika artist dihapus
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday'); // Hari dalam seminggu (0=Minggu, 6=Sabtu)
            $table->time('start_time'); // Jam mulai kerja
            $table->time('end_time'); // Jam selesai kerja
            $table->boolean('is_off')->default(false); // Apakah hari tersebut adalah hari libur artist, default false (masuk kerja)
            $table->timestamps(); // Kolom created_at dan updated_at

            // Constraint unik: satu artist hanya boleh punya satu jadwal per hari
            $table->unique(['artist_id', 'day_of_week']);
        });
    }

    /**
     * Menghapus tabel artist_schedules.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_schedules');
    }
};
