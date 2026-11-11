<?php

// =============================================================================
// Migration: Menambahkan kolom payment_type ke tabel payments (jika belum ada).
// Kolom ini membedakan jenis pembayaran: DP (uang muka) atau pelunasan.
// Menggunakan pengecekan hasColumn untuk keamanan migrasi idempotent.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom payment_type ke tabel payments jika belum ada.
     */
    public function up(): void
    {
        // Cek apakah tabel payments ada dan kolom payment_type belum ada, untuk mencegah error duplikasi
        if (Schema::hasTable('payments') && ! Schema::hasColumn('payments', 'payment_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('payment_type', 20)->default('dp')->after('booking_id'); // Tipe pembayaran: 'dp' atau 'full', default 'dp'
            });
        }
    }

    /**
     * Menghapus kolom payment_type dari tabel payments jika ada.
     */
    public function down(): void
    {
        // Cek keberadaan tabel dan kolom sebelum menghapus, untuk keamanan rollback
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'payment_type')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('payment_type');
            });
        }
    }
};
