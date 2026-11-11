<?php

// ==========================================================================
// ReviewController.php
// Controller untuk mengelola review/ulasan dari pelanggan setelah
// treatment booking selesai. Pelanggan hanya bisa memberi satu review
// per booking dan hanya untuk booking yang berstatus completed.
// ==========================================================================

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Menyimpan review baru untuk booking yang sudah selesai.
     * Mendukung upload foto review (opsional).
     */
    public function store(Request $request, Booking $booking)
    {
        // Pastikan hanya pemilik booking yang dapat memberi review
        abort_if(Auth::id() !== (int) $booking->customer_id, 403, 'Kamu tidak berhak memberi review untuk booking ini.');
        // Pastikan booking sudah berstatus completed
        abort_if($booking->status !== Booking::STATUS_COMPLETED, 400, 'Review hanya bisa diberikan untuk booking yang sudah selesai.');
        // Pastikan booking belum memiliki review (satu booking = satu review)
        abort_if($booking->review()->exists(), 409, 'Booking ini sudah memiliki review. Satu booking hanya satu review.');

        // Validasi input: rating wajib 1-5, komentar dan foto opsional
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
            'review_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Proses upload foto review jika ada, konversi langsung ke format Base64 Data URI
        $photoBase64 = null;
        if ($request->hasFile('review_photo')) {
            $file = $request->file('review_photo');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $photoBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        }

        try {
            // Simpan review ke database dengan foto Base64
            Review::create([
                'booking_id' => $booking->id,
                'customer_id' => Auth::id(),
                'artist_id' => $booking->artist_id,
                'rating' => (int) $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'photo' => $photoBase64,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['review' => 'Gagal menyimpan review: '.$e->getMessage()]);
        }

        return back()->with('success', 'Terima kasih! Review kamu berhasil dikirim.');
    }
}
