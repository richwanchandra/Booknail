<?php

// =============================================================================
// Migration: Mengubah kolom proof_file pada tabel payments dari string ke longText.
// Selain mengubah tipe kolom, migration ini juga melakukan migrasi data:
// mengkonversi file bukti transfer fisik yang sudah ada ke format Base64 Data URI
// dan menghapus file fisiknya dari storage publik untuk menghemat ruang disk.
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
     * Mengubah proof_file ke longText dan migrasi file fisik ke Base64 Data URI.
     */
    public function up(): void
    {
        // Ubah tipe kolom proof_file menjadi longText untuk menampung data Base64
        Schema::table('payments', function (Blueprint $table) {
            $table->longText('proof_file')->nullable()->change();
        });

        // Migrasi bukti transfer fisik yang sudah ada ke format Base64 Data URI
        // dan bersihkan file fisiknya dari storage publik agar folder proyek tidak berat.
        // Hanya proses file yang bukan data URI (belum dikonversi)
        $payments = DB::table('payments')
            ->whereNotNull('proof_file')
            ->where('proof_file', 'not like', 'data:%') // Abaikan yang sudah berbentuk Base64 Data URI
            ->get();

        // Iterasi setiap pembayaran yang masih menyimpan path file fisik
        foreach ($payments as $payment) {
            $filePath = $payment->proof_file; // Path file relatif di storage publik
            // Cek apakah file fisik masih ada di storage
            if (Storage::disk('public')->exists($filePath)) {
                $content = Storage::disk('public')->get($filePath); // Baca konten file
                $mime = Storage::disk('public')->mimeType($filePath) ?: 'image/jpeg'; // Deteksi MIME type, fallback ke image/jpeg
                $base64Data = 'data:'.$mime.';base64,'.base64_encode($content); // Konversi ke format Data URI

                // Update record di database dengan data Base64
                DB::table('payments')->where('id', $payment->id)->update([
                    'proof_file' => $base64Data,
                ]);

                // Hapus file fisik lama agar storage bersih
                Storage::disk('public')->delete($filePath);
            }
        }
    }

    /**
     * Reverse the migrations.
     * Mengembalikan kolom proof_file ke tipe string(255).
     * PERINGATAN: Data Base64 yang sudah tersimpan akan terpotong dan tidak bisa dikembalikan ke file fisik.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_file', 255)->nullable()->change(); // Kembalikan ke VARCHAR(255)
        });
    }
};
