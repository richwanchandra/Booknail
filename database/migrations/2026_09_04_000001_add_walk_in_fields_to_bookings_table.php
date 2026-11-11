<?php

// =============================================================================
// Migration: Menambahkan kolom-kolom untuk mendukung fitur walk-in booking.
// Walk-in adalah pelanggan yang datang langsung tanpa reservasi online.
// Kolom customer_id diubah menjadi nullable karena walk-in tidak harus punya akun.
// Juga mengubah proof_file di tabel payments menjadi nullable karena walk-in bisa bayar tunai.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom walk-in ke tabel bookings dan mengubah proof_file menjadi nullable.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Ubah customer_id menjadi nullable karena walk-in tidak wajib punya akun user
            $table->unsignedBigInteger('customer_id')->nullable()->change();

            // Nama pelanggan walk-in (diisi manual oleh kasir jika customer_id null)
            if (! Schema::hasColumn('bookings', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('customer_id');
            }
            // Nomor telepon pelanggan walk-in
            if (! Schema::hasColumn('bookings', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('customer_name');
            }
            // Cabang salon tempat booking dilakukan (default: Citra Garden 6)
            if (! Schema::hasColumn('bookings', 'branch')) {
                $table->string('branch', 50)->nullable()->default('Citra Garden 6')->after('artist_id');
            }
            // Sumber booking: WEB_ONLINE (reservasi online), WALK_IN (datang langsung), dll
            if (! Schema::hasColumn('bookings', 'booking_source')) {
                $table->string('booking_source', 30)->default('WEB_ONLINE')->after('status');
            }
            // Penanda apakah booking ini adalah walk-in (true) atau online (false)
            if (! Schema::hasColumn('bookings', 'is_walk_in')) {
                $table->boolean('is_walk_in')->default(false)->after('booking_source');
            }
            // Catatan dari kasir untuk keperluan internal
            if (! Schema::hasColumn('bookings', 'cashier_notes')) {
                $table->string('cashier_notes', 500)->nullable()->after('design_notes');
            }
        });

        // Ubah proof_file menjadi nullable agar walk-in yang bayar tunai tidak perlu upload bukti
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('proof_file')->nullable()->change();
            });
        }
    }

    /**
     * Rollback: Menghapus kolom walk-in dan mengembalikan proof_file menjadi wajib.
     */
    public function down(): void
    {
        // Kembalikan proof_file menjadi NOT NULL
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->string('proof_file')->nullable(false)->change();
            });
        }

        Schema::table('bookings', function (Blueprint $table) {
            // Hapus kolom-kolom walk-in jika ada
            if (Schema::hasColumn('bookings', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('bookings', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
            if (Schema::hasColumn('bookings', 'branch')) {
                $table->dropColumn('branch');
            }
            if (Schema::hasColumn('bookings', 'booking_source')) {
                $table->dropColumn('booking_source');
            }
            if (Schema::hasColumn('bookings', 'is_walk_in')) {
                $table->dropColumn('is_walk_in');
            }
            if (Schema::hasColumn('bookings', 'cashier_notes')) {
                $table->dropColumn('cashier_notes');
            }
            // Kembalikan customer_id menjadi NOT NULL (wajib punya akun)
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
        });
    }
};
