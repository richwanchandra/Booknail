<?php

// =============================================================================
// Migration: Membuat tabel portfolios untuk menampilkan galeri karya nail artist.
// Portfolio bisa dikaitkan dengan kategori, layanan, dan artist tertentu.
// Gambar disimpan sebagai Base64 Data URI, path storage, atau URL eksternal.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel 'portfolios' untuk galeri karya nail artist.
     */
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('title'); // Judul portfolio (misalnya "French Tip Rose Gold")
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete(); // Kategori terkait, null jika kategori dihapus
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete(); // Layanan terkait, null jika layanan dihapus
            $table->foreignId('nail_artist_id')->nullable()->constrained('nail_artists')->nullOnDelete(); // Artist pembuat karya, null jika artist dihapus
            $table->longText('image'); // Base64 data URI or storage path or URL — gambar portfolio
            $table->text('description')->nullable(); // Deskripsi detail tentang karya portfolio
            $table->string('tags')->nullable(); // e.g. "French, Rose Gold, Glitter" — tag untuk pencarian dan filter
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif portfolio, default aktif
            $table->integer('sort_order')->default(0); // Urutan tampilan portfolio (angka kecil tampil lebih dulu)
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     * Menghapus tabel portfolios.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
