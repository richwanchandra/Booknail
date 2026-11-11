<?php

// =============================================================================
// Migration: Menambahkan kolom tambahan ke tabel users.
// Kolom 'phone' untuk nomor telepon dan 'loyalty_points' untuk sistem poin loyalitas pelanggan.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom phone dan loyalty_points ke tabel users.
     * Kolom ini mendukung fitur kontak pelanggan dan program loyalitas.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('name'); // Nomor telepon pengguna (maks 20 karakter), opsional karena bisa diisi nanti
            $table->unsignedInteger('loyalty_points')->default(0)->after('phone'); // Poin loyalitas pelanggan, default 0 untuk pengguna baru
        });
    }

    /**
     * Menghapus kolom phone dan loyalty_points dari tabel users.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'loyalty_points']); // Hapus kedua kolom sekaligus
        });
    }
};
