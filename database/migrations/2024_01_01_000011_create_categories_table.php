<?php

// =============================================================================
// Migration: Membuat tabel categories untuk mengelompokkan layanan nail art.
// Kategori digunakan untuk mengorganisasi layanan agar mudah ditemukan pelanggan.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'categories' untuk menyimpan kategori layanan nail art.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name'); // Nama kategori (misalnya "Nail Art", "Nail Care")
            $table->string('slug')->unique(); // Slug URL-friendly yang unik, digunakan untuk routing dan SEO
            $table->string('icon')->nullable(); // Path atau nama ikon kategori, opsional
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif kategori, default aktif agar langsung tampil
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel categories.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
