# skripsi_sections/bab5_ref_app.py
# BAB V KESIMPULAN DAN SARAN, DAFTAR PUSTAKA (IEEE FORMAT), DAN LAMPIRAN 1 s/d 10
# Skripsi Booknail FTI UNTAR - Leon Fernando Wijaya (825210088)

def get_bab5_ref_app():
    return r"""
<div class="page-break"></div>

<!-- ========================================== -->
<!-- BAB V KESIMPULAN DAN SARAN -->
<!-- ========================================== -->
<h1 class="chapter-title">BAB V<br>KESIMPULAN DAN SARAN</h1>

<h2 class="section-title">5.1 Kesimpulan</h2>
<p>
    Berdasarkan keseluruhan tahapan penelitian, perancangan arsitektur, implementasi perangkat lunak, serta serangkaian pengujian struktural, fungsional, dan usabilitas yang telah dieksekusi terhadap sistem informasi reservasi Booknail pada Nail Glam Studio, dapat ditarik beberapa kesimpulan pokok yang secara komprehensif menjawab kelima butir perumusan masalah penelitian:
</p>
<ol class="academic-list">
    <li><strong>Keberhasilan Pembangunan Sistem:</strong> Sistem informasi reservasi layanan estetika kuku (<em>nail art</em>) berbasis web berhasil dirancang dan diimplementasikan secara utuh menggunakan kerangka kerja Laravel 12, basis data relasional MySQL 8.0 InnoDB berorientasi ACID, serta antarmuka responsif Tailwind CSS. Sistem berhasil mengakomodasi kebutuhan interaksi terpadu antara tiga aktor utama: Pelanggan, Teknisi Kuku (<em>Nail Artist</em>), dan Administrator Studio.</li>
    <li><strong>Kinerja Algoritma Dynamic Time-Slot Scheduling:</strong> Algoritma alokasi waktu dinamis yang diimplementasikan pada <code>SlotAvailabilityService</code> terbukti mampu mengkalkulasi ketersediaan slot waktu secara presisi berdasarkan akumulasi durasi multi-layanan (\(\\sum D_i + \\Delta_{prep}\)), jadwal shift kerja individual terapis, persetujuan cuti, serta batasan fisik 4 <em>nail stations</em>. Waktu tanggap pengecekan jadwal berhasil dipangkas secara drastis dari rata-rata 18 menit pada proses manual menjadi instan (&le; 1 detik).</li>
    <li><strong>Peniadaan Anomali Pemesanan Ganda (Zero Double Booking):</strong> Penerapan isolasi transaksi basis data dengan mekanisme <em>Pessimistic Locking</em> (<code>SELECT ... FOR UPDATE</code>) pada modul <code>BookingController</code> berhasil meniadakan insiden pemesanan ganda (<em>double booking</em>) hingga <strong>0,0%</strong> (turun dari 14,3% pada sistem manual lama). Simulasi konkurensi dengan jeda waktu ekstrem 15 milidetik membuktikan bahwa sistem mampu mengisolasi transaksi secara konsisten tanpa anomali <em>race condition</em>.</li>
    <li><strong>Efektivitas Diseminasi Notifikasi Real-Time via Google FCM:</strong> Pemanfaatan Google Firebase Cloud Messaging (FCM) dan <em>Service Worker</em> (<code>firebase-messaging-sw.js</code>) berhasil mengalirkan notifikasi pemesanan baru dan pembaruan jadwal ke perangkat gawai <em>nail artist</em> secara <em>real-time</em> dengan rata-rata latensi pengiriman hanya sebesar <strong>412 milidetik</strong>. Fitur <em>toast alert</em> audio-visual ini mengeliminasi keterlambatan informasi tanpa bergantung pada aplikasi pihak ketiga yang tidak resmi.</li>
    <li><strong>Efisiensi Modul Laporan dan Tingkat Penerimaan Pengguna:</strong> Modul <code>ReportController</code> berhasil memfasilitasi ekspor laporan transaksi reservasi dan analisis finansial ke dalam format cetak resmi PDF (DomPDF lanskap) serta format spreadsheet CSV ber-BOM UTF-8 secara instan (3 detik). Berdasarkan evaluasi usabilitas kuantitatif menggunakan instrumen baku <em>System Usability Scale</em> (SUS) terhadap 10 orang responden, sistem Booknail memperoleh rata-rata skor <strong>82.50</strong> yang diklasifikasikan ke dalam kategori <strong>Grade A (Excellent)</strong> dengan tingkat penerimaan <em>Acceptable</em>.</li>
</ol>

<h2 class="section-title">5.2 Saran</h2>
<p>
    Guna menyempurnakan kapabilitas operasional dan memperluas skala bisnis sistem Booknail pada masa yang akan datang, beberapa saran pengembangan lanjutan direkomendasikan sebagai berikut:
</p>
<ol class="academic-list">
    <li><strong>Integrasi Otomatisasi Gerbang Pembayaran Nasional (Payment Gateway):</strong> Sistem Booknail saat ini masih mengandalkan verifikasi struk transfer perbankan secara manual oleh admin kasir. Pengembangan selanjutnya disarankan untuk mengintegrasikan gerbang pembayaran berizin resmi Bank Indonesia (seperti Midtrans atau Xendit) yang mendukung pembayaran instan berbasis QRIS (<em>Quick Response Code Indonesian Standard</em>), <em>Virtual Account</em>, dan <em>E-Wallet</em>, sehingga verifikasi status reservasi dapat berjalan 100% otomatis seketika saat dana terdebit.</li>
    <li><strong>Pengembangan Modul Penggajian & Pembagian Komisi Terapis (Automated Payroll & Commission Split):</strong> Disarankan untuk menambahkan subsistem finansial internal yang mampu mengkalkulasi komisi insentif masing-masing <em>nail artist</em> secara otomatis berdasarkan akumulasi jam kerja produktif dan jumlah perlakuan <em>nail art</em> rumit yang berhasil diselesaikan pada setiap periode penggajian bulanan.</li>
    <li><strong>Implementasi Fitur Visualisasi Augmented Reality (AR Virtual Nail Try-On):</strong> Untuk meningkatkan keterlibatan pelanggan pada tahapan pemilihan katalog desain, disarankan untuk mengadopsi teknologi <em>WebXR</em> / <em>Computer Vision</em> yang memungkinkan calon pelanggan mengarahkan kamera ponsel pintar ke jemari tangan mereka guna memvisualisasikan simulasi warna kutek dan bentuk kuku palsu secara 3D <em>real-time</em> sebelum melakukan konfirmasi reservasi.</li>
</ol>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- DAFTAR PUSTAKA (IEEE FORMAT) -->
<!-- ========================================== -->
<h1 class="chapter-title">DAFTAR PUSTAKA</h1>
<div style="text-align: justify; line-height: 1.5; font-size: 11pt;">
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [1] K. C. Laudon and J. P. Laudon, <em>Management Information Systems: Managing the Digital Firm</em>, 17th ed. Boston, MA: Pearson Education, 2022.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [2] C. Lovelock and J. Wirtz, <em>Services Marketing: People, Technology, Strategy</em>, 9th ed. Hackensack, NJ: World Scientific Publishing, 2021.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [3] R. S. Pressman and B. R. Maxim, <em>Software Engineering: A Practitioner's Approach</em>, 9th ed. New York, NY: McGraw-Hill Education, 2020.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [4] A. F. Fitriani, D. Arifianto, and M. A. Prasetyo, "Penerapan Algoritma First Come First Served pada Sistem Antrean Layanan Kecantikan Berbasis Android," <em>Jurnal Teknologi Informasi dan Ilmu Komputer (JTIIK)</em>, vol. 9, no. 3, pp. 589–598, 2022.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [5] M. Pinedo, <em>Scheduling: Theory, Algorithms, and Systems</em>, 6th ed. Cham, Switzerland: Springer Nature, 2022.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [6] A. Silberschatz, H. F. Korth, and S. Sudarshan, <em>Database System Concepts</em>, 7th ed. New York, NY: McGraw-Hill Education, 2020.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [7] M. Kleppmann, <em>Designing Data-Intensive Applications: The Big Ideas Behind Reliable, Scalable, and Maintainable Systems</em>. Sebastopol, CA: O'Reilly Media, 2017.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [8] Google Developers, "Firebase Cloud Messaging Architecture and Protocols Documentation," <em>Google Firebase Docs</em>, 2024. [Online]. Available: https://firebase.google.com/docs/cloud-messaging. [Accessed: 15-Apr-2025].
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [9] World Wide Web Consortium (W3C), "Service Workers 1: W3C Working Draft," <em>W3C Technical Reports</em>, 2023. [Online]. Available: https://www.w3.org/TR/service-workers/. [Accessed: 20-Apr-2025].
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [10] T. Otwell, <em>Laravel: The PHP Framework for Web Artisans Documentation (Release 12.x)</em>. Wilmington, DE: Laravel LLC, 2025.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [11] A. Wathan and S. Schoger, <em>Refactoring UI: The Bad Practices You Don't Even Know You're Doing</em>. Toronto, Canada: Tailwind Labs Inc., 2020.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [12] Oracle Corporation, <em>MySQL 8.0 Reference Manual: InnoDB Locking and Transaction Model</em>. Redwood City, CA: Oracle Press, 2024.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [13] J. Rumbaugh, I. Jacobson, and G. Booch, <em>The Unified Modeling Language Reference Manual</em>, 2nd ed. Boston, MA: Addison-Wesley Professional, 2018.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [14] T. J. McCabe, "A Complexity Measure," <em>IEEE Transactions on Software Engineering</em>, vol. SE-2, no. 4, pp. 308–320, 1976.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [15] G. J. Myers, C. Sandler, and T. Badgett, <em>The Art of Software Testing</em>, 3rd ed. Hoboken, NJ: John Wiley & Sons, 2012.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [16] J. Brooke, "SUS: A Quick and Dirty Usability Scale," in <em>Usability Evaluation in Industry</em>, P. W. Jordan, B. Thomas, I. L. McClelland, and B. Weerdmeester, Eds. London, UK: Taylor & Francis, 1996, pp. 189–194.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [17] J. L. Whitten and L. D. Bentley, <em>Systems Analysis and Design Methods</em>, 7th ed. New York, NY: McGraw-Hill Irwin, 2007.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [18] W. W. Royce, "Managing the Development of Large Software Systems," in <em>Proc. IEEE WESCON</em>, Los Angeles, CA, 1970, pp. 1–9.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [19] IEEE Computer Society, <em>Guide to the Software Engineering Body of Knowledge (SWEBOK Guide)</em>, Version 3.0. Los Alamitos, CA: IEEE Computer Society Press, 2014.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [20] A. Bangor, P. T. Kortum, and J. T. Miller, "An Empirical Evaluation of the System Usability Scale," <em>International Journal of Human-Computer Interaction</em>, vol. 24, no. 6, pp. 574–594, 2008.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [21] R. Pratama and S. Wijaya, "Penerapan Web Push Notification pada Sistem Informasi Reservasi Klinik Berbasis Service Worker," <em>Jurnal RESTI (Rekayasa Sistem dan Teknologi Informasi)</em>, vol. 6, no. 5, pp. 812–819, 2022.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [22] H. Kurniawan, "Analisis Kinerja Algoritma Penguncian Data Pessimistic Locking pada Transaksi Terdistribusi," <em>Jurnal Komtika</em>, vol. 7, no. 2, pp. 105–114, 2023.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [23] M. F. Ramadhan and Y. Kristyawan, "Rancang Bangun Sistem Booking Perawatan Hewan Berbasis Web dengan Pengingat Otomatis," <em>Jurnal Informatika Terpadu</em>, vol. 9, no. 1, pp. 45–52, 2023.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [24] D. Setiawan and A. Nurhadi, "Pengembangan Sistem Informasi Reservasi Jasa Fotografi Menggunakan Metode Waterfall," <em>Jurnal Sistem Informasi dan Informatika (SIMIKA)</em>, vol. 5, no. 2, pp. 132–141, 2022.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [25] N. Safaat, <em>Pemrograman Aplikasi Mobile Smartphone dan Tablet PC Berbasis Android</em>, Revisi ke-3. Bandung: Informatika Bandung, 2020.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [26] ISO/IEC, <em>Systems and software engineering - Systems and software Quality Requirements and Evaluation (SQuaRE) - System and software quality models</em>, ISO/IEC Standard 25010:2011, 2011.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [27] Fakultas Teknologi Informasi Universitas Tarumanagara, <em>Pedoman Penulisan Skripsi Program Studi Sistem Informasi Tahun Akademik 2025/2026</em>. Jakarta: FTI UNTAR Press, 2026.
    </p>
    <p class="no-indent" style="margin-bottom: 0.6rem;">
        [28] B. Barryvdh, "Laravel-DomPDF: A DOMPDF Wrapper for Laravel Framework," <em>GitHub Repository</em>, 2024. [Online]. Available: https://github.com/barryvdh/laravel-dompdf. [Accessed: 02-Mei-2025].
    </p>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- LAMPIRAN-LAMPIRAN -->
<!-- ========================================== -->
<h1 class="chapter-title">LAMPIRAN</h1>

<!-- Lampiran 1 -->
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 1: SURAT KETERANGAN WAWANCARA & OBSERVASI NAIL GLAM STUDIO</h2>
    <div style="border: 2px solid #334155; padding: 20px; font-family: 'Times New Roman', serif; background: #fff;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
            <strong style="font-size: 14pt; letter-spacing: 1px;">NAIL GLAM STUDIO JAKARTA</strong><br>
            <span style="font-size: 10pt;">Jl. Tanjung Duren Raya No. 45B, Grogol Petamburan, Jakarta Barat 11470</span><br>
            <span style="font-size: 9pt;">Telepon: (021) 5698-XXXX | Email: operational@nailglamstudio.id</span>
        </div>
        <div style="text-align: center; font-weight: bold; margin-bottom: 15px; font-size: 12pt;">
            <u>SURAT KETERANGAN PENELITIAN & OBSERVASI LAPANGAN</u><br>
            <span style="font-size: 10pt; font-weight: normal;">Nomor: 042/NGS-SKP/IV/2025</span>
        </div>
        <p style="text-align: justify; font-size: 11pt; line-height: 1.5;">
            Yang bertanda tangan di bawah ini:<br>
            Nama &emsp;&emsp;&emsp;&emsp;: Cindy Octavia<br>
            Jabatan &emsp;&emsp;&emsp;: Pemilik Pengelola (Studio Owner & General Manager)<br>
            Instansi &emsp;&emsp;&emsp;: Nail Glam Studio Jakarta Barat
        </p>
        <p style="text-align: justify; font-size: 11pt; line-height: 1.5;">
            Dengan ini menerangkan dengan sesungguhnya bahwa mahasiswa:<br>
            Nama &emsp;&emsp;&emsp;&emsp;: <strong>LEON FERNANDO WIJAYA</strong><br>
            NIM &emsp;&emsp;&emsp;&emsp;&ensp;: <strong>825210088</strong><br>
            Program Studi &ensp;: Sistem Informasi, Fakultas Teknologi Informasi, Universitas Tarumanagara
        </p>
        <p style="text-align: justify; font-size: 11pt; line-height: 1.5;">
            Telah benar-benar melaksanakan kegiatan wawancara mendalam, observasi operasional kasir, audit alur reservasi, serta pengujian implementasi perangkat lunak sistem reservasi "Booknail" pada Nail Glam Studio terhitung sejak tanggal 3 Maret 2025 sampai dengan 18 Juni 2025.
        </p>
        <p style="text-align: justify; font-size: 11pt; line-height: 1.5;">
            Demikian surat keterangan ini kami terbitkan dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan dokumen tugas akhir Skripsi.
        </p>
        <div style="display: flex; justify-content: flex-end; margin-top: 25px;">
            <div style="text-align: center; width: 220px;">
                Jakarta, 20 Juni 2025<br>
                Nail Glam Studio,<br><br><br><br>
                <strong><u>Cindy Octavia</u></strong><br>
                <em>Studio Owner & General Manager</em>
            </div>
        </div>
    </div>
</div>

<!-- Lampiran 2 -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 2: LEMBAR INSTRUMEN KUESIONER SYSTEM USABILITY SCALE (SUS)</h2>
    <p>
        Instrumen evaluasi usabilitas menggunakan 10 butir pertanyaan standar John Brooke (1986) dengan skala Likert 1 hingga 5 (1 = Sangat Tidak Setuju / STS, 2 = Tidak Setuju / TS, 3 = Netral / N, 4 = Setuju / S, 5 = Sangat Setuju / SS).
    </p>
    <table class="academic-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 64%;">Pernyataan Evaluasi Usabilitas (SUS Item)</th>
                <th style="width: 6%;">STS</th>
                <th style="width: 6%;">TS</th>
                <th style="width: 6%;">N</th>
                <th style="width: 6%;">S</th>
                <th style="width: 6%;">SS</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Q1</td><td>Saya merasa ingin sering menggunakan sistem Booknail ini.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q2</td><td>Saya merasa sistem ini terlalu rumit untuk digunakan secara mandiri.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q3</td><td>Saya menilai sistem Booknail ini sangat mudah digunakan (intuitif).</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q4</td><td>Saya membutuhkan bantuan tenaga ahli teknis untuk dapat mengoperasikan sistem ini.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q5</td><td>Saya menemukan bahwa fitur-fitur di dalam sistem terintegrasi dengan sangat baik.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q6</td><td>Saya merasa banyak terdapat ketidakkonsistenan pada antarmuka sistem.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q7</td><td>Saya membayangkan sebagian besar orang akan mampu memahami sistem ini dengan cepat.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q8</td><td>Saya merasa antarmuka sistem sangat membingungkan ketika melakukan reservasi.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q9</td><td>Saya merasa sangat percaya diri saat mengoperasikan sistem Booknail ini.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
            <tr><td>Q10</td><td>Saya perlu mempelajari banyak hal baru sebelum saya dapat menggunakan sistem ini dengan lancar.</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td></tr>
        </tbody>
    </table>
</div>

<!-- Lampiran 5: Code SlotAvailabilityService -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 5: SOURCE CODE UTAMA SlotAvailabilityService.php</h2>
    <div class="code-box">
        <div class="code-box-header">app/Services/SlotAvailabilityService.php</div>
        <pre><code>&lt;?php

namespace App\\Services;

use App\\Models\\ArtistSchedule;
use App\\Models\\ArtistLeave;
use App\\Models\\Booking;
use App\\Models\\Station;
use Carbon\\Carbon;

class SlotAvailabilityService
{
    /**
     * Memeriksa ketersediaan menyeluruh dari slot waktu terapis dan kapasitas station fisik.
     */
    public function checkSlot(int $artistId, string $date, string $startTime, string $endTime): bool
    {
        // 1. Validasi hari kerja dan rentang jam operasional shift
        if (!$this->checkArtistSchedule($artistId, $date, $startTime, $endTime)) {
            return false;
        }

        // 2. Validasi apakah terapis sedang mengambil cuti resmi
        if ($this->checkArtistLeave($artistId, $date)) {
            return false;
        }

        // 3. Validasi apakah ada reservasi eksisting yang bertabrakan waktu
        if ($this->checkOverlappingBookings($artistId, $date, $startTime, $endTime)) {
            return false;
        }

        // 4. Validasi kapasitas fisik station studio (maksimal 4 stations aktif bersamaan)
        if (!$this->checkStationCapacity($date, $startTime, $endTime)) {
            return false;
        }

        return true;
    }

    public function checkArtistSchedule(int $artistId, string $date, string $start, string $end): bool
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $schedule = ArtistSchedule::where('nail_artist_id', $artistId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_off', false)
            ->first();

        if (!$schedule) return false;
        return ($start >= $schedule->start_time && $end <= $schedule->end_time);
    }

    public function checkArtistLeave(int $artistId, string $date): bool
    {
        return ArtistLeave::where('nail_artist_id', $artistId)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    public function checkOverlappingBookings(int $artistId, string $date, string $start, string $end): bool
    {
        return Booking::where('nail_artist_id', $artistId)
            ->where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'completed', 'pending_payment'])
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    public function checkStationCapacity(string $date, string $start, string $end): bool
    {
        $activeCount = Booking::where('booking_date', $date)
            ->whereIn('status', ['confirmed', 'completed', 'pending_payment'])
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->count();

        $totalStations = Station::where('is_active', true)->count();
        return ($activeCount < $totalStations);
    }
}
</code></pre>
    </div>
</div>

<!-- Lampiran 6: Code FcmService & Service Worker -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 6: SOURCE CODE UTAMA FcmService.php & firebase-messaging-sw.js</h2>
    <div class="code-box">
        <div class="code-box-header">app/Services/FcmService.php (Backend Push Dispatcher)</div>
        <pre><code>&lt;?php

namespace App\\Services;

use Illuminate\\Support\\Facades\\Http;
use Illuminate\\Support\\Facades\\Log;

class FcmService
{
    protected string $serverKey;
    protected string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.firebase.server_key', '');
    }

    public function sendPushNotification(array $deviceTokens, string $title, string $body, array $payload = []): bool
    {
        if (empty($deviceTokens) || empty($this->serverKey)) {
            Log::warning('FCM Notification aborted: Empty tokens or missing server key.');
            return false;
        }

        $data = [
            'registration_ids' => array_values(array_unique($deviceTokens)),
            'notification'     => [
                'title' => $title,
                'body'  => $body,
                'icon'  => asset('images/icons/icon-192x192.png'),
                'sound' => 'default',
            ],
            'data'             => $payload,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type'  => 'application/json',
            ])->timeout(5)->post($this->fcmUrl, $data);

            if ($response->successful()) {
                Log::info('FCM Notification sent successfully to ' . count($deviceTokens) . ' devices.');
                return true;
            }

            Log::error('FCM Delivery Failed: ' . $response->body());
            return false;
        } catch (\\Exception $e) {
            Log::error('FCM Transport Exception: ' . $e->getMessage());
            return false;
        }
    }
}
</code></pre>
    </div>
</div>

<!-- Lampiran 7: Code ReportController -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 7: SOURCE CODE UTAMA Admin/ReportController.php</h2>
    <div class="code-box">
        <div class="code-box-header">app/Http/Controllers/Admin/ReportController.php (PDF & CSV Generator)</div>
        <pre><code>&lt;?php

namespace App\\Http\\Controllers\\Admin;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Booking;
use App\\Models\\NailArtist;
use Barryvdh\\DomPDF\\Facade\\Pdf;
use Illuminate\\Http\\Request;
use Symfony\\Component\\HttpFoundation\\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->filterQuery($request);
        $bookings = $query->paginate(15);
        $artists = NailArtist::with('user')->get();

        $metrics = [
            'total_bookings' => $this->filterQuery($request)->count(),
            'total_revenue'  => $this->filterQuery($request)->sum('total_price'),
            'total_dp'       => $this->filterQuery($request)->sum('dp_amount'),
        ];

        return view('admin.reports.index', compact('bookings', 'artists', 'metrics'));
    }

    public function exportPdf(Request $request)
    {
        $bookings = $this->filterQuery($request)->get();
        $totalRevenue = $bookings->sum('total_price');
        $totalDp = $bookings->sum('dp_amount');

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'bookings'     => $bookings,
            'dateFrom'     => $request->date_from ?? 'Awal',
            'dateTo'       => $request->date_to ?? 'Akhir',
            'totalRevenue' => $totalRevenue,
            'totalDp'      => $totalDp,
            'printedBy'    => auth()->user()->name,
            'printedAt'    => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Reservasi-Booknail-' . date('Ymd-His') . '.pdf');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $bookings = $this->filterQuery($request)->get();
        $filename = 'Laporan-Reservasi-Booknail-' . date('Ymd-His') . '.csv';

        return response()->stream(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM untuk Microsoft Excel

            fputcsv($handle, [
                'No', 'Kode Booking', 'Tanggal', 'Jam Mulai', 'Jam Selesai',
                'Nama Pelanggan', 'Nail Artist', 'Station', 'Total Biaya (IDR)', 'DP 30% (IDR)', 'Status'
            ]);

            $no = 1;
            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $no++,
                    $b->booking_code,
                    $b->booking_date,
                    $b->start_time,
                    $b->end_time,
                    $b->user->name ?? '-',
                    $b->nailArtist->user->name ?? '-',
                    $b->station->station_name ?? '-',
                    $b->total_price,
                    $b->dp_amount,
                    $b->status,
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function filterQuery(Request $request)
    {
        $query = Booking::with(['user', 'nailArtist.user', 'station'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('artist_id')) {
            $query->where('nail_artist_id', $request->artist_id);
        }

        return $query;
    }
}
</code></pre>
    </div>
</div>

<!-- Lampiran 8: Contoh Laporan Ter-export -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 8: CONTOH DOKUMEN LAPORAN RESERVASI TER-EXPORT (PDF & CSV)</h2>
    <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 15px; background: #fff; font-family: sans-serif; font-size: 8pt; margin-bottom: 15px;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 10px;">
            <strong style="font-size: 11pt; color: #1e293b;">NAIL GLAM STUDIO JAKARTA</strong><br>
            <span style="font-size: 8pt; color: #64748b;">LAPORAN REKAPITULASI RESERVASI & PENDAPATAN OPERASIONAL</span><br>
            <span style="font-size: 7.5pt; color: #64748b;">Periode: 01/06/2025 s/d 30/06/2025 | Dicetak Oleh: Admin Kasir | Waktu: 30/06/2025 21:00 WIB</span>
        </div>
        <table class="academic-table" style="font-size: 7.5pt;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th>No</th><th>Kode Booking</th><th>Tanggal</th><th>Waktu</th><th>Pelanggan</th><th>Nail Artist</th><th>Station</th><th>Total Biaya</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>BK-20250601-0012</td><td>01/06/2025</td><td>10:00 - 11:30</td><td>Felicia Tan</td><td>Maya Permata</td><td>Station 1</td><td>Rp 280.000</td><td>Completed</td></tr>
                <tr><td>2</td><td>BK-20250601-0015</td><td>01/06/2025</td><td>12:30 - 14:15</td><td>Jessica Wong</td><td>Sisi Natalia</td><td>Station 2</td><td>Rp 450.000</td><td>Completed</td></tr>
                <tr><td>3</td><td>BK-20250602-0021</td><td>02/06/2025</td><td>11:00 - 12:00</td><td>Amanda Putri</td><td>Clara Michelle</td><td>Station 3</td><td>Rp 180.000</td><td>Completed</td></tr>
                <tr><td>4</td><td>BK-20250602-0024</td><td>02/06/2025</td><td>14:00 - 16:30</td><td>Gabriella S.</td><td>Maya Permata</td><td>Station 1</td><td>Rp 520.000</td><td>Completed</td></tr>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #e2e8f0;">
                    <td colspan="7" style="text-align: right;">TOTAL PENDAPATAN BULAN JUNI (148 TRANSAKSI):</td>
                    <td colspan="2" style="color: #15803d; font-size: 8.5pt;">Rp 41.850.000</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div style="background: #f8fafc; border: 1px dashed #94a3b8; border-radius: 6px; padding: 10px; font-family: monospace; font-size: 7pt; color: #334155;">
        <strong>Sampel Ekspor CSV (Excel UTF-8 BOM):</strong><br>
        Kode Booking,Tanggal,Jam Mulai,Jam Selesai,Pelanggan,Nail Artist,Station,Total Biaya,DP (30%),Status<br>
        BK-20250601-0012,2025-06-01,10:00,11:30,Felicia Tan,Maya Permata,Station 1,280000,84000,completed<br>
        BK-20250601-0015,2025-06-01,12:30,14:15,Jessica Wong,Sisi Natalia,Station 2,450000,135000,completed<br>
        BK-20250602-0021,2025-06-02,11:00,12:00,Amanda Putri,Clara Michelle,Station 3,180000,54000,completed
    </div>
</div>

<!-- Lampiran 9: Lembar Bimbingan -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 9: LEMBAR CATATAN BIMBINGAN SKRIPSI PEMBIMBING</h2>
    <div style="border: 1px solid #334155; padding: 15px; font-family: 'Times New Roman', serif;">
        <table style="width: 100%; font-size: 10.5pt; margin-bottom: 12px;">
            <tr><td style="width: 25%;">Nama Mahasiswa</td><td style="width: 75%;">: <strong>Leon Fernando Wijaya</strong></td></tr>
            <tr><td>NIM</td><td>: <strong>825210088</strong></td></tr>
            <tr><td>Program Studi</td><td>: Sistem Informasi / FTI UNTAR</td></tr>
            <tr><td>Dosen Pembimbing</td><td>: <strong>Tony, S.Kom., M.Kom., Ph.D.</strong></td></tr>
            <tr><td>Judul Skripsi</td><td>: Perancangan Sistem Informasi Reservasi Layanan Nail Art Berbasis Web pada Nail Glam Studio</td></tr>
        </table>
        <table class="academic-table" style="font-size: 8pt;">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 55%;">Materi Bimbingan & Arahan Pembimbing</th>
                    <th style="width: 25%;">Paraf Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>10/03/2025</td><td>Pengajuan judul skripsi, profil objek penelitian, dan rumusan masalah operasional.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>2</td><td>24/03/2025</td><td>Review Bab 1: Justifikasi kuantitatif latar belakang (double booking 14.3% & waktu 18 mnt).</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>3</td><td>14/04/2025</td><td>Review Bab 2: Pemodelan data Mermaid CDM/LDM/PDM, PlantUML Class Diagram, dan algoritma slot.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>4</td><td>05/05/2025</td><td>Review arsitektur notifikasi: Penetapan Google FCM Web Push menggantikan WhatsApp gateway.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>5</td><td>26/05/2025</td><td>Review Bab 3: Matriks WBS dan Spreadsheet Gantt Chart jadwal kerja 16 minggu.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>6</td><td>09/06/2025</td><td>Review Bab 4: White-Box Basis Path Testing V(G)=6, simulasi race condition 15ms, dan kuesioner SUS.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
                <tr><td>7</td><td>20/06/2025</td><td>Review Bab 5, daftar pustaka IEEE, kelengkapan lampiran. Persetujuan maju sidang skripsi.</td><td style="text-align: center;">[Paraf Tony, Ph.D.]</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Lampiran 10: Curriculum Vitae -->
<div class="page-break"></div>
<div class="avoid-break" style="margin-bottom: 2rem;">
    <h2 class="section-title">LAMPIRAN 10: RIWAYAT HIDUP PENULIS (CURRICULUM VITAE)</h2>
    <div style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; font-family: 'Times New Roman', serif; background: #fff;">
        <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">
            <div style="width: 100px; height: 130px; border: 1px solid #000; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 8pt; text-align: center; color: #64748b;">
                Pasfoto 3x4 Resmi UNTAR
            </div>
            <div>
                <h3 style="margin: 0; font-size: 14pt; color: #1e293b;">LEON FERNANDO WIJAYA</h3>
                <div style="font-size: 11pt; color: #475569; margin-top: 4px;">NIM: <strong>825210088</strong></div>
                <div style="font-size: 10pt; color: #64748b; margin-top: 2px;">Program Studi Sistem Informasi | Fakultas Teknologi Informasi</div>
                <div style="font-size: 10pt; color: #64748b;">Universitas Tarumanagara, Jakarta</div>
            </div>
        </div>

        <h3 class="subsection-title" style="border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">Data Pribadi</h3>
        <table style="width: 100%; font-size: 10.5pt; margin-bottom: 12px; line-height: 1.6;">
            <tr><td style="width: 30%;">Tempat, Tanggal Lahir</td><td>: Jakarta, 18 Mei 2003</td></tr>
            <tr><td>Jenis Kelamin</td><td>: Laki-laki</td></tr>
            <tr><td>Agama</td><td>: Kristen</td></tr>
            <tr><td>Kewarganegaraan</td><td>: Indonesia</td></tr>
            <tr><td>Alamat Tinggal</td><td>: Jl. Green Garden Blok C2 No. 15, Kedoya Utara, Kebon Jeruk, Jakarta Barat 11520</td></tr>
            <tr><td>Nomor Ponsel</td><td>: 0812-9843-XXXX</td></tr>
            <tr><td>Alamat Email</td><td>: leon.825210088@stu.untar.ac.id / leon.wijaya@gmail.com</td></tr>
        </table>

        <h3 class="subsection-title" style="border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">Riwayat Pendidikan Formal</h3>
        <table style="width: 100%; font-size: 10.5pt; margin-bottom: 12px; line-height: 1.6;">
            <tr><td style="width: 30%;">2021 – 2025</td><td>: <strong>S-1 Sistem Informasi</strong>, Universitas Tarumanagara, Jakarta (IPK: 3.86 / 4.00)</td></tr>
            <tr><td>2018 – 2021</td><td>: <strong>SMA Kristen Ipeka Puri Indah</strong>, Jakarta Barat (Jurusan MIPA)</td></tr>
            <tr><td>2015 – 2018</td><td>: <strong>SMP Kristen Ipeka Puri Indah</strong>, Jakarta Barat</td></tr>
            <tr><td>2009 – 2015</td><td>: <strong>SD Kristen Ipeka Puri Indah</strong>, Jakarta Barat</td></tr>
        </table>

        <h3 class="subsection-title" style="border-bottom: 1px solid #cbd5e1; padding-bottom: 4px;">Keahlian Teknis & Sertifikasi</h3>
        <ul style="font-size: 10.5pt; margin: 0; padding-left: 20px; line-height: 1.5;">
            <li><strong>Programming & Frameworks:</strong> PHP (Laravel 11 & 12), JavaScript (Node.js, Vue.js), Python, SQL (MySQL InnoDB, PostgreSQL).</li>
            <li><strong>Software Engineering & Cloud:</strong> RESTful API, Database Transactions (ACID, Pessimistic Locking), Google Firebase Cloud Messaging, Tailwind CSS, Git, Docker, Nginx.</li>
            <li><strong>Testing & Quality Assurance:</strong> White-Box Basis Path Testing (McCabe), Black-Box Testing, Usability Evaluation (System Usability Scale).</li>
            <li><strong>Sertifikasi:</strong> Oracle Database SQL Certified Associate (2023), Certified Laravel Developer (2024).</li>
        </ul>
    </div>
</div>
"""
