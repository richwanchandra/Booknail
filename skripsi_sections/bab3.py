# skripsi_sections/bab3.py
# BAB III METODOLOGI PENELITIAN DAN TATA LAKSANA PENGEMBANGAN
# Skripsi Booknail FTI UNTAR - Leon Fernando Wijaya (825210088)

def get_bab3():
    return r"""
<div class="page-break"></div>

<!-- ========================================== -->
<!-- BAB III METODOLOGI PENELITIAN DAN TATA LAKSANA PENGEMBANGAN -->
<!-- ========================================== -->
<h1 class="chapter-title">BAB III<br>METODOLOGI PENELITIAN DAN TATA LAKSANA PENGEMBANGAN</h1>

<h2 class="section-title">3.1 Tata Laksana Metode Pengembangan Waterfall</h2>
<p>
    Pengembangan sistem informasi reservasi Booknail dilaksanakan dengan mengadopsi model sekuensial linier (<em>Waterfall Model</em>) yang diperkenalkan oleh Winston W. Royce [18]. Model Waterfall dipilih secara cermat karena karakteristik domain bisnis reservasi studio kecantikan pada Nail Glam Studio memiliki batasan masalah yang terdefinisi dengan jelas (<em>well-defined requirements</em>), kapasitas fisik yang pasti (4 <em>nail stations</em> dan 4 <em>nail artists</em>), serta kepatuhan mutlak terhadap aturan logika transaksi ACID untuk mencegah anomali pemesanan ganda. Melalui pendekatan sekuensial, setiap fase pengembangan harus diselesaikan dan diverifikasi secara tuntas sebelum melangkah ke tahapan berikutnya, meminimalkan ambiguitas spesifikasi teknis.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 8.5pt;">
        <div style="text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 15px; color: #1e293b;">
            TAHAPAN MODEL WATERFALL PENGEMBANGAN SISTEM BOOKNAIL
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px; max-width: 520px; margin: 0 auto;">
            <div style="background: #eff6ff; border: 1.5px solid #3b82f6; border-radius: 6px; padding: 8px 14px; margin-left: 0; box-shadow: 0 2px 4px rgba(59,130,246,0.15);">
                <strong style="color: #1e40af;">1. Analisis Kebutuhan (Requirements Analysis)</strong><br>
                <span style="font-size: 8pt; color: #475569;">Observasi operasional Nail Glam Studio, wawancara pengelola, analisis PIECES, penentuan 27 use cases.</span>
            </div>
            <div style="color: #3b82f6; font-weight: bold; margin-left: 50px;">&darr;</div>
            <div style="background: #f0fdf4; border: 1.5px solid #22c55e; border-radius: 6px; padding: 8px 14px; margin-left: 40px; box-shadow: 0 2px 4px rgba(34,197,94,0.15);">
                <strong style="color: #15803d;">2. Perancangan Sistem (System Design)</strong><br>
                <span style="font-size: 8pt; color: #475569;">Pemodelan UML (Activity & Sequence), perancangan data CDM/LDM/PDM Mermaid, PlantUML Class Diagram, wireframing UI.</span>
            </div>
            <div style="color: #22c55e; font-weight: bold; margin-left: 90px;">&darr;</div>
            <div style="background: #fff7ed; border: 1.5px solid #f97316; border-radius: 6px; padding: 8px 14px; margin-left: 80px; box-shadow: 0 2px 4px rgba(249,115,22,0.15);">
                <strong style="color: #c2410c;">3. Implementasi & Pengkodean (Implementation)</strong><br>
                <span style="font-size: 8pt; color: #475569;">Pembangunan backend Laravel 12, algoritma SlotAvailabilityService, pessimistic locking DB::transaction, Google FCM, modul export PDF/CSV.</span>
            </div>
            <div style="color: #f97316; font-weight: bold; margin-left: 130px;">&darr;</div>
            <div style="background: #fdf2f8; border: 1.5px solid #ec4899; border-radius: 6px; padding: 8px 14px; margin-left: 120px; box-shadow: 0 2px 4px rgba(236,72,153,0.15);">
                <strong style="color: #be185d;">4. Pengujian Sistem (Testing & Verification)</strong><br>
                <span style="font-size: 8pt; color: #475569;">White-Box Basis Path Testing (V(G)=6), Black-Box 27 use cases, simulasi race condition 15 ms, evaluasi usabilitas SUS 10 responden.</span>
            </div>
            <div style="color: #ec4899; font-weight: bold; margin-left: 170px;">&darr;</div>
            <div style="background: #faf5ff; border: 1.5px solid #a855f7; border-radius: 6px; padding: 8px 14px; margin-left: 160px; box-shadow: 0 2px 4px rgba(168,85,247,0.15);">
                <strong style="color: #7e22ce;">5. Penerapan & Pemeliharaan (Deployment & Maintenance)</strong><br>
                <span style="font-size: 8pt; color: #475569;">Deployment ke environment staging HTTPS, pelatihan staf admin dan terapis, prosedur pemeliharaan korektif dan preventif.</span>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 3.1 Tahapan Metodologi Waterfall yang Diterapkan dalam Pengembangan Booknail</div>
</div>

<p>
    Rincian aktivitas pada setiap fase metodologi Waterfall dijabarkan sebagai berikut:
</p>
<ol class="academic-list">
    <li><strong>Fase Analisis Kebutuhan:</strong> Melakukan wawancara langsung dengan Ibu Cindy Octavia (Pemilik Nail Glam Studio) dan staf kasir untuk memetakan alur transaksi manual, mencatat keluhan operasional, mengumpulkan data historis pembatalan pemesanan dan insiden <em>double booking</em>, serta merumuskan spesifikasi kebutuhan perangkat lunak (<em>Software Requirements Specification</em> / SRS).</li>
    <li><strong>Fase Perancangan Sistem:</strong> Merancang arsitektur sistem berbasis MVC, merumuskan algoritma alokasi waktu dinamis, menyusun diagram alir use case, activity, dan sequence, merancang skema basis data relasional (CDM, LDM, PDM dalam notasi Mermaid, serta Class Diagram dalam notasi PlantUML), serta mendesain prototipe antarmuka pengguna yang ergonomis.</li>
    <li><strong>Fase Implementasi dan Pengkodean:</strong> Menerjemahkan rancangan logis ke dalam baris kode program menggunakan bahasa pemrograman PHP 8.2 pada kerangka kerja Laravel 12, antarmuka responsif Tailwind CSS, serta integrasi pustaka eksternal seperti DomPDF dan Google Firebase Cloud Messaging (FCM) Web Push Service Worker.</li>
    <li><strong>Fase Pengujian Sistem:</strong> Mengeksekusi pengujian struktural kode (White-Box Basis Path Testing) terhadap unit logika alokasi slot untuk membuktikan ketercakupan logika 100%, pengujian fungsionalitas kotak hitam (Black-Box Testing) untuk 27 use cases, pengujian stres beban konkurensi data (<em>anti-race condition testing</em>), serta pengujian penerimaan pengguna menggunakan kuesioner <em>System Usability Scale</em> (SUS).</li>
    <li><strong>Fase Penerapan dan Pemeliharaan:</strong> Memasang sistem pada server hosting berprotokol aman HTTPS, melakukan registrasi token peramban terapis ke Google FCM, melatih operator admin dan terapis, serta menyusun prosedur pemeliharaan sistem berkala.</li>
</ol>

<!-- ========================================== -->
<!-- 3.2 KEBUTUHAN IMPLEMENTASI SISTEM -->
<!-- ========================================== -->
<h2 class="section-title">3.2 Kebutuhan Implementasi Sistem</h2>

<h3 class="subsection-title">3.2.1 Topologi Jaringan Client-Server & Arsitektur Google FCM</h3>
<p>
    Sistem Booknail dibangun di atas arsitektur jaringan <em>client-server</em> berbasis web yang aman dengan mengadopsi standar enkripsi Transport Layer Security (TLS 1.3) pada protokol HTTPS (port 443). Penggunaan HTTPS merupakan prasyarat mutlak yang diwajibkan oleh standar W3C agar peramban web mengizinkan eksekusi <em>Service Worker</em> dan pendaftaran API Web Push Google FCM. Topologi jaringan disajikan pada Gambar 3.2.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 8.5pt;">
        <div style="text-align: center; font-weight: bold; font-size: 9.5pt; margin-bottom: 12px; color: #1e293b;">
            TOPOLOGI JARINGAN SISTEM BOOKNAIL & GOOGLE FCM WEB PUSH
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; text-align: center;">
            <!-- Client Layer -->
            <div style="width: 28%; display: flex; flex-direction: column; gap: 8px;">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px;">
                    <strong>Pelanggan (Mobile/PC)</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Peramban Web Klien<br>(Chrome, Safari, Edge)</span>
                </div>
                <div style="background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 8px;">
                    <strong>Terapis (Tablet/Ponsel)</strong><br>
                    <span style="font-size: 7.5pt; color: #c2410c;">Service Worker Aktif<br>Terhubung ke FCM</span>
                </div>
                <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px;">
                    <strong>Admin Studio (PC)</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">Dashboard Kasir & Laporan PDF/CSV</span>
                </div>
            </div>

            <!-- Protocol Connector -->
            <div style="width: 12%; font-size: 8pt; color: #0284c7; font-weight: bold;">
                &larr; HTTPS &rarr;<br>(Port 443 TLS)
            </div>

            <!-- Server Layer -->
            <div style="width: 30%; background: #f8fafc; border: 1.5px solid #0284c7; border-radius: 6px; padding: 10px;">
                <strong style="color: #0369a1;">Web & Database Server</strong><br>
                <div style="font-size: 7.5pt; color: #334155; text-align: left; margin-top: 6px; line-height: 1.3;">
                    &bull; Nginx / Apache Web Server<br>
                    &bull; PHP 8.2 Engine (Laravel 12)<br>
                    &bull; MySQL 8.0 InnoDB (ACID)<br>
                    &bull; DomPDF Generation Engine<br>
                    &bull; Queue Worker (SendFcmJob)
                </div>
            </div>

            <!-- Cloud Connector -->
            <div style="width: 12%; font-size: 8pt; color: #ea580c; font-weight: bold;">
                &larr; JSON &rarr;<br>WebPush API
            </div>

            <!-- Cloud Layer -->
            <div style="width: 20%; background: #fffbeb; border: 1.5px solid #f59e0b; border-radius: 6px; padding: 8px;">
                <strong style="color: #b45309;">Google FCM Cloud</strong><br>
                <span style="font-size: 7.5pt; color: #78350f;">Push Gateway Server<br>Token Registry</span>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 3.2 Topologi Jaringan Terdistribusi Client-Server dan Arsitektur Google Firebase Cloud Messaging</div>
</div>

<h3 class="subsection-title">3.2.2 Kebutuhan Perangkat Keras (Hardware Requirements)</h3>
<p>
    Spesifikasi perangkat keras minimum yang dibutuhkan untuk menopang lingkungan pengembangan (<em>development</em>), peladen produksi (<em>production server</em>), dan perangkat pengguna akhir dirangkum pada Tabel 3.1.
</p>

<div class="table-container">
    <div class="table-title">Tabel 3.1 Spesifikasi Kebutuhan Perangkat Keras Sistem Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 22%;">Perangkat</th>
                <th style="width: 38%;">Komponen Spesifikasi Minimum</th>
                <th style="width: 40%;">Spesifikasi Rekomendasi Operasional</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Development Workstation</strong> (Laptop Peneliti)</td>
                <td>Processor Intel Core i5 Gen 8 / AMD Ryzen 5, RAM 8 GB DDR4, Penyimpanan SSD 256 GB, Layar 14 inci Full HD.</td>
                <td>Processor AMD Ryzen 7 5700U / Intel Core i7, RAM 16 GB DDR4, Penyimpanan NVMe SSD 512 GB, Koneksi Internet 50 Mbps.</td>
            </tr>
            <tr>
                <td><strong>Web & Database Server</strong> (Production Hosting)</td>
                <td>Virtual Private Server (VPS), 1 vCPU Cores, RAM 2 GB, Penyimpanan 20 GB SSD, Bandwidth 1 TB/bulan.</td>
                <td>Cloud VPS (DigitalOcean / AWS LightSail), 2 vCPU Cores, RAM 4 GB, Penyimpanan 50 GB NVMe SSD, OS Ubuntu 24.04 LTS.</td>
            </tr>
            <tr>
                <td><strong>Client Kasir / Admin Studio</strong></td>
                <td>Komputer Desktop / Laptop, Processor Intel Core i3, RAM 4 GB, Penyimpanan 128 GB SSD, Monitor 14 inci.</td>
                <td>PC All-in-One / Laptop, Intel Core i5, RAM 8 GB, Monitor 19.5 inci, Terhubung ke Printer Kasir Thermal 80mm & Printer A4.</td>
            </tr>
            <tr>
                <td><strong>Client Nail Artist & Pelanggan</strong></td>
                <td>Smartphone Android v9.0 (Pie) atau iOS 14, Layar sentuh 5.5 inci, RAM 3 GB, Koneksi 4G / Wi-Fi.</td>
                <td>Tablet Android 10+ (untuk terapis di studio) atau Smartphone 6.5 inci, RAM 4 GB+, Koneksi stabil 4G/5G/Wi-Fi.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">3.2.3 Kebutuhan Perangkat Lunak (Software Requirements)</h3>
<p>
    Ekosistem perangkat lunak yang dimanfaatkan dalam pembangunan dan eksekusi sistem Booknail disajikan pada Tabel 3.2.
</p>

<div class="table-container">
    <div class="table-title">Tabel 3.2 Spesifikasi Kebutuhan Perangkat Lunak Sistem Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kategori Perangkat Lunak</th>
                <th style="width: 35%;">Nama Perangkat Lunak & Versi</th>
                <th style="width: 40%;">Fungsi Utama dalam Sistem</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sistem Operasi Server</td>
                <td>Linux Ubuntu 24.04 LTS Server 64-bit</td>
                <td>Lingkungan dasar ekosistem hosting produksi.</td>
            </tr>
            <tr>
                <td>Web Server Engine</td>
                <td>Nginx 1.24 / Apache 2.4 (Laravel Herd)</td>
                <td>Penanganan HTTP/HTTPS requests dan reverse proxy.</td>
            </tr>
            <tr>
                <td>Bahasa Pemrograman</td>
                <td>PHP v8.2.x (CLI & FPM)</td>
                <td>Eksekusi logika pemrosesan backend aplikasi web.</td>
            </tr>
            <tr>
                <td>Kerangka Kerja Backend</td>
                <td>Laravel Framework v12.x</td>
                <td>Penyedia arsitektur MVC, ORM Eloquent, routing, & auth.</td>
            </tr>
            <tr>
                <td>Database Management System</td>
                <td>MySQL Server v8.0 Community Edition</td>
                <td>Penyimpanan relasional data transaksi (InnoDB Engine).</td>
            </tr>
            <tr>
                <td>Runtime Frontend Tooling</td>
                <td>Node.js v20.x & NPM v10.x / Vite v5.x</td>
                <td>Kompilasi lembar gaya Tailwind CSS dan JavaScript klien.</td>
            </tr>
            <tr>
                <td>Styling Framework</td>
                <td>Tailwind CSS v3.4</td>
                <td>Penyusunan antarmuka web responsif berbasis utilitas.</td>
            </tr>
            <tr>
                <td>PDF Generation Engine</td>
                <td>Barryvdh/Laravel-DomPDF v2.2 (dompdf)</td>
                <td>Pustaka pengubah dokumen HTML/CSS menjadi cetak PDF A4.</td>
            </tr>
            <tr>
                <td>Push Notification Service</td>
                <td>Google Firebase Cloud Messaging (FCM)</td>
                <td>Platform transmisi push notification real-time via Web Push.</td>
            </tr>
            <tr>
                <td>Code Editor & Pemodelan</td>
                <td>Visual Studio Code, PlantUML, Mermaid.js</td>
                <td>Penyuntingan kode program dan visualisasi diagram UML.</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">3.2.4 Personil Pengembang dan Pengguna Sistem</h3>
<p>
    Penerapan sistem Booknail melibatkan kolaborasi antara peneliti pengembang, dosen pembimbing akademis, dan pihak internal Nail Glam Studio seperti dijabarkan pada Tabel 3.3.
</p>

<div class="table-container">
    <div class="table-title">Tabel 3.3 Alokasi Peran dan Tanggung Jawab Personil Terkait Proyek Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 25%;">Nama / Jabatan</th>
                <th style="width: 25%;">Peran dalam Proyek</th>
                <th style="width: 50%;">Tanggung Jawab Utama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Leon Fernando Wijaya</strong><br>(NIM: 825210088)</td>
                <td>Peneliti & Lead Software Engineer</td>
                <td>Melakukan studi literatur, wawancara lapangan, analisis kebutuhan, perancangan sistem, pengkodean penuh, eksekusi pengujian, dan penyusunan laporan skripsi.</td>
            </tr>
            <tr>
                <td><strong>Tony, S.Kom., M.Kom., Ph.D.</strong></td>
                <td>Dosen Pembimbing Akademis</td>
                <td>Memberikan supervisi metodologis, validasi logika algoritma alokasi waktu, review kepatuhan pedoman penulisan skripsi FTI UNTAR, dan persetujuan pengujian.</td>
            </tr>
            <tr>
                <td><strong>Cindy Octavia</strong></td>
                <td>Pemilik & General Manager Nail Glam Studio</td>
                <td>Narasumber utama proses bisnis, penyedia data operasional historis, evaluator modul laporan eksekutif, dan koordinator pengujian UAT studio.</td>
            </tr>
            <tr>
                <td><strong>Admin Kasir Studio</strong></td>
                <td>Operator Sistem Utama (Administrator)</td>
                <td>Melakukan verifikasi bukti pembayaran DP, mengelola ketersediaan jam kerja terapis, dan mengekspor laporan reservasi harian/bulanan dalam PDF & CSV.</td>
            </tr>
            <tr>
                <td><strong>4 Nail Artists</strong><br>(Maya, Sisi, Clara, Dina)</td>
                <td>Pengguna Akhir Teknis (Nail Artists)</td>
                <td>Menerima alert notifikasi push FCM secara real-time pada dashboard terapis dan memperbarui status progres pengerjaan pelanggan di lokasi.</td>
            </tr>
            <tr>
                <td><strong>Calon Pelanggan</strong></td>
                <td>Pengguna Akhir Publik (Customer)</td>
                <td>Melakukan eksplorasi katalog desain kuku, reservasi mandiri 24/7 via wizard multi-layanan, dan mengunggah struk transfer komitmen uang muka (DP).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">3.2.5 Prosedur Instalasi, Konfigurasi SSL HTTPS, dan Setup Google FCM</h3>
<p>
    Pemasangan sistem Booknail pada lingkungan peladen produksi dilakukan melalui lima tahapan prosedur teknis terstandarisasi:
</p>
<ol class="academic-list">
    <li><strong>Kloning Kode Sumber & Dependensi Backend:</strong>
        <pre style="background: #0f172a; color: #f8fafc; padding: 8px; border-radius: 4px; font-family: monospace; font-size: 7.5pt; margin: 4px 0;">git clone https://github.com/untarlab/booknail.git
cd booknail
composer install --no-dev --optimize-autoloader
npm install && npm run build</pre>
    </li>
    <li><strong>Konfigurasi Berkas Lingkungan (.env):</strong> Menyalin <code>.env.example</code> menjadi <code>.env</code>, mengonfigurasi koneksi MySQL (<code>DB_DATABASE</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code>), serta menetapkan kunci aplikasi <code>php artisan key:generate</code>.</li>
    <li><strong>Migrasi Skema Basis Data & Seeding Data Awal:</strong> Mengeksekusi migrasi 15 tabel InnoDB dan menginjeksi data awal (kategori layanan, katalog treatment, profil 4 stations, dan akun staf) melalui perintah <code>php artisan migrate --seed</code>.</li>
    <li><strong>Konfigurasi Sertifikat Keamanan SSL/TLS HTTPS:</strong> Memasang sertifikat Let's Encrypt melalui Certbot Nginx (atau Herd Secured SSL pada server lokal) guna memastikan koneksi terenkripsi port 443 yang menjadi syarat mutlak eksekusi Web Push Service Worker.</li>
    <li><strong>Registrasi Kredensial Google Firebase Cloud Messaging:</strong> Membuat proyek pada Google Firebase Console, mengunduh kredensial <code>fcm_server_key</code> ke konfigurasi <code>config/services.php</code>, serta menaruh konfigurasi web SDK pada berkas publik <code>public/firebase-messaging-sw.js</code>.</li>
</ol>

<!-- ========================================== -->
<!-- 3.3 RENCANA IMPLEMENTASI & JADWAL PENGEMBANGAN -->
<!-- ========================================== -->
<h2 class="section-title">3.3 Rencana Implementasi dan Jadwal Pengembangan</h2>

<h3 class="subsection-title">3.3.1 Work Breakdown Structure (WBS)</h3>
<p>
    Aktivitas pengembangan sistem Booknail didekomposisikan ke dalam struktur rincian kerja (<em>Work Breakdown Structure</em> / WBS) yang terdiri dari 5 fase utama dan 18 sub-aktivitas terukur seperti disajikan pada Tabel 3.4.
</p>

<div class="table-container">
    <div class="table-title">Tabel 3.4 Work Breakdown Structure (WBS) Proyek Pengembangan Sistem Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 12%;">Kode WBS</th>
                <th style="width: 38%;">Deskripsi Paket Kerja (Work Package)</th>
                <th style="width: 25%;">Penanggung Jawab</th>
                <th style="width: 25%;">Artefak Keluaran (Deliverable)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1.0</strong></td>
                <td><strong>Fase Analisis Kebutuhan Sistem</strong></td>
                <td>Leon Fernando W.</td>
                <td>Dokumen Spesifikasi SRS</td>
            </tr>
            <tr>
                <td>1.1</td>
                <td>Wawancara observasi lapangan & audit proses manual</td>
                <td>Leon Fernando W. & Owner</td>
                <td>Transkrip Wawancara & Data Historis</td>
            </tr>
            <tr>
                <td>1.2</td>
                <td>Analisis kelemahan sistem berjalan (Metode PIECES)</td>
                <td>Leon Fernando W.</td>
                <td>Matriks Evaluasi PIECES</td>
            </tr>
            <tr>
                <td>1.3</td>
                <td>Spesifikasi kebutuhan fungsional & non-fungsional</td>
                <td>Leon Fernando W.</td>
                <td>Katalog 27 Use Cases</td>
            </tr>
            <tr>
                <td><strong>2.0</strong></td>
                <td><strong>Fase Perancangan Sistem (System Design)</strong></td>
                <td>Leon Fernando W.</td>
                <td>Dokumen Desain Sistem (SDD)</td>
            </tr>
            <tr>
                <td>2.1</td>
                <td>Pemodelan proses UML (Use Case, Activity, Sequence)</td>
                <td>Leon Fernando W.</td>
                <td>Diagram UML Terstandarisasi</td>
            </tr>
            <tr>
                <td>2.2</td>
                <td>Perancangan basis data relasional (CDM, LDM, PDM)</td>
                <td>Leon Fernando W.</td>
                <td>Skema Mermaid & Kamus Data</td>
            </tr>
            <tr>
                <td>2.3</td>
                <td>Perancangan struktur kelas sistem (Class Diagram)</td>
                <td>Leon Fernando W.</td>
                <td>Diagram Kode PlantUML</td>
            </tr>
            <tr>
                <td>2.4</td>
                <td>Perancangan antarmuka pengguna & mockups (WND)</td>
                <td>Leon Fernando W.</td>
                <td>Wireframe & Mockup Responsif</td>
            </tr>
            <tr>
                <td><strong>3.0</strong></td>
                <td><strong>Fase Implementasi & Pengkodean Perangkat Lunak</strong></td>
                <td>Leon Fernando W.</td>
                <td>Kode Sumber Aplikasi Booknail</td>
            </tr>
            <tr>
                <td>3.1</td>
                <td>Setup arsitektur Laravel 12 & migrasi 15 tabel database</td>
                <td>Leon Fernando W.</td>
                <td>Skema Database MySQL InnoDB</td>
            </tr>
            <tr>
                <td>3.2</td>
                <td>Pembangunan algoritma SlotAvailabilityService</td>
                <td>Leon Fernando W.</td>
                <td>Modul Kalkulasi Waktu Dinamis</td>
            </tr>
            <tr>
                <td>3.3</td>
                <td>Implementasi transaksi pessimistic lock (lockForUpdate)</td>
                <td>Leon Fernando W.</td>
                <td>Modul Transaksi Anti-Race Condition</td>
            </tr>
            <tr>
                <td>3.4</td>
                <td>Integrasi Google FCM Web Push & Service Worker</td>
                <td>Leon Fernando W.</td>
                <td>Modul Push Notification Real-Time</td>
            </tr>
            <tr>
                <td>3.5</td>
                <td>Pembangunan modul laporan & ekspor ganda (PDF & CSV)</td>
                <td>Leon Fernando W.</td>
                <td>Modul ReportController & Template</td>
            </tr>
            <tr>
                <td><strong>4.0</strong></td>
                <td><strong>Fase Pengujian & Evaluasi Sistem</strong></td>
                <td>Leon Fernando W.</td>
                <td>Laporan Hasil Pengujian Komprehensif</td>
            </tr>
            <tr>
                <td>4.1</td>
                <td>Pengujian White-Box Basis Path Testing (V(G)=6)</td>
                <td>Leon Fernando W.</td>
                <td>Flowgraph & Matriks Basis Path</td>
            </tr>
            <tr>
                <td>4.2</td>
                <td>Pengujian fungsionalitas Black-Box Testing (27 use cases)</td>
                <td>Leon Fernando W. & Admin</td>
                <td>Matriks Uji Fungsional 100% Valid</td>
            </tr>
            <tr>
                <td>4.3</td>
                <td>Pengujian beban konkurensi (simulasi race condition 15 ms)</td>
                <td>Leon Fernando W.</td>
                <td>Log Uji Isolasi Transaksi</td>
            </tr>
            <tr>
                <td>4.4</td>
                <td>Pengujian usabilitas pengguna (System Usability Scale)</td>
                <td>Leon Fernando W. & 10 Responden</td>
                <td>Kuesioner & Skor SUS (82.50)</td>
            </tr>
            <tr>
                <td><strong>5.0</strong></td>
                <td><strong>Fase Penerapan, Pelatihan, & Penyusunan Skripsi</strong></td>
                <td>Leon Fernando W.</td>
                <td>Buku Laporan Skripsi & Manual</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">3.3.2 Jadwal Pengembangan Sistem (Gantt Chart Spreadsheet)</h3>
<p>
    Proyek perancangan dan implementasi Booknail dijadwalkan berlangsung selama 16 minggu kerja efektif (Maret 2025 s/d Juni 2025). Sesuai pedoman akademik, rencana kerja dipetakan ke dalam format <strong>Spreadsheet Gantt Chart</strong> terperinci pada Tabel 3.5.
</p>

<div class="table-container">
    <div class="table-title">Tabel 3.5 Rencana Jadwal Kerja Proyek Booknail dalam Format Spreadsheet Gantt Chart (16 Minggu)</div>
    <div style="overflow-x: auto;">
        <table class="academic-table" style="font-size: 7pt; text-align: center;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 4%;">No</th>
                    <th rowspan="2" style="width: 28%; text-align: left;">Aktivitas Proyek (WBS Package)</th>
                    <th colspan="4" style="background: #e0f2fe; color: #0369a1;">Bulan 1 (Mar)</th>
                    <th colspan="4" style="background: #f0fdf4; color: #15803d;">Bulan 2 (Apr)</th>
                    <th colspan="4" style="background: #fff7ed; color: #c2410c;">Bulan 3 (Mei)</th>
                    <th colspan="4" style="background: #fdf2f8; color: #be185d;">Bulan 4 (Jun)</th>
                </tr>
                <tr>
                    <th>W1</th><th>W2</th><th>W3</th><th>W4</th>
                    <th>W5</th><th>W6</th><th>W7</th><th>W8</th>
                    <th>W9</th><th>W10</th><th>W11</th><th>W12</th>
                    <th>W13</th><th>W14</th><th>W15</th><th>W16</th>
                </tr>
            </thead>
            <tbody>
                <!-- Tahap 1 -->
                <tr>
                    <td>1</td>
                    <td style="text-align: left;">Wawancara & Observasi Nail Glam Studio</td>
                    <td class="bar-analysis">X</td><td class="bar-analysis">X</td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td style="text-align: left;">Analisis PIECES & Spesifikasi Kebutuhan</td>
                    <td></td><td class="bar-analysis">X</td><td class="bar-analysis">X</td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <!-- Tahap 2 -->
                <tr>
                    <td>3</td>
                    <td style="text-align: left;">Pemodelan UML (Activity & Sequence Diagram)</td>
                    <td></td><td></td><td class="bar-design">X</td><td class="bar-design">X</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>4</td>
                    <td style="text-align: left;">Desain Database (Mermaid CDM/LDM/PDM)</td>
                    <td></td><td></td><td></td><td class="bar-design">X</td>
                    <td class="bar-design">X</td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>5</td>
                    <td style="text-align: left;">Desain Antarmuka Responsif (Wireframing UI)</td>
                    <td></td><td></td><td></td><td></td>
                    <td class="bar-design">X</td><td class="bar-design">X</td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <!-- Tahap 3 -->
                <tr>
                    <td>6</td>
                    <td style="text-align: left;">Setup Laravel 12 & Migrasi 15 Tabel Database</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td class="bar-coding">X</td><td class="bar-coding">X</td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>7</td>
                    <td style="text-align: left;">Koding Algoritma SlotAvailabilityService</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td class="bar-coding">X</td><td class="bar-coding">X</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>8</td>
                    <td style="text-align: left;">Koding DB::transaction & Pessimistic Lock</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td class="bar-coding">X</td>
                    <td class="bar-coding">X</td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>9</td>
                    <td style="text-align: left;">Integrasi Google FCM Web Push & Service Worker</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td class="bar-coding">X</td><td class="bar-coding">X</td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>10</td>
                    <td style="text-align: left;">Koding Modul Laporan & Export PDF/CSV</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td class="bar-coding">X</td><td class="bar-coding">X</td><td></td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <!-- Tahap 4 -->
                <tr>
                    <td>11</td>
                    <td style="text-align: left;">White-Box Basis Path Testing (V(G)=6)</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td class="bar-testing">X</td><td class="bar-testing">X</td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>12</td>
                    <td style="text-align: left;">Black-Box Testing (27 Use Cases) & Konkurensi</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td class="bar-testing">X</td>
                    <td class="bar-testing">X</td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>13</td>
                    <td style="text-align: left;">Evaluasi Usabilitas SUS (10 Responden)</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td class="bar-testing">X</td><td class="bar-testing">X</td><td></td><td></td>
                </tr>
                <!-- Tahap 5 -->
                <tr>
                    <td>14</td>
                    <td style="text-align: left;">Deployment Staging HTTPS & Pelatihan Staf</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td class="bar-analysis">X</td><td class="bar-analysis">X</td><td></td>
                </tr>
                <tr>
                    <td>15</td>
                    <td style="text-align: left;">Penyusunan Laporan Akhir Skripsi & Sidang</td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td></td><td></td>
                    <td></td><td></td><td class="bar-analysis">X</td><td class="bar-analysis">X</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- 3.4 PROSEDUR PEMELIHARAAN SISTEM -->
<!-- ========================================== -->
<h2 class="section-title">3.4 Prosedur Pemeliharaan Sistem</h2>
<p>
    Setelah fase implementasi dan peluncuran (<em>deployment</em>), sistem Booknail dirawat melalui prosedur pemeliharaan berkelanjutan yang mencakup empat klasifikasi pemeliharaan standar rekayasa perangkat lunak [19]:
</p>
<ul class="academic-list">
    <li><strong>Pemeliharaan Korektif (Corrective Maintenance):</strong> Mengidentifikasi dan memperbaiki anomali atau kegagalan logika (<em>software bugs</em>) yang tidak terdeteksi saat pengujian awal. Mekanisme pelaporan terpusat memanfaatkan logging Laravel pada <code>storage/logs/laravel.log</code> yang otomatis merekam jejak galat (<em>stack trace</em>) saat terjadi kegagalan HTTP 500 atau kegagalan pengiriman push notification FCM.</li>
    <li><strong>Pemeliharaan Adaptif (Adaptive Maintenance):</strong> Menyesuaikan perangkat lunak dengan perubahan lingkungan komputasi eksternal, seperti pembaruan versi PHP runtime pada server, pembaruan skema API Google Firebase Cloud Messaging, atau perubahan kebijakan izin peramban web terhadap <em>Service Worker</em>.</li>
    <li><strong>Pemeliharaan Perfektif (Perfective Maintenance):</strong> Menyempurnakan efisiensi performa dan pengalaman pengguna berdasarkan umpan balik operasional sehari-hari di Nail Glam Studio. Pemeliharaan ini mencakup penambahan indeks komposit basis data pada kolom tanggal dan waktu pemesanan untuk mempercepat kueri laporan tahunan, serta pengoptimalan kompresi gambar bukti transfer otomatis.</li>
    <li><strong>Pemeliharaan Preventif (Preventive Maintenance):</strong> Mengantisipasi potensi degradasi keandalan sistem sebelum menimbulkan gangguan nyata. Langkah preventif mencakup penjadwalan pencadangan basis data otomatis (<em>database backup automation</em>) setiap tengah malam via <code>mysqldump</code> yang diunggah ke penyimpanan terpisah, pembersihan berkas sesi kedaluwarsa secara berkala, serta rotasi berkas log sistem.</li>
</ul>
"""
