<?php

// =============================================================================
// Migration: Mengubah kolom custom_design_photo pada tabel bookings,
// kolom photo pada tabel nail_artists, dan kolom photo pada tabel reviews
// dari string (VARCHAR 255) ke longText.
// Mendukung penyimpanan gambar Base64 Data URI untuk setiap foto yang diunggah.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah kolom foto menjadi longText dan mengonversi file lama jika ada.
     */
    public function up(): void
    {
        // 1. Ubah kolom custom_design_photo di tabel bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->longText('custom_design_photo')->nullable()->change();
        });

        // 2. Ubah kolom photo di tabel nail_artists
        Schema::table('nail_artists', function (Blueprint $table) {
            $table->longText('photo')->nullable()->change();
        });

        // 3. Ubah kolom photo di tabel reviews
        Schema::table('reviews', function (Blueprint $table) {
            $table->longText('photo')->nullable()->change();
        });

        // Migrasi data file fisik lama di bookings ke Base64 Data URI
        $bookings = DB::table('bookings')
            ->whereNotNull('custom_design_photo')
            ->where('custom_design_photo', 'not like', 'data:%')
            ->get();

        foreach ($bookings as $b) {
            $filePath = $b->custom_design_photo;
            if (Storage::disk('public')->exists($filePath)) {
                $content = Storage::disk('public')->get($filePath);
                $mime = Storage::disk('public')->mimeType($filePath) ?: 'image/jpeg';
                $base64Data = 'data:'.$mime.';base64,'.base64_encode($content);

                DB::table('bookings')->where('id', $b->id)->update([
                    'custom_design_photo' => $base64Data,
                ]);

                Storage::disk('public')->delete($filePath);
            }
        }

        // Migrasi data file fisik lama di reviews ke Base64 Data URI
        $reviews = DB::table('reviews')
            ->whereNotNull('photo')
            ->where('photo', 'not like', 'data:%')
            ->get();

        foreach ($reviews as $r) {
            $filePath = $r->photo;
            if (Storage::disk('public')->exists($filePath)) {
                $content = Storage::disk('public')->get($filePath);
                $mime = Storage::disk('public')->mimeType($filePath) ?: 'image/jpeg';
                $base64Data = 'data:'.$mime.';base64,'.base64_encode($content);

                DB::table('reviews')->where('id', $r->id)->update([
                    'photo' => $base64Data,
                ]);

                Storage::disk('public')->delete($filePath);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('custom_design_photo', 255)->nullable()->change();
        });

        Schema::table('nail_artists', function (Blueprint $table) {
            $table->string('photo', 255)->nullable()->change();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->string('photo', 255)->nullable()->change();
        });
    }
};

