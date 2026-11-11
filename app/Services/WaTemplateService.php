<?php

// ==========================================================================
// File: WaTemplateService.php
// Fungsi: Service untuk membangun pesan WhatsApp dari template berdasarkan
//         tipe notifikasi (booking dibuat, DP dikonfirmasi, pengingat, dll).
//         Template menggunakan placeholder {key} yang diganti dengan data aktual.
// ==========================================================================

namespace App\Services;

use App\Models\WaLog;

class WaTemplateService
{
    // Metode statis untuk membangun pesan WhatsApp berdasarkan tipe dan data.
    // Mengembalikan string pesan yang siap dikirim melalui WA gateway.
    public static function buildMessage(string $type, array $data): string
    {
        // Closure untuk mengganti placeholder {key} dalam template dengan nilai dari array data
        $replace = function ($tpl, $d) {
            foreach ($d as $k => $v) {
                $tpl = str_replace('{'.$k.'}', (string) $v, $tpl);
            }

            return $tpl;
        };

        // Pilih template berdasarkan tipe pesan menggunakan match expression
        $template = match ($type) {
            // Template: Booking baru berhasil dibuat, berisi detail dan instruksi pembayaran DP
            WaLog::TYPE_BOOKING_CREATED => <<<'TXT'
🌸 Halo {customer_name}! Terima kasih telah booking di Booknail Studio.

📝 Kode Booking: {booking_code}
👩‍🎨 Nail Artist: {artist_name}
📅 Tanggal: {date}
⏰ Waktu: {time}
💰 Total: {total}
💳 DP Minimum: {dp}

💡 Instruksi:
{payment_instructions}

Jika ada pertanyaan, balas pesan ini ya. Sampai bertemu di studio! ✨💅
TXT,
            // Template: DP sudah diverifikasi oleh admin, booking terkonfirmasi
            WaLog::TYPE_DP_CONFIRMED => <<<'TXT'
✅ Halo {customer_name}! DP Booking kamu SUDAH DIVERIFIKASI.

📝 Kode Booking: {booking_code}
👩‍🎨 Nail Artist: {artist_name}
📅 Tanggal: {date}
⏰ Waktu: {time}

Booking status: TERKONFIRMASI ✔
Datang tepat waktu ya! Sampai bertemu di studio Booknail. 💅💕
TXT,
            // Template: Pengingat H-1 sebelum jadwal booking
            WaLog::TYPE_REMINDER_H1 => <<<'TXT'
🌟 PENGINGAT H-1 Booking Booknail Studio

📝 Kode Booking: {booking_code}
👩‍🎨 Nail Artist: {artist_name}
📅 Besok, {date}
⏰ {time}

Persiapkan referensi desain jika ada. Sampai bertemu besok! 💖
TXT,
            // Template: Pengingat 2 jam sebelum jadwal booking
            WaLog::TYPE_REMINDER_2H => <<<'TXT'
⏰ PENGINGAT 2 JAM LAGI Booking di Booknail

📝 Kode: {booking_code}
👩‍🎨 Artist: {artist_name}
⏰ {time}

Studio lokasi: Jl. Nail Art No. 1. Silakan datang 10 menit sebelum ya! 💅
TXT,
            // Template: Booking dibatalkan (baik oleh admin maupun otomatis oleh sistem)
            WaLog::TYPE_BOOKING_CANCELLED => <<<'TXT'
⚠️ Booking {booking_code} DIBATALKAN.

Customer: {customer_name}
Tanggal: {date}
Alasan: {payment_instructions}

Silakan booking ulang kapan saja melalui website Booknail. Jika ada pertanyaan hubungi admin.
TXT,
            // Template: Booking selesai, terima kasih dan informasi loyalti poin
            WaLog::TYPE_BOOKING_COMPLETED => <<<'TXT'
🎉 Terima kasih {customer_name}!

Kamu baru saja selesai perawatan dengan booking {booking_code}.
Total belanja: {total}.
Kamu mendapatkan loyalty poin (1 per Rp10.000), cek di Dashboard akunmu.

💖 Bantu kami dengan memberikan review di Dashboard → Riwayat Booking → Beri Review.
Sampai jumpa perawatan berikutnya di Booknail! ✨
TXT,
            // Template: Pemberitahuan pergeseran jadwal akibat cascading shift
            WaLog::TYPE_SCHEDULE_DELAYED => <<<'TXT'
⚠️ PEMBERITAHUAN PENYESUAIAN JADWAL (Nail Glam Studio)

Halo {customer_name}! Kami menginformasikan terdapat penyesuaian waktu perawatan kamu bersama {artist_name} hari ini.

📝 Kode Booking: {booking_code}
📅 Tanggal: {date}
⏰ Jadwal Semula: {original_time} WIB
✨ Estimasi Jadwal Baru: {new_time} WIB (Mundur ~{delay_minutes} menit)

💡 Alasan:
{delay_reason}

Mohon konfirmasi kesediaan kamu dengan membalas:
1️⃣ OKE (Tetap hadir di jam baru)
2️⃣ RESCHEDULE (Ingin pindah ke jam / hari lain)

Mohon maaf sebesar-besarnya atas ketidaknyamanan ini. Kami ingin memastikan setiap pelanggan mendapatkan kualitas pengerjaan kuku terbaik dan teliti tanpa terburu-buru. Terima kasih atas pengertiannya! 💅🌸
TXT,
            // Default: template generik jika tipe tidak dikenali
            default => 'Booking {booking_code} untuk {customer_name}.',
        };

        // Gabungkan data default (nilai kosong) dengan data aktual, lalu ganti placeholder di template
        return $replace($template, array_merge([
            'customer_name' => '',
            'booking_code' => '',
            'artist_name' => '',
            'date' => '',
            'time' => '',
            'total' => '',
            'dp' => '',
            'payment_instructions' => '',
            'original_time' => '',
            'new_time' => '',
            'delay_minutes' => '',
            'delay_reason' => '',
        ], $data));
    }
}
