<?php

// =============================================================================
// Migration: Membuat tabel untuk sistem antrian (queue) Laravel.
// Terdiri dari tabel jobs, job_batches, dan failed_jobs untuk mengelola
// pekerjaan latar belakang, batch processing, dan pencatatan kegagalan.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tiga tabel untuk infrastruktur queue: jobs, job_batches, dan failed_jobs.
     */
    public function up(): void
    {
        // Tabel 'jobs' — menyimpan pekerjaan yang menunggu diproses oleh queue worker
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('queue')->index(); // Nama antrian (misalnya 'default', 'emails'), diindeks untuk pencarian cepat
            $table->longText('payload'); // Data pekerjaan yang di-serialize (berisi class, parameter, dll)
            $table->unsignedSmallInteger('attempts'); // Jumlah percobaan eksekusi pekerjaan
            $table->unsignedInteger('reserved_at')->nullable(); // Waktu saat pekerjaan sedang diproses oleh worker, null jika belum diambil
            $table->unsignedInteger('available_at'); // Waktu paling awal pekerjaan bisa diproses (untuk delayed job)
            $table->unsignedInteger('created_at'); // Waktu pekerjaan ditambahkan ke antrian
        });

        // Tabel 'job_batches' — menyimpan metadata batch job untuk fitur Job Batching Laravel
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID batch sebagai primary key
            $table->string('name'); // Nama deskriptif batch
            $table->integer('total_jobs'); // Total jumlah pekerjaan dalam batch
            $table->integer('pending_jobs'); // Jumlah pekerjaan yang belum selesai
            $table->integer('failed_jobs'); // Jumlah pekerjaan yang gagal
            $table->longText('failed_job_ids'); // Daftar ID pekerjaan yang gagal (JSON)
            $table->mediumText('options')->nullable(); // Opsi batch (callback then/catch/finally) yang di-serialize
            $table->integer('cancelled_at')->nullable(); // Timestamp pembatalan batch, null jika belum dibatalkan
            $table->integer('created_at'); // Waktu batch dibuat
            $table->integer('finished_at')->nullable(); // Waktu batch selesai, null jika masih berjalan
        });

        // Tabel 'failed_jobs' — mencatat pekerjaan yang gagal dieksekusi untuk keperluan debugging dan retry
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('uuid')->unique(); // UUID unik untuk identifikasi pekerjaan gagal
            $table->string('connection'); // Nama koneksi queue yang digunakan (misalnya 'database', 'redis')
            $table->string('queue'); // Nama antrian tempat pekerjaan gagal
            $table->longText('payload'); // Data pekerjaan yang gagal (di-serialize)
            $table->longText('exception'); // Pesan dan stack trace exception yang menyebabkan kegagalan
            $table->timestamp('failed_at')->useCurrent(); // Waktu kegagalan, otomatis diisi saat record dibuat

            // Indeks komposit untuk pencarian berdasarkan koneksi, antrian, dan waktu gagal
            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus semua tabel queue.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
