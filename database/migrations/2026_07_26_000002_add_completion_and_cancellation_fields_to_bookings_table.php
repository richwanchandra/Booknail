<?php

// =============================================================================
// Migration: Menambahkan kolom penyelesaian dan pembatalan ke tabel bookings.
// Kolom completed_at, cancelled_at, dan cancel_reason ditambahkan secara kondisional
// untuk mendukung pencatatan waktu dan alasan penyelesaian/pembatalan booking.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom completed_at, cancelled_at, dan cancel_reason ke tabel bookings.
     * Setiap kolom dicek terlebih dahulu untuk mencegah duplikasi jika sudah ada dari migration sebelumnya.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom waktu penyelesaian booking jika belum ada
            if (! Schema::hasColumn('bookings', 'completed_at')) {
                $table->timestamp('completed_at')->nullable(); // Waktu booking selesai dikerjakan
            }
            // Tambahkan kolom waktu pembatalan booking jika belum ada
            if (! Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable(); // Waktu booking dibatalkan
            }
            // Tambahkan kolom alasan pembatalan jika belum ada
            if (! Schema::hasColumn('bookings', 'cancel_reason')) {
                $table->text('cancel_reason')->nullable(); // Alasan pembatalan dari pelanggan/admin
            }
        });
    }

    /**
     * Menghapus kolom completed_at, cancelled_at, dan cancel_reason dari tabel bookings.
     * Setiap kolom dicek keberadaannya sebelum dihapus untuk keamanan rollback.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('bookings', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
            if (Schema::hasColumn('bookings', 'cancel_reason')) {
                $table->dropColumn('cancel_reason');
            }
        });
    }
};
