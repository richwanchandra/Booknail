<?php

// =============================================================================
// Migration: Menambahkan kolom konfirmasi artist ke tabel bookings.
// Fitur ini memungkinkan nail artist untuk mengkonfirmasi atau menolak booking
// yang ditugaskan kepadanya, termasuk catatan dari artist.
// =============================================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom-kolom konfirmasi artist ke tabel bookings.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Status konfirmasi artist: 'pending' (menunggu), 'accepted' (diterima), 'rejected' (ditolak)
            if (! Schema::hasColumn('bookings', 'artist_confirmation_status')) {
                $table->string('artist_confirmation_status', 30)->default('pending')->after('customer_delay_ack');
            }
            // Waktu saat artist mengkonfirmasi booking
            if (! Schema::hasColumn('bookings', 'artist_confirmed_at')) {
                $table->dateTime('artist_confirmed_at')->nullable()->after('artist_confirmation_status');
            }
            // Catatan dari artist (misalnya alasan penolakan atau permintaan khusus)
            if (! Schema::hasColumn('bookings', 'artist_notes')) {
                $table->text('artist_notes')->nullable()->after('artist_confirmed_at');
            }
        });
    }

    /**
     * Rollback: Menghapus kolom-kolom konfirmasi artist dari tabel bookings.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Daftar kolom konfirmasi artist yang akan dihapus
            $cols = ['artist_confirmation_status', 'artist_confirmed_at', 'artist_notes'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('bookings', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
