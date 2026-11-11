<?php

// =============================================================================
// Migration: Membuat tabel payments untuk mengelola data pembayaran booking.
// Menyimpan bukti transfer, jumlah bayar, status verifikasi, dan riwayat penolakan.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'payments' untuk menyimpan transaksi pembayaran.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete(); // Foreign key ke tabel bookings, hapus pembayaran jika booking dihapus
            $table->string('payment_type', 20)->default('dp')->after('booking_id'); // Tipe pembayaran: 'dp' (uang muka) atau 'full' (pelunasan), default DP
            $table->string('proof_file'); // Path file bukti transfer yang diunggah pelanggan
            $table->decimal('amount', 12, 0); // Jumlah pembayaran dalam Rupiah
            // Status verifikasi pembayaran: pending (menunggu) → verified (terverifikasi) / rejected (ditolak)
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable(); // Alasan penolakan pembayaran oleh admin, null jika belum ditolak
            $table->timestamp('verified_at')->nullable(); // Waktu verifikasi pembayaran oleh admin
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete(); // Admin yang memverifikasi, null jika admin dihapus
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel payments.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
