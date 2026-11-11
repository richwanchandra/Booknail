<?php

// =============================================================================
// Migration: Membuat tabel booking_details sebagai detail item layanan per booking.
// Satu booking bisa memiliki banyak detail layanan (relasi one-to-many).
// Tabel ini mencatat layanan apa saja yang dipilih beserta harga dan durasinya.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'booking_details' untuk menyimpan rincian layanan per booking.
     */
    public function up(): void
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete(); // Foreign key ke tabel bookings, hapus detail jika booking dihapus
            $table->foreignId('service_id')->constrained()->cascadeOnDelete(); // Foreign key ke tabel services, hapus detail jika layanan dihapus
            $table->decimal('price', 12, 0); // Harga layanan saat booking dibuat (snapshot harga, agar tidak berubah jika harga diperbarui)
            $table->unsignedSmallInteger('duration_minutes'); // Durasi layanan dalam menit (snapshot dari master service)
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel booking_details.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
