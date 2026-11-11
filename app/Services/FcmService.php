<?php

// ==========================================================================
// File: FcmService.php
// Fungsi: Service untuk mengirim push notification melalui Firebase Cloud
//         Messaging (FCM) menggunakan Legacy HTTP API. Mendukung pengiriman
//         ke token tertentu, ke semua token milik satu user, atau ke semua admin.
// ==========================================================================

namespace App\Services;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    // Server key FCM dari konfigurasi Firebase
    protected string $serverKey;

    // Konstruktor: inisialisasi server key dari config/services.php
    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key', '');
    }

    /**
     * Send a push notification to one or more FCM registration tokens.
     *
     * @param  string|array  $tokens  One token string or array of token strings
     * @param  string  $title
     * @param  string  $body
     * @param  array   $data  Optional custom data payload
     * @return bool
     */
    // Metode utama untuk mengirim push notification ke satu atau banyak token FCM.
    // Mengembalikan true jika proses pengiriman berhasil dijalankan (bukan jaminan terkirim).
    public function send(string|array $tokens, string $title, string $body, array $data = []): bool
    {
        // Jika server key belum dikonfigurasi, lewati pengiriman
        if (empty($this->serverKey)) {
            Log::warning('FCM: server key not configured. Skipping push notification.');
            return false;
        }

        // Filter token yang kosong/null, lalu re-index array
        $tokens = array_values(array_filter((array) $tokens));
        if (empty($tokens)) {
            Log::info('FCM: No valid tokens to deliver push notification.');
            return false;
        }

        // Tentukan URL tujuan saat notifikasi diklik (click_action)
        $targetUrl = $data['url'] ?? $data['click_action'] ?? (function_exists('route') && \Illuminate\Support\Facades\Route::has('admin.bookings.index') ? route('admin.bookings.index') : url('/'));

        // FCM Legacy HTTP API supports up to 1000 tokens per request
        // Kirim notifikasi dalam batch 1000 token per request sesuai batas FCM
        foreach (array_chunk($tokens, 1000) as $chunk) {
            // Bangun payload notifikasi sesuai format FCM Legacy API
            $payload = [
                'registration_ids' => $chunk,
                'priority' => 'high',
                'content_available' => true,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'icon'  => url('/favicon.ico'),
                    'click_action' => $targetUrl,
                ],
                'data' => array_merge([
                    'title' => $title,
                    'body' => $body,
                    'url' => $targetUrl,
                    'click_action' => $targetUrl,
                ], $data),
            ];

            try {
                // Kirim HTTP POST ke endpoint FCM dengan header Authorization
                $response = Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type'  => 'application/json',
                ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', $payload);

                Log::info('FCM: push notification dispatched', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                // Log error jika response status bukan 2xx
                if (! $response->successful()) {
                    Log::error('FCM: request failed', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                // Tangkap exception jaringan atau lainnya agar tidak menghentikan proses
                Log::error('FCM: exception during send', ['message' => $e->getMessage()]);
            }
        }

        return true;
    }

    /**
     * Send a notification to all FCM tokens belonging to a user.
     */
    // Kirim notifikasi ke semua perangkat (token FCM) milik user tertentu
    public function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        // Ambil semua token FCM unik milik user
        $tokens = $user->fcmTokens()->pluck('token')->unique()->toArray();
        if (empty($tokens)) {
            return false;
        }

        return $this->send($tokens, $title, $body, $data);
    }

    /**
     * Send a notification to all admin users.
     */
    // Kirim notifikasi ke semua perangkat milik user yang memiliki role 'admin'
    public function sendToAdmins(string $title, string $body, array $data = []): bool
    {
        // Query token FCM dari semua user dengan role admin
        $tokens = FcmToken::whereHas('user', fn ($q) => $q->role('admin'))
            ->pluck('token')
            ->unique()
            ->toArray();

        if (empty($tokens)) {
            Log::info('FCM: No admin registered FCM tokens found.');
            return false;
        }

        return $this->send($tokens, $title, $body, $data);
    }
}
