<?php

// =============================================================================
// Migration: Membuat tabel bookings sebagai tabel utama untuk pemesanan layanan.
// Menyimpan seluruh informasi booking termasuk pelanggan, artist, jadwal,
// harga, status, dan catatan pembatalan/penyelesaian.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'bookings' untuk mengelola data pemesanan layanan salon.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('booking_code', 20)->unique(); // Kode booking unik yang ditampilkan ke pelanggan (misalnya "BK-20240101-001")
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete(); // Foreign key ke tabel users sebagai pelanggan
            $table->foreignId('artist_id')->constrained('nail_artists')->cascadeOnDelete(); // Foreign key ke tabel nail_artists sebagai artist yang ditugaskan
            $table->date('booking_date'); // Tanggal reservasi layanan
            $table->time('start_time'); // Jam mulai layanan
            $table->time('end_time'); // Jam selesai layanan (dihitung otomatis berdasarkan durasi layanan)
            $table->string('custom_design_photo')->nullable(); // Path foto desain kustom yang diunggah pelanggan, opsional
            $table->string('design_notes')->nullable(); // Catatan desain dari pelanggan, opsional
            $table->decimal('total_price', 12, 0); // Total harga booking sebelum diskon (dalam Rupiah)
            $table->decimal('dp_amount', 12, 0); // Jumlah uang muka (down payment) yang harus dibayar
            $table->unsignedInteger('loyalty_points_used')->default(0); // Jumlah poin loyalitas yang digunakan untuk booking ini
            $table->decimal('loyalty_discount', 12, 0)->default(0); // Nilai diskon dari poin loyalitas (dalam Rupiah)
            // Status alur booking: pending → confirmed → in_progress → completed/cancelled/rescheduled
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled'])
                ->default('pending'); // Status awal booking adalah 'pending' menunggu verifikasi pembayaran DP
            $table->text('cancellation_reason')->nullable(); // Alasan pembatalan (diisi oleh admin/sistem)
            $table->text('cancel_reason')->nullable(); // Alasan pembatalan dari perspektif pelanggan
            $table->timestamp('cancelled_at')->nullable(); // Waktu saat booking dibatalkan
            $table->timestamp('completed_at')->nullable(); // Waktu saat layanan selesai dikerjakan
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel bookings.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
