<?php

// ==========================================================================
// BookingController.php
// Controller utama untuk mengelola alur pemesanan (booking) oleh pelanggan.
// Menangani pemilihan slot, upload desain, submit booking, pembayaran DP,
// reset flow, konfirmasi delay, pengecekan update, dan upload bukti bayar.
// ==========================================================================

namespace App\Http\Controllers;

use App\Jobs\SendFcmJob;
use App\Jobs\SendWaJob;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Payment;
use App\Models\Service;
use App\Models\WaLog;
use App\Services\SlotAvailabilityService;
use App\Services\WaTemplateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Memilih slot waktu dan terapis untuk booking (Step 2).
     * Memvalidasi ketersediaan slot sebelum menyimpan ke session.
     */
    public function selectSlot(Request $request)
    {
        // Validasi input: artist_id, tanggal booking, dan waktu mulai
        $validated = $request->validate([
            'artist_id' => 'required|integer|exists:nail_artists,id',
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i:s,H:i',
        ]);

        // Ambil data step 1 dari session (layanan yang sudah dipilih)
        $step1 = session('booking_step1');
        // Jika session step 1 tidak ada, berarti sudah kedaluwarsa
        if (! $step1) {
            return back()->withErrors(['booking' => 'Session step 1 expired. Silakan pilih layanan ulang.']);
        }

        // Cek ketersediaan slot melalui SlotAvailabilityService
        $slotService = app(SlotAvailabilityService::class);
        $check = $slotService->check(
            (int) $validated['artist_id'],
            $validated['booking_date'],
            $validated['start_time'],
            $step1['total_duration']
        );

        // Jika slot tidak tersedia, kembalikan pesan error ke pengguna
        if (! $check['available']) {
            return back()->withErrors(['slot' => $check['reason']]);
        }

        // Simpan data slot yang dipilih ke session step 1
        $step1['artist_id'] = (int) $validated['artist_id'];
        $step1['booking_date'] = $validated['booking_date'];
        $step1['start_time'] = $validated['start_time'];
        // Majukan ke step 3 (upload desain)
        session(['booking_step1' => $step1, 'booking_current_step' => 3]);

        return back()->with('success', 'Slot & terapis berhasil dipilih. Silakan upload desain referensi (opsional).');
    }

    /**
     * Mengunggah desain referensi nail art (Step 3 - opsional).
     * File desain disimpan ke storage publik, path lama dihapus jika ada file baru.
     */
    public function uploadDesign(Request $request)
    {
        // Pastikan session step 1 masih aktif
        $step1 = session('booking_step1');
        if (! $step1) {
            return back()->withErrors(['booking' => 'Session expired. Mulai ulang booking.']);
        }

        // Validasi file desain (opsional) dan catatan desain
        $validated = $request->validate([
            'design_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'design_notes' => 'nullable|string|max:2000',
        ]);

        // Ambil data step 2 yang sudah ada di session (jika ada)
        $step2 = session('booking_step2', []);

        // Data desain base64 sebelumnya (untuk keperluan penggantian jika diganti)
        $designBase64 = $step2['design_photo_base64'] ?? null;
        // Jika ada file desain baru yang diunggah, konversi ke Base64
        if ($request->hasFile('design_photo')) {
            $file = $request->file('design_photo');
            $mime = $file->getMimeType() ?: 'image/jpeg';
            // Konversi file desain ke format Base64 agar tersimpan langsung di database
            $designBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        }

        // Simpan data desain base64 dan catatan ke session step 2
        $step2['design_photo_base64'] = $designBase64;
        $step2['design_notes'] = $validated['design_notes'] ?? null;

        // Majukan ke step 4 (konfirmasi & upload bukti transfer DP)
        session(['booking_step2' => $step2, 'booking_current_step' => 4]);

        return back()->with('success', 'Desain tersimpan. Silakan lanjut ke konfirmasi & upload bukti transfer DP.');
    }

    /**
     * Submit booking final (Step 4).
     * Membuat record booking, detail layanan, pembayaran DP,
     * mengirim notifikasi WA dan FCM, serta memberikan loyalty points.
     */
    public function submitBooking(Request $request)
    {
        // Ambil data dari session step 1 dan step 2
        $step1 = session('booking_step1');
        $step2 = session('booking_step2', []);

        // Jika session step 1 sudah kedaluwarsa, arahkan ulang ke halaman booking
        if (! $step1) {
            return redirect()->route('booking.index')
                ->withErrors(['booking' => 'Session booking expired. Silakan mulai booking dari Step 1.']);
        }

        // Jumlah loyalty points yang ingin ditukar oleh pelanggan
        $redeemed = (int) ($request->input('loyalty_points_redeemed', 0));
        // Cek apakah ada file bukti DP yang diunggah
        $hasDpProof = $request->hasFile('dp_proof_file');

        // Validasi input: loyalty points, file bukti DP, dan metode pembayaran
        $request->validate([
            'loyalty_points_redeemed' => 'nullable|integer|min:0|max:'.(Auth::user()->loyalty_points ?? 0),
            'dp_proof_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:3072',
            'payment_method' => 'nullable|in:bank_transfer,qris,ewallet,cash',
        ], [
            'dp_proof_file.required' => 'Pelanggan wajib mengunggah bukti pembayaran DP agar pesanan dapat diproses & dikonfirmasi booking.',
            'dp_proof_file.file' => 'File bukti pembayaran harus berupa dokumen atau gambar yang valid.',
            'dp_proof_file.mimes' => 'Format bukti pembayaran harus JPG, PNG, WEBP, atau PDF.',
            'dp_proof_file.max' => 'Ukuran file bukti pembayaran maksimal 3MB.',
        ]);

        // Mulai transaksi database untuk menjaga konsistensi data
        DB::beginTransaction();
        // Menyimpan path file yang diupload untuk rollback jika terjadi error
        $uploadedPaths = [];
        try {
            // Ambil data layanan yang dipilih dari database
            $services = Service::whereIn('id', $step1['service_ids'])->get();
            // Hitung total harga dari semua layanan yang dipilih
            $totalPrice = (int) $services->sum('price');
            // Hitung diskon dari loyalty points (setiap 100 poin = Rp 10.000)
            $discount = (int) (floor($redeemed / 100) * 10000);
            // Total harga akhir setelah diskon, minimal 0
            $finalTotal = max(0, $totalPrice - $discount);
            // DP sebesar 30% dari total harga akhir
            $dpAmount = (int) ceil($finalTotal * 0.3);
            // Hitung waktu selesai berdasarkan waktu mulai + total durasi
            $endTime = Carbon::parse("{$step1['booking_date']} {$step1['start_time']}")
                ->addMinutes($step1['total_duration'])
                ->format('H:i:s');

            // Cek ulang ketersediaan slot sebelum insert (mencegah race condition)
            $slotService = app(SlotAvailabilityService::class);
            $slotCheck = $slotService->check(
                $step1['artist_id'],
                $step1['booking_date'],
                $step1['start_time'],
                $step1['total_duration']
            );
            // Jika slot sudah tidak tersedia, lempar exception
            if (! $slotCheck['available']) {
                throw new \RuntimeException('Slot tidak tersedia: '.$slotCheck['reason']);
            }

            // Generate kode booking unik dengan mekanisme retry hingga 3 kali
            $bookingCode = null;
            for ($attempt = 0; $attempt < 3; $attempt++) {
                try {
                    $bookingCode = Booking::generateBookingCode();
                    break;
                } catch (\Throwable $e) {
                    // Jika gagal 3 kali berturut-turut, lempar exception terakhir
                    if ($attempt === 2) {
                        throw $e;
                    }
                    // Tunggu 100ms sebelum retry
                    usleep(100000);
                }
            }

            // Jika pelanggan menukar loyalty points, kurangi saldo poin
            if ($redeemed > 0) {
                // Ambil data user terbaru dari database untuk menghindari stale data
                $freshUser = Auth::user()->fresh();
                // Validasi bahwa saldo poin mencukupi
                if ($freshUser->loyalty_points < $redeemed) {
                    throw new \RuntimeException('Saldo loyalty points tidak mencukupi. Refresh dan coba lagi.');
                }
                $freshUser->decrement('loyalty_points', $redeemed);
            }

            // Konversi file bukti DP ke format Base64 untuk disimpan di database
            $dpProofBase64 = null;
            if ($hasDpProof) {
                $file = $request->file('dp_proof_file');
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $dpProofBase64 = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
            }

            // Ambil data desain Base64 dari session step 2
            $designBase64 = $step2['design_photo_base64'] ?? null;

            // Buat record booking utama di database
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'customer_id' => Auth::id(),
                'artist_id' => $step1['artist_id'],
                'booking_date' => $step1['booking_date'],
                'start_time' => $step1['start_time'],
                'end_time' => $endTime,
                'custom_design_photo' => $designBase64,
                'design_notes' => $step2['design_notes'] ?? null,
                'total_price' => $finalTotal,
                'dp_amount' => $dpAmount,
                'loyalty_points_used' => $redeemed,
                'loyalty_discount' => $discount,
                'status' => Booking::STATUS_PENDING,
                'is_walk_in' => false,
            ]);

            // Buat detail booking untuk setiap layanan yang dipilih
            foreach ($services as $svc) {
                BookingDetail::create([
                    'booking_id' => $booking->id,
                    'service_id' => $svc->id,
                    'price' => $svc->price,
                    'duration_minutes' => $svc->duration_minutes,
                ]);
            }

            // Simpan record pembayaran DP jika bukti sudah diunggah
            if ($hasDpProof) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'proof_file' => $dpProofBase64,
                    'amount' => $dpAmount,
                    'payment_type' => 'dp',
                    'payment_method' => $request->input('payment_method', 'bank_transfer'),
                    'status' => Payment::STATUS_PENDING,
                    'paid_at' => now(),
                ]);
            }

            // Kirim notifikasi WhatsApp ke pelanggan tentang booking baru
            $msg = WaTemplateService::buildMessage(WaLog::TYPE_BOOKING_CREATED, [
                'customer_name' => Auth::user()->name,
                'booking_code' => $booking->booking_code,
                'artist_name' => optional($booking->artist->user)->name ?? '-',
                'date' => $booking->formatted_booking_date,
                'time' => $booking->time_range,
                'total' => $booking->formatted_total_price,
                'dp' => $booking->formatted_dp_amount,
                'payment_instructions' => $hasDpProof ? 'Bukti transfer sudah diupload, menunggu verifikasi admin (maks 1x24 jam).' : 'Silakan transfer DP sebesar '.$booking->formatted_dp_amount.' lalu upload bukti transfer di Dashboard → Booking → Upload Bukti DP.',
            ]);
            // Catat log pengiriman WA ke database
            $waLog = WaLog::create([
                'booking_id' => $booking->id,
                'phone_number' => Auth::user()->phone ?? '',
                'message_type' => WaLog::TYPE_BOOKING_CREATED,
                'message_body' => $msg,
                'status' => WaLog::STATUS_QUEUED,
            ]);

            // Dispatch job pengiriman WA jika nomor telepon tersedia
            if (Auth::user()->phone && class_exists(SendWaJob::class)) {
                try {
                    SendWaJob::dispatch($waLog->id);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            // FCM: Kirim notifikasi push ke semua admin untuk konfirmasi booking baru
            try {
                $confirmUrl = route('admin.bookings.index', ['q' => $booking->booking_code]);
                SendFcmJob::dispatchAfterResponse(
                    null,
                    true, // toAdmins
                    '📝 Booking Baru Masuk! Butuh Konfirmasi',
                    Auth::user()->name.' melakukan booking baru ('.$booking->booking_code.'). Klik untuk konfirmasi status booking.',
                    [
                        'booking_code' => $booking->booking_code,
                        'booking_id' => (string) $booking->id,
                        'customer_name' => Auth::user()->name,
                        'url' => $confirmUrl,
                        'click_action' => $confirmUrl,
                        'action' => 'confirm',
                    ]
                );
            } catch (\Throwable $e) {
                report($e);
            }

            // FCM: Kirim notifikasi push ke pelanggan tentang booking yang berhasil dibuat
            try {
                $dashboardUrl = route('dashboard');
                SendFcmJob::dispatchAfterResponse(
                    Auth::id(),
                    false, // toCustomer
                    '🎉 Booking Anda Berhasil Dibuat!',
                    "Booking #{$booking->booking_code} berhasil dibuat. Silakan transfer DP {$booking->formatted_dp_amount} untuk mengamankan jadwal Anda.",
                    [
                        'booking_code' => $booking->booking_code,
                        'booking_id' => (string) $booking->id,
                        'url' => $dashboardUrl,
                        'click_action' => $dashboardUrl,
                    ]
                );
            } catch (\Throwable $e) {
                report($e);
            }

            // Commit transaksi jika semua proses berhasil
            DB::commit();
        } catch (\Throwable $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            // Hapus file yang sudah diupload untuk membersihkan storage
            foreach ($uploadedPaths as $p) {
                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            return back()->withErrors(['submit' => 'Booking gagal: '.$e->getMessage()])->withInput();
        }

        // Bersihkan semua data session booking setelah berhasil
        session()->forget(['booking_step1', 'booking_step2', 'booking_step3', 'booking_current_step']);

        return redirect()->route('dashboard')
            ->with('success', "Booking berhasil dibuat dengan kode {$booking->booking_code}. Total: {$booking->formatted_total_price}. DP: {$booking->formatted_dp_amount}. Terima kasih!");
    }

    /**
     * Mereset seluruh alur booking dan mengembalikan ke Step 1.
     * Menghapus file desain yang sudah diupload serta membersihkan session.
     */
    public function resetFlow(Request $request)
    {
        // Bersihkan semua session booking (desain sudah disimpan sebagai Base64, tidak perlu hapus file)
        session()->forget(['booking_step1', 'booking_step2', 'booking_step3', 'booking_current_step']);

        return redirect()->route('booking.index')->with('success', 'Flow booking direset.');
    }

    /**
     * Menangani konfirmasi atau permintaan reschedule dari pelanggan
     * ketika terjadi keterlambatan pada jadwal booking mereka.
     */
    public function ackDelay(Request $request, Booking $booking)
    {
        // Hanya pemilik booking atau admin yang dapat mengakses
        abort_unless($booking->customer_id === Auth::id() || (Auth::check() && Auth::user()->hasRole('admin')), 403);

        // Ambil aksi yang dipilih pelanggan: 'confirmed' atau 'reschedule'
        $action = $request->input('action', 'confirmed');

        // Jika pelanggan mengonfirmasi jadwal baru
        if ($action === 'confirmed') {
            $booking->update(['customer_delay_ack' => 'confirmed_new_time']);

            return back()->with('success', "Terima kasih! Jadwal baru kamu untuk booking {$booking->booking_code} telah dikonfirmasi.");
        }

        // Jika pelanggan meminta reschedule
        $booking->update(['customer_delay_ack' => 'reschedule_requested']);

        return back()->with('success', "Permintaan reschedule untuk booking {$booking->booking_code} telah dicatat. Admin kami akan segera menghubungi kamu via WhatsApp.");
    }

    /**
     * Endpoint polling untuk memeriksa apakah ada pembaruan status booking
     * pada sisi pelanggan (digunakan oleh frontend untuk real-time update).
     */
    public function checkCustomerUpdates(Request $request)
    {
        $user = Auth::user();
        // Jika pengguna tidak terautentikasi, kembalikan respons kosong
        if (! $user) {
            return response()->json(['has_update' => false]);
        }

        // Parameter 'since' digunakan sebagai penanda waktu polling terakhir
        $since = $request->get('since');
        // Jika tidak ada parameter since, kembalikan waktu server saja
        if (! $since) {
            return response()->json([
                'has_update' => false,
                'server_time' => now()->toIso8601String(),
            ]);
        }

        // Parse waktu 'since' dengan fallback 30 detik ke belakang jika gagal
        try {
            $sinceDate = \Carbon\Carbon::parse($since);
        } catch (\Throwable $e) {
            $sinceDate = now()->subSeconds(30);
        }

        // Cari booking pelanggan yang diperbarui setelah waktu 'since'
        $booking = Booking::where('customer_id', $user->id)
            ->where('updated_at', '>', $sinceDate)
            ->latest('updated_at')
            ->first();

        // Jika ada booking yang baru diperbarui, kirimkan data notifikasi
        if ($booking) {
            // Mapping judul notifikasi berdasarkan status booking
            $statusTitles = [
                Booking::STATUS_CONFIRMED => '✅ Pembayaran DP Dikonfirmasi!',
                Booking::STATUS_IN_PROGRESS => '💅 Treatment Sedang Berlangsung',
                Booking::STATUS_COMPLETED => '✨ Treatment Selesai! Terima Kasih',
                Booking::STATUS_CANCELLED => '🚫 Booking Dibatalkan',
            ];

            // Mapping pesan notifikasi berdasarkan status booking
            $statusMessages = [
                Booking::STATUS_CONFIRMED => "Pembayaran DP untuk booking {$booking->booking_code} telah diverifikasi. Jadwal Anda pada {$booking->formatted_booking_date} ({$booking->time_range}) siap!",
                Booking::STATUS_IN_PROGRESS => "Sesi nail art booking {$booking->booking_code} sedang berlangsung. Selamat menikmati perawatan!",
                Booking::STATUS_COMPLETED => "Treatment booking {$booking->booking_code} telah selesai. Poin loyalty Anda telah bertambah. Terima kasih!",
                Booking::STATUS_CANCELLED => "Booking {$booking->booking_code} telah dibatalkan.",
            ];

            // Gunakan pesan default jika status tidak ada dalam mapping
            $title = $statusTitles[$booking->status] ?? "Pembaruan Booking #{$booking->booking_code}";
            $message = $statusMessages[$booking->status] ?? "Status booking Anda telah diperbarui menjadi {$booking->status}.";

            return response()->json([
                'has_update' => true,
                'server_time' => now()->toIso8601String(),
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->status,
                    'url' => route('dashboard'),
                ],
            ]);
        }

        // Tidak ada pembaruan, kembalikan flag false
        return response()->json([
            'has_update' => false,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Mengunggah bukti pembayaran DP untuk booking yang sudah ada (status PENDING).
     * Bukti disimpan sebagai Base64 di database dan mengirim notifikasi ke admin.
     */
    public function uploadPaymentProof(Request $request, Booking $booking)
    {
        // Hanya pemilik booking atau admin yang boleh upload bukti bayar
        abort_unless($booking->customer_id === Auth::id() || (Auth::check() && Auth::user()->hasRole('admin')), 403);
        // Hanya booking dengan status PENDING yang dapat mengunggah bukti DP
        abort_unless($booking->status === Booking::STATUS_PENDING, 400, 'Hanya booking PENDING yang dapat mengunggah bukti DP.');

        // Validasi file bukti pembayaran
        $request->validate([
            'dp_proof_file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:3072',
            'payment_method' => 'nullable|in:bank_transfer,qris,ewallet,cash',
        ], [
            'dp_proof_file.required' => 'Bukti pembayaran wajib diunggah.',
            'dp_proof_file.file' => 'File bukti pembayaran harus berupa dokumen atau gambar yang valid.',
            'dp_proof_file.mimes' => 'Format file harus JPG, PNG, WEBP, atau PDF.',
            'dp_proof_file.max' => 'Ukuran file bukti pembayaran maksimal 3MB.',
        ]);

        // Konversi file bukti DP ke format Base64
        $file = $request->file('dp_proof_file');
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $base64Proof = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));

        // Bersihkan file fisik lama jika sebelumnya tersimpan di storage lokal
        $existingPayment = $booking->payments()->where('payment_type', 'dp')->first();
        if ($existingPayment && $existingPayment->proof_file && ! str_starts_with($existingPayment->proof_file, 'data:')) {
            if (Storage::disk('public')->exists($existingPayment->proof_file)) {
                Storage::disk('public')->delete($existingPayment->proof_file);
            }
        }

        // Update atau buat record pembayaran DP dengan bukti Base64
        Payment::updateOrCreate(
            ['booking_id' => $booking->id, 'payment_type' => 'dp'],
            [
                'proof_file' => $base64Proof,
                'amount' => $booking->dp_amount,
                'payment_method' => $request->input('payment_method', 'bank_transfer'),
                'status' => Payment::STATUS_PENDING,
                'paid_at' => now(),
            ]
        );

        // Kirim notifikasi FCM ke admin bahwa pelanggan sudah upload bukti bayar
        try {
            $confirmUrl = route('admin.bookings.index', ['q' => $booking->booking_code]);
            SendFcmJob::dispatchAfterResponse(
                null,
                true, // toAdmins
                '📸 Bukti Transfer DP Diunggah!',
                Auth::user()->name." telah mengunggah bukti pembayaran untuk booking {$booking->booking_code}. Silakan periksa & konfirmasi.",
                [
                    'booking_code' => $booking->booking_code,
                    'url' => $confirmUrl,
                    'click_action' => $confirmUrl,
                ]
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', "Bukti pembayaran untuk booking {$booking->booking_code} berhasil diunggah! Menunggu konfirmasi admin.");
    }
}
