<?php

// ==========================================================================
// File: FonnteDriver.php
// Fungsi: Implementasi WaGatewayInterface menggunakan layanan Fonnte
//         (https://fonnte.com) sebagai penyedia API WhatsApp. Menangani
//         pengiriman pesan WA termasuk normalisasi format nomor telepon Indonesia.
// ==========================================================================

namespace App\Services\WaGateway;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteDriver implements WaGatewayInterface
{
    // API key Fonnte untuk autentikasi request
    protected string $apiKey;

    // Base URL endpoint API Fonnte
    protected string $baseUrl;

    // Konstruktor: ambil konfigurasi API key dan base URL dari config/services.php
    public function __construct()
    {
        $this->apiKey = config('services.fonnte.key', '');
        $this->baseUrl = rtrim(config('services.fonnte.base_url', 'https://api.fonnte.com'), '/');
    }

    // Mengirim pesan WhatsApp ke nomor telepon tertentu melalui API Fonnte.
    // Mengembalikan array dengan key: success, providerId, error.
    public function send(string $phoneNumber, string $message): array
    {
        // Jika API key belum dikonfigurasi, kembalikan error
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'providerId' => null,
                'error' => 'FONNTE_API_KEY tidak diset di .env. Gunakan MockDriver atau isi key.',
            ];
        }

        // Normalisasi format nomor telepon ke format internasional (628xxx)
        $phone = $this->normalizePhone($phoneNumber);
        if (empty($phone)) {
            return ['success' => false, 'providerId' => null, 'error' => 'Nomor WhatsApp kosong / tidak valid.'];
        }

        try {
            // Kirim POST request ke endpoint /send milik Fonnte
            $url = "{$this->baseUrl}/send";
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => $this->apiKey])
                ->asForm()
                ->post($url, [
                    'target' => $phone,
                    'message' => $message,
                    'delay' => '0',  // Kirim langsung tanpa delay
                ]);

            // Parse response body dari Fonnte
            $body = $response->json() ?? [];

            // Fonnte mengembalikan status keberhasilan melalui key 'status' atau 'success'
            $isOk = (bool) ($body['status'] ?? false) || (bool) ($body['success'] ?? false);
            Log::info('[FONNTE] send', ['phone' => $phone, 'status' => $response->status(), 'body' => $body]);

            return [
                'success' => $isOk,
                'providerId' => $body['id'] ?? null,
                'error' => $isOk ? null : ($body['reason'] ?? $body['message'] ?? ('HTTP '.$response->status())),
            ];
        } catch (\Throwable $e) {
            // Tangkap exception jaringan atau lainnya, log error lengkap
            Log::error('[FONNTE] exception', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return [
                'success' => false,
                'providerId' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    // Normalisasi nomor telepon Indonesia ke format internasional tanpa tanda "+".
    // Contoh: 08123456 → 628123456, +628123456 → 628123456
    protected function normalizePhone(string $phone): string
    {
        // Hapus semua karakter non-digit
        $num = preg_replace('/\D+/', '', $phone);

        // Konversi awalan 08 menjadi 628
        if (str_starts_with($num, '08')) {
            $num = '628'.substr($num, 2);
        } elseif (str_starts_with($num, '+62')) {
            // Konversi awalan +62 menjadi 62 (tanda + sudah dihapus oleh regex di atas)
            $num = '62'.substr($num, 3);
        }

        return $num;
    }
}
