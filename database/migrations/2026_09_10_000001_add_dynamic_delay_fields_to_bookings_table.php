<?php

// =============================================================================
// Migration: Menambahkan kolom-kolom untuk fitur keterlambatan dinamis (dynamic delay).
// Fitur ini memungkinkan pencatatan dan notifikasi jika layanan terlambat dimulai,
// termasuk alasan keterlambatan, waktu asli, dan respons pelanggan.
// Juga mengubah kolom message_type pada wa_logs agar mendukung tipe pesan baru.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom-kolom delay dan upgrade ke tabel bookings.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Penanda apakah booking mengalami keterlambatan
            if (! Schema::hasColumn('bookings', 'is_delayed')) {
                $table->boolean('is_delayed')->default(false)->after('status');
            }
            // Jumlah menit keterlambatan
            if (! Schema::hasColumn('bookings', 'delay_minutes')) {
                $table->integer('delay_minutes')->default(0)->after('is_delayed');
            }
            // Jam mulai asli sebelum digeser karena delay
            if (! Schema::hasColumn('bookings', 'original_start_time')) {
                $table->time('original_start_time')->nullable()->after('delay_minutes');
            }
            // Jam selesai asli sebelum digeser karena delay
            if (! Schema::hasColumn('bookings', 'original_end_time')) {
                $table->time('original_end_time')->nullable()->after('original_start_time');
            }
            // Alasan keterlambatan (misalnya "artist sebelumnya overtime")
            if (! Schema::hasColumn('bookings', 'delay_reason')) {
                $table->string('delay_reason', 255)->nullable()->after('original_end_time');
            }
            // Waktu notifikasi keterlambatan dikirim ke pelanggan
            if (! Schema::hasColumn('bookings', 'delay_notified_at')) {
                $table->dateTime('delay_notified_at')->nullable()->after('delay_reason');
            }
            // Respons pelanggan terhadap keterlambatan (misalnya "accepted", "rescheduled")
            if (! Schema::hasColumn('bookings', 'customer_delay_ack')) {
                $table->string('customer_delay_ack', 50)->nullable()->after('delay_notified_at');
            }
            // Catatan upgrade layanan (kompensasi karena keterlambatan, misalnya "free nail art")
            if (! Schema::hasColumn('bookings', 'service_upgrade_notes')) {
                $table->text('service_upgrade_notes')->nullable()->after('cashier_notes');
            }
        });

        // Ubah tipe kolom message_type di wa_logs menjadi string(50) agar mendukung tipe pesan baru
        if (Schema::hasTable('wa_logs')) {
            Schema::table('wa_logs', function (Blueprint $table) {
                $table->string('message_type', 50)->change(); // Ubah dari enum ke string agar lebih fleksibel
            });
        }
    }

    /**
     * Rollback: Menghapus semua kolom delay dari tabel bookings.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Daftar kolom yang akan dihapus saat rollback
            $columns = [
                'is_delayed',
                'delay_minutes',
                'original_start_time',
                'original_end_time',
                'delay_reason',
                'delay_notified_at',
                'customer_delay_ack',
                'service_upgrade_notes',
            ];

            // Hapus setiap kolom jika ada, dicek satu per satu untuk keamanan
            foreach ($columns as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
