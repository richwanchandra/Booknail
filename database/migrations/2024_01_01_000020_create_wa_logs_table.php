<?php

// =============================================================================
// Migration: Membuat tabel wa_logs untuk mencatat log pengiriman pesan WhatsApp.
// Digunakan untuk melacak status notifikasi WA yang dikirim ke pelanggan
// (booking baru, konfirmasi DP, pengingat, pembatalan, selesai, keterlambatan).
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel 'wa_logs' untuk mencatat riwayat pengiriman notifikasi WhatsApp.
     */
    public function up(): void
    {
        Schema::create('wa_logs', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('phone_number', 20); // Nomor telepon tujuan pesan WhatsApp
            // Tipe pesan WA yang dikirim, menentukan template pesan yang digunakan
            $table->enum('message_type', [
                'booking_created',    // Notifikasi booking baru dibuat
                'dp_confirmed',       // Konfirmasi DP telah diverifikasi
                'reminder_h1',        // Pengingat H-1 sebelum jadwal booking
                'reminder_2h',        // Pengingat 2 jam sebelum jadwal booking
                'booking_cancelled',  // Notifikasi booking dibatalkan
                'booking_completed',  // Notifikasi layanan telah selesai
                'schedule_delayed',   // Notifikasi keterlambatan jadwal
            ]);
            $table->text('message_body'); // Isi pesan WA yang dikirim
            // Status pengiriman pesan: queued (antrian) → sent (terkirim) / failed (gagal)
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->string('provider_message_id')->nullable(); // ID pesan dari provider WA API (untuk tracking)
            $table->text('error_message')->nullable(); // Pesan error jika pengiriman gagal
            $table->timestamp('sent_at')->nullable(); // Waktu aktual pesan berhasil terkirim
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete(); // Relasi ke booking terkait, null jika booking dihapus
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Menghapus tabel wa_logs.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_logs');
    }
};
