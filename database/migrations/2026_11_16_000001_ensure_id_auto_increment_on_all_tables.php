<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memastikan kolom 'id' pada semua tabel memiliki atribut AUTO_INCREMENT di MySQL.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Daftar tabel utama aplikasi Booknail
        $targetTables = [
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

        // 1. Periksa tabel-tabel utama secara eksplisit
        foreach ($targetTables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'id')) {
                continue;
            }

            $this->ensureAutoIncrement($table);
        }

        // 2. Periksa seluruh tabel lain di database saat ini yang kolom id-nya belum AUTO_INCREMENT
        try {
            $databaseName = DB::getDatabaseName();
            $nonAutoIncrementTables = DB::select("
                SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND COLUMN_NAME = 'id'
                  AND LOWER(EXTRA) NOT LIKE '%auto_increment%'
            ", [$databaseName]);

            foreach ($nonAutoIncrementTables as $row) {
                $table = $row->TABLE_NAME;
                $this->ensureAutoIncrement($table);
            }
        } catch (\Throwable $e) {
            // Log warning jika query information_schema terbatas permission-nya
            report($e);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu me-revert AUTO_INCREMENT karena merupakan standar primary key
    }

    /**
     * Helper untuk menambahkan AUTO_INCREMENT pada kolom id tabel MySQL
     */
    protected function ensureAutoIncrement(string $table): void
    {
        try {
            $databaseName = DB::getDatabaseName();
            $col = DB::selectOne("
                SELECT COLUMN_TYPE, COLUMN_KEY, EXTRA
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = ?
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = 'id'
            ", [$databaseName, $table]);

            if ($col && !str_contains(strtolower($col->EXTRA ?? ''), 'auto_increment')) {
                // Pastikan primary key terdefinisi jika belum
                if (($col->COLUMN_KEY ?? '') !== 'PRI') {
                    $hasPk = DB::selectOne("
                        SELECT CONSTRAINT_NAME
                        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                        WHERE TABLE_SCHEMA = ?
                          AND TABLE_NAME = ?
                          AND CONSTRAINT_TYPE = 'PRIMARY KEY'
                    ", [$databaseName, $table]);

                    if (!$hasPk) {
                        DB::statement("ALTER TABLE `{$table}` ADD PRIMARY KEY (`id`)");
                    }
                }

                $columnType = !empty($col->COLUMN_TYPE) ? $col->COLUMN_TYPE : 'BIGINT UNSIGNED';
                DB::statement("ALTER TABLE `{$table}` MODIFY `id` {$columnType} NOT NULL AUTO_INCREMENT");

                // Sinkronkan counter AUTO_INCREMENT dengan MAX(id) yang ada saat ini
                $maxId = DB::table($table)->max('id') ?? 0;
                $nextId = (int)$maxId + 1;
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
};
