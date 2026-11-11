<?php

// =============================================================================
// Migration: Membuat tabel-tabel untuk paket Spatie Laravel Permission.
// Tabel yang dibuat: permissions, roles, model_has_permissions, model_has_roles,
// dan role_has_permissions. Mendukung fitur teams jika dikonfigurasi.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat seluruh tabel yang dibutuhkan oleh Spatie Permission.
     */
    public function up(): void
    {
        // Ambil konfigurasi dari config/permission.php
        $teams = config('permission.teams'); // Apakah fitur teams diaktifkan
        $tableNames = config('permission.table_names'); // Nama-nama tabel yang dikonfigurasi
        $columnNames = config('permission.column_names'); // Nama-nama kolom yang dikonfigurasi
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id'; // Nama kolom foreign key untuk role di tabel pivot
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id'; // Nama kolom foreign key untuk permission di tabel pivot

        // Validasi: pastikan konfigurasi sudah dimuat, jika belum lempar exception
        throw_if(empty($tableNames), 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        // Tabel 'permissions' — menyimpan daftar permission yang tersedia di sistem
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->id(); // permission id
            $table->string('name'); // Nama permission (misalnya "manage bookings", "view reports")
            $table->string('guard_name'); // Guard yang digunakan (misalnya "web", "api")
            $table->timestamps(); // Kolom created_at dan updated_at

            // Kombinasi nama dan guard harus unik
            $table->unique(['name', 'guard_name']);
        });

        /**
         * See `docs/prerequisites.md` for suggested lengths on 'name' and 'guard_name' if "1071 Specified key was too long" errors are encountered.
         */
        // Tabel 'roles' — menyimpan daftar role (misalnya admin, kasir, artist)
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->id(); // role id
            // Jika fitur teams aktif, tambahkan kolom foreign key untuk team
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable(); // Foreign key ke tabel teams
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name'); // Nama role (misalnya "admin", "nail_artist", "customer")
            $table->string('guard_name'); // Guard yang digunakan
            $table->timestamps(); // Kolom created_at dan updated_at
            // Constraint unik berbeda tergantung apakah fitur teams aktif
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']); // Unik per team
            } else {
                $table->unique(['name', 'guard_name']); // Unik global
            }
        });

        // Tabel pivot 'model_has_permissions' — relasi langsung antara model (user) dan permission
        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission); // Foreign key ke tabel permissions

            $table->string('model_type'); // Tipe model polymorphic (misalnya "App\Models\User")
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID model (user_id)
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            // Foreign key constraint ke tabel permissions dengan cascade delete
            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();
            // Jika fitur teams aktif, tambahkan kolom team foreign key
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']); // Foreign key ke tabel teams
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                // Primary key komposit termasuk team
                $table->primary([$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                // Primary key komposit tanpa team
                $table->primary([$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }
        });

        // Tabel pivot 'model_has_roles' — relasi antara model (user) dan role
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole); // Foreign key ke tabel roles

            $table->string('model_type'); // Tipe model polymorphic
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID model (user_id)
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            // Foreign key constraint ke tabel roles dengan cascade delete
            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();
            // Jika fitur teams aktif, tambahkan kolom team foreign key
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']); // Foreign key ke tabel teams
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                // Primary key komposit termasuk team
                $table->primary([$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                // Primary key komposit tanpa team
                $table->primary([$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        // Tabel pivot 'role_has_permissions' — relasi many-to-many antara role dan permission
        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission); // Foreign key ke tabel permissions
            $table->unsignedBigInteger($pivotRole); // Foreign key ke tabel roles

            // Foreign key constraint ke tabel permissions
            $table->foreign($pivotPermission)
                ->references('id') // permission id
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            // Foreign key constraint ke tabel roles
            $table->foreign($pivotRole)
                ->references('id') // role id
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            // Primary key komposit dari permission dan role
            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        // Bersihkan cache permission setelah tabel dibuat agar konfigurasi terbaru digunakan
        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     * Menghapus semua tabel permission dalam urutan yang benar (tabel pivot dulu, lalu tabel utama).
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        // Validasi: pastikan konfigurasi tersedia untuk rollback
        throw_if(empty($tableNames), 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        // Hapus tabel dalam urutan terbalik (tabel yang memiliki foreign key dihapus terlebih dahulu)
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
