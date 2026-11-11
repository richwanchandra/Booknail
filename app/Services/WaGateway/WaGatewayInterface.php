<?php

// ==========================================================================
// File: WaGatewayInterface.php
// Fungsi: Interface (kontrak) untuk semua driver gateway WhatsApp.
//         Setiap implementasi (FonnteDriver, MockDriver, dll.) harus
//         mengimplementasikan metode send() dengan signature yang sama.
//         Menggunakan pola Strategy Pattern untuk memudahkan pergantian provider.
// ==========================================================================

namespace App\Services\WaGateway;

interface WaGatewayInterface
{
    // Mengirim pesan WhatsApp ke nomor telepon tertentu.
    // Harus mengembalikan array dengan key: 'success' (bool), 'providerId' (string|null), 'error' (string|null).
    public function send(string $phoneNumber, string $message): array;
}
