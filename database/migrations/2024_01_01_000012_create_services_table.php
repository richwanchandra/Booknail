<?php

// =============================================================================
// Migration: Membuat tabel services untuk menyimpan daftar layanan nail art.
// Setiap layanan terkait dengan kategori dan memiliki harga serta durasi pengerjaan.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'services' untuk menyimpan detail layanan yang ditawarkan salon.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('category_id')->constrained()->cascadeOnDelete(); // Foreign key ke tabel categories, hapus layanan jika kategori dihapus
            $table->string('name'); // Nama layanan (misalnya "Gel Manicure", "Nail Extension")
            $table->decimal('price', 12, 0); // Harga layanan dalam Rupiah (tanpa desimal, presisi 12 digit)
            $table->unsignedSmallInteger('duration_minutes'); // Durasi pengerjaan layanan dalam menit
            $table->text('description')->nullable(); // Deskripsi detail layanan, opsional
            $table->string('image')->nullable(); // Path gambar/foto layanan, opsional
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif layanan, default aktif
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel services.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
