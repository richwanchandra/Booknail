<?php

// =============================================================================
// Migration: Mengubah tipe kolom image pada tabel services dari string ke longText.
// Perubahan ini diperlukan agar kolom dapat menyimpan data Base64 Data URI
// yang ukurannya jauh lebih besar dari batas kolom VARCHAR (255 karakter).
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengubah kolom image menjadi longText untuk mendukung penyimpanan Base64 Data URI.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->longText('image')->nullable()->change(); // Ubah ke longText agar bisa menampung gambar Base64
        });
    }

    /**
     * Rollback: Mengembalikan kolom image ke tipe string(255).
     * PERINGATAN: Data Base64 yang sudah tersimpan akan terpotong saat rollback.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->change(); // Kembalikan ke VARCHAR(255)
        });
    }
};
