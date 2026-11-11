<?php

// ==========================================================================
// File: MockDriver.php
// Fungsi: Implementasi tiruan (mock) dari WaGatewayInterface untuk keperluan
//         development dan testing. Tidak mengirim pesan WA sungguhan, melainkan
//         hanya mencatat isi pesan ke log aplikasi.
// ==========================================================================

namespace App\Services\WaGateway;

use Illuminate\Support\Facades\Log;

class MockDriver implements WaGatewayInterface
{
    // Mensimulasikan pengiriman pesan WhatsApp tanpa benar-benar mengirim.
    // Pesan dicatat ke log dan selalu mengembalikan status sukses.
    public function send(string $phoneNumber, string $message): array
    {
        // Buat garis pemisah untuk memudahkan pembacaan log
        $line = str_repeat('-', 40);

        // Tulis isi pesan ke log untuk keperluan debugging
        Log::info("\n{$line}\n[WA_MOCK] TO: {$phoneNumber}\n[WA_MOCK] BODY:\n{$message}\n{$line}\n");

        // Kembalikan response sukses dengan provider ID acak bertanda MOCK_
        return [
            'success' => true,
            'providerId' => 'MOCK_'.bin2hex(random_bytes(6)),
            'error' => null,
        ];
    }
}
