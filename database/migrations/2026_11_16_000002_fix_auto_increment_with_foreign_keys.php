<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memperbaiki AUTO_INCREMENT pada tabel MySQL dengan mematikan foreign key checks sementara.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // PENTING: Wajib matikan foreign key checks agar MySQL mengizinkan ALTER TABLE MODIFY id pada tabel yang direferensikan
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        $tables = [
            'bookings',
            'booking_details',
            'payments',
            'reviews',
            'wa_logs',
            'users',
            'categories',
            'services',
            'nail_artists',
            'artist_schedules',
            'artist_leaves',
            'portfolios',
            'fcm_tokens',
            'password_otps',
            'jobs',
            'failed_jobs',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'id')) {
                try {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");
                    $maxId = (int) (DB::table($table)->max('id') ?? 0);
                    $nextId = $maxId + 1;
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
