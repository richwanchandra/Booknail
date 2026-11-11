<?php

// ==========================================================================
// File: SendFcmJob.php
// Fungsi: Job antrian (queue) untuk mengirim push notification FCM secara
//         asinkron. Mendukung pengiriman ke user tertentu atau ke semua admin.
//         Memiliki mekanisme retry otomatis hingga 3 kali percobaan.
// ==========================================================================

namespace App\Jobs;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFcmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Jumlah maksimal percobaan pengiriman sebelum job dianggap gagal
    public int $tries = 3;

    // Jeda waktu (detik) antar percobaan ulang
    public int $backoff = 10;

    /**
     * @param  int|null  $userId     Target user ID (null = send to admins)
     * @param  bool      $toAdmins   If true, send to all admin FCM tokens
     * @param  string    $title
     * @param  string    $body
     * @param  array     $data       Optional extra payload
     */
    // Konstruktor menerima parameter untuk menentukan tujuan dan isi notifikasi.
    // Jika toAdmins=true, userId diabaikan dan notifikasi dikirim ke semua admin.
    public function __construct(
        public readonly ?int $userId,
        public readonly bool $toAdmins,
        public readonly string $title,
        public readonly string $body,
        public readonly array $data = [],
    ) {}

    // Eksekusi job: kirim push notification melalui FcmService.
    // Dipanggil otomatis oleh queue worker saat job diproses.
    public function handle(FcmService $fcm): void
    {
        // Jika flag toAdmins aktif, kirim ke semua admin lalu selesai
        if ($this->toAdmins) {
            $fcm->sendToAdmins($this->title, $this->body, $this->data);
            return;
        }

        // Jika userId tersedia, kirim notifikasi ke user tertentu
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                $fcm->sendToUser($user, $this->title, $this->body, $this->data);
            }
        }
    }
}
