<?php

// =============================================================================
// Migration: Membuat tabel reviews untuk menyimpan ulasan pelanggan.
// Setiap booking hanya boleh memiliki satu ulasan (constraint unique pada booking_id).
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'reviews' untuk menyimpan ulasan dan rating dari pelanggan.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete(); // Foreign key ke tabel bookings, hapus review jika booking dihapus
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete(); // Foreign key ke tabel users (pelanggan yang memberi review)
            $table->foreignId('artist_id')->constrained('nail_artists')->cascadeOnDelete(); // Foreign key ke tabel nail_artists (artist yang di-review)
            $table->tinyInteger('rating')->comment('1-5 stars'); // Rating bintang 1-5
            $table->text('comment')->nullable(); // Komentar/ulasan tertulis, opsional
            $table->string('photo')->nullable(); // Path foto hasil karya yang diunggah pelanggan, opsional
            $table->timestamps(); // Kolom created_at dan updated_at

            $table->unique('booking_id'); // one review per booking — satu booking hanya boleh punya satu ulasan
        });
    }

    /**
     * Menghapus tabel reviews.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
