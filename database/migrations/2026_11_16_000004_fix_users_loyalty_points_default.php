<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memperbaiki default value kolom loyalty_points pada tabel users di MySQL.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'loyalty_points')) {
                DB::statement('ALTER TABLE `users` MODIFY `loyalty_points` INT UNSIGNED NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('users', 'phone')) {
                DB::statement('ALTER TABLE `users` MODIFY `phone` VARCHAR(20) NULL DEFAULT NULL');
            }
            if (Schema::hasColumn('users', 'id')) {
                try {
                    DB::statement('ALTER TABLE `users` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                } catch (\Throwable $e) {}
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
