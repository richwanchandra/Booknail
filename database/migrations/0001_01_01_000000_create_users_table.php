<?php

// =============================================================================
// Migration: Membuat tabel dasar autentikasi pengguna (users, password_reset_tokens, sessions).
// File ini merupakan migration bawaan Laravel untuk sistem autentikasi dan manajemen sesi.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tiga tabel inti: users, password_reset_tokens, dan sessions.
     */
    public function up(): void
    {
        // Tabel 'users' — menyimpan data akun pengguna yang terdaftar
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name'); // Nama lengkap pengguna
            $table->string('email')->unique(); // Alamat email unik sebagai identitas login
            $table->timestamp('email_verified_at')->nullable(); // Waktu verifikasi email, null jika belum diverifikasi
            $table->string('password'); // Hash password pengguna
            $table->rememberToken(); // Token "remember me" untuk sesi persisten
            $table->timestamps(); // Kolom created_at dan updated_at
        });

        // Tabel 'password_reset_tokens' — menyimpan token untuk fitur reset password
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email sebagai primary key, satu token per email
            $table->string('token'); // Token acak yang dikirim ke email pengguna
            $table->timestamp('created_at')->nullable(); // Waktu pembuatan token, untuk menentukan masa berlaku
        });

        // Tabel 'sessions' — menyimpan data sesi pengguna saat menggunakan driver database
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID sesi unik sebagai primary key
            $table->foreignId('user_id')->nullable()->index(); // Relasi ke tabel users, null jika pengguna belum login (guest)
            $table->string('ip_address', 45)->nullable(); // Alamat IP pengguna (mendukung IPv6 hingga 45 karakter)
            $table->text('user_agent')->nullable(); // Informasi browser/perangkat pengguna
            $table->longText('payload'); // Data sesi yang di-serialize
            $table->integer('last_activity')->index(); // Timestamp aktivitas terakhir, diindeks untuk pembersihan sesi kadaluarsa
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus semua tabel yang dibuat pada method up().
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
