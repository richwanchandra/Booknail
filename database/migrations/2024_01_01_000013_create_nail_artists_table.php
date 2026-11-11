<?php

// =============================================================================
// Migration: Membuat tabel nail_artists untuk menyimpan data nail artist (terapis kuku).
// Setiap nail artist terhubung ke akun user untuk keperluan autentikasi dan profil.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'nail_artists' untuk profil nail artist di salon.
     */
    public function up(): void
    {
        Schema::create('nail_artists', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Relasi one-to-one ke tabel users, hapus profil artist jika akun dihapus
            $table->string('bio')->nullable(); // Biografi singkat nail artist, opsional
            $table->string('photo')->nullable(); // Path foto profil nail artist, opsional
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif artist, default aktif agar bisa menerima booking
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel nail_artists.
     */
    public function down(): void
    {
        Schema::dropIfExists('nail_artists');
    }
};
