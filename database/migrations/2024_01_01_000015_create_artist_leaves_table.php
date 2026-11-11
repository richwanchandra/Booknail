<?php

// =============================================================================
// Migration: Membuat tabel artist_leaves untuk mencatat cuti/izin nail artist.
// Tabel ini digunakan untuk menandai tanggal spesifik di mana artist tidak tersedia.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'artist_leaves' untuk menyimpan data cuti nail artist.
     */
    public function up(): void
    {
        Schema::create('artist_leaves', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('artist_id')->constrained('nail_artists')->cascadeOnDelete(); // Foreign key ke tabel nail_artists, hapus record cuti jika artist dihapus
            $table->date('leave_date'); // Tanggal cuti/izin
            $table->string('reason')->nullable(); // Alasan cuti, opsional
            $table->timestamps(); // Kolom created_at dan updated_at

            // Constraint unik: satu artist hanya boleh punya satu record cuti per tanggal
            $table->unique(['artist_id', 'leave_date']);
        });
    }

    /**
     * Menghapus tabel artist_leaves.
     */
    public function down(): void
    {
        Schema::dropIfExists('artist_leaves');
    }
};
