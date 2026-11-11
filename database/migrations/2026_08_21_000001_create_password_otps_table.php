<?php

// =============================================================================
// Migration: Membuat tabel password_otps untuk fitur reset password menggunakan OTP.
// Menyimpan kode OTP yang dikirim ke email pengguna beserta waktu kedaluwarsa.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel 'password_otps' untuk menyimpan OTP reset password.
     */
    public function up(): void
    {
        Schema::create('password_otps', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('email')->index(); // Email pengguna yang meminta reset, diindeks untuk pencarian cepat
            $table->string('otp'); // Kode OTP (One-Time Password) yang dikirim ke email
            $table->timestamp('expires_at'); // Waktu kedaluwarsa OTP, setelah waktu ini OTP tidak valid lagi
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus tabel password_otps.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_otps');
    }
};
