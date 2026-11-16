<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memperbaiki default value untuk kolom-kolom tabel bookings di MySQL strict mode.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        if (Schema::hasTable('bookings')) {
            if (Schema::hasColumn('bookings', 'delay_minutes')) {
                DB::statement('ALTER TABLE `bookings` MODIFY `delay_minutes` INT NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('bookings', 'is_delayed')) {
                DB::statement('ALTER TABLE `bookings` MODIFY `is_delayed` TINYINT(1) NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('bookings', 'artist_confirmation_status')) {
                DB::statement("ALTER TABLE `bookings` MODIFY `artist_confirmation_status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
            }
            if (Schema::hasColumn('bookings', 'booking_source')) {
                DB::statement("ALTER TABLE `bookings` MODIFY `booking_source` VARCHAR(30) NOT NULL DEFAULT 'WEB_ONLINE'");
            }
            if (Schema::hasColumn('bookings', 'branch')) {
                DB::statement("ALTER TABLE `bookings` MODIFY `branch` VARCHAR(50) NULL DEFAULT 'Citra Garden 6'");
            }
            if (Schema::hasColumn('bookings', 'is_walk_in')) {
                DB::statement('ALTER TABLE `bookings` MODIFY `is_walk_in` TINYINT(1) NOT NULL DEFAULT 0');
            }
            if (Schema::hasColumn('bookings', 'status')) {
                DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled') NOT NULL DEFAULT 'pending'");
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
