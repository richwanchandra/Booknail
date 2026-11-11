<?php

// =============================================================================
// Migration: Membuat tabel fcm_tokens untuk menyimpan token Firebase Cloud Messaging.
// Token FCM digunakan untuk mengirim push notification ke perangkat pengguna.
// Satu user bisa memiliki banyak token (untuk banyak perangkat).
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Membuat tabel 'fcm_tokens' untuk menyimpan token push notification.
     */
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel users, hapus token jika user dihapus
            $table->string('token')->unique(); // Token FCM unik per perangkat
            $table->timestamp('last_used_at')->nullable(); // Waktu terakhir token digunakan, untuk membersihkan token yang sudah tidak aktif
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus tabel fcm_tokens.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
