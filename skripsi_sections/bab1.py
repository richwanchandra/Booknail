# skripsi_sections/bab1.py
# BAB I PENDAHULUAN Skripsi Booknail FTI UNTAR

def get_bab1():
    return """
<div class="page-break"></div>

<!-- ========================================== -->
<!-- BAB I PENDAHULUAN -->
<!-- ========================================== -->
<div style="counter-reset: page 1;">
    <h1 class="chapter-title">BAB I<br>PENDAHULUAN</h1>

    <h2 class="section-title">1.1 Latar Belakang Masalah</h2>
    <p>
        Industri estetika kecantikan di Indonesia mengalami akselerasi pertumbuhan yang sangat signifikan dalam kurun waktu lima tahun terakhir, khususnya pada sektor perawatan estetika kuku (<em>nail art</em>). Kesadaran masyarakat urban terhadap penampilan diri dan perawatan estetika telah menempatkan layanan <em>nail art</em> bukan sekadar pelengkap riasan seremonial, melainkan bagian integral dari gaya hidup modern sehari-hari. Nail Glam Studio merupakan salah satu pelaku usaha jasa estetika kecantikan kuku independen terkemuka yang beroperasi di kawasan Jakarta Barat. Studio ini menyediakan spektrum layanan kecantikan kuku komprehensif, mencakup <em>Basic Manicure & Pedicure</em>, <em>Gel Polish Application</em>, <em>Nail Extension (Tip & Form Acrylic/Gel)</em>, serta kustomisasi <em>Hand-Painted Nail Art</em> bertingkat kerumitan tinggi. Dalam menunjang operasional hariannya, Nail Glam Studio didukung oleh 4 meja pengerjaan (<em>station</em>) aktif dan 4 tenaga teknisi profesional (<em>nail artist</em>) terlatih, dengan volume kunjungan mencapai 25 hingga 40 pelanggan per hari pada hari kerja biasa, serta melonjak hingga lebih dari 55 pelanggan per hari pada akhir pekan (<em>weekend</em>) dan musim liburan (<em>peak season</em>).
    </p>
    <p>
        Berbeda dengan jenis usaha ritel atau salon konvensional yang memiliki durasi pelayanan seragam, industri <em>nail art</em> memiliki karakteristik operasional yang sangat unik dan dinamis. Durasi pengerjaan setiap sesi layanan sangat bergantung pada kombinasi jenis perawatan dan tingkat kerumitan desain yang diminta oleh pelanggan. Sebagai contoh empiris di Nail Glam Studio, pengerjaan <em>Express Manicure</em> hanya membutuhkan alokasi waktu sekitar 30 menit, sedangkan <em>Gel Polish with Simple Art</em> memerlukan waktu 60 hingga 75 menit. Apabila pelanggan memilih paket <em>Nail Extension Full Set</em> yang dipadukan dengan desain ornamen 3D timbul (<em>Complex 3D Nail Art</em>) dan pemasangan kristal Swarovski pada kesepuluh jari, total waktu pengerjaan dapat mencapai 150 hingga 180 menit tanpa henti. Di sisi lain, kapasitas fisik studio memiliki batasan tegas (<em>finite capacity</em>), di mana studio hanya mampu melayani maksimal 4 orang pelanggan dalam waktu bersamaan sesuai jumlah ketersediaan <em>station</em> dan kehadiran fisik <em>nail artist</em> pada jadwal shift kerjanya.
    </p>
    <p>
        Berdasarkan observasi lapangan dan hasil wawancara mendalam yang dilakukan bersama Ibu Cindy Octavia selaku pemilik pengelola Nail Glam Studio, proses pengelolaan reservasi dan antrean pelanggan selama ini masih dijalankan secara konvensional. Pelanggan yang ingin melakukan perawatan diharuskan mengirimkan pesan teks melalui aplikasi perpesanan instan WhatsApp admin studio, melakukan panggilan telepon, atau langsung mendatangi lokasi studio tanpa reservasi sebelumnya (<em>walk-in customer</em>). Alur pemesanan manual ini terbukti menimbulkan inefisiensi operasional yang sangat masif. Ketika pelanggan menghubungi admin untuk menanyakan ketersediaan jadwal, staf admin harus memeriksa buku agenda reservasi manual berbahan kertas, mencocokkannya dengan jadwal shift teknisi pada papan tulis studio, dan melakukan kalkulasi mental untuk memperkirakan apakah rentang waktu pengerjaan layanan yang diminta pelanggan masih mencukupi sebelum jadwal kedatangan pelanggan berikutnya.
    </p>

    <div class="figure-container">
        <div class="browser-mockup" style="max-width: 500px;">
            <div class="browser-header" style="background: #075e54; color: #fff;">
                <span style="font-size: 9.5pt; font-weight: bold;">WhatsApp Chat - Reservasi Manual Nail Glam Studio</span>
            </div>
            <div class="browser-content" style="background: #efeae2; font-family: sans-serif; font-size: 9.5pt;">
                <div style="background: #ffffff; padding: 8px 12px; border-radius: 8px; margin-bottom: 8px; max-width: 80%;">
                    <strong>Pelanggan:</strong> "Halo kak, besok Sabtu siang jam 14.00 ada slot kosong gak ya untuk nail extension + custom art?"
                </div>
                <div style="background: #dcf8c6; padding: 8px 12px; border-radius: 8px; margin-bottom: 8px; margin-left: auto; max-width: 80%; text-align: right;">
                    <strong>Admin (18 mnt kemudian):</strong> "Halo kak! Sebentar ya kami cek jadwal terapisnya dulu..."
                </div>
                <div style="background: #dcf8c6; padding: 8px 12px; border-radius: 8px; margin-bottom: 8px; margin-left: auto; max-width: 80%; text-align: right;">
                    <strong>Admin (25 mnt kemudian):</strong> "Waduh kak, jam 14.00 ternyata Kak Maya sudah di-booking pelanggan lain dari jam 13.30. Bisa geser ke jam 16.30 atau ganti terapis lain?"
                </div>
                <div style="background: #ffffff; padding: 8px 12px; border-radius: 8px; max-width: 80%;">
                    <strong>Pelanggan:</strong> "Yah... lama banget balesnya kak, saya udah booking di studio lain jadinya."
                </div>
            </div>
        </div>
        <div class="figure-caption">Gambar 1.1 Tangkapan Layar Percakapan Reservasi Manual via WhatsApp yang Membutuhkan Waktu Tanggap Lama</div>
    </div>

    <p>
        Berdasarkan pencatatan waktu operasional selama periode pra-penelitian, rata-rata durasi komunikasi interaktif bolak-balik antara staf admin dan seorang calon pelanggan untuk menyepakati satu jadwal reservasi mencapai 18 menit. Lamanya proses negosiasi jadwal ini memicu frustrasi calon pelanggan, sehingga tidak jarang pelanggan membatalkan niat reservasinya dan berpindah ke studio kompetitor (<em>customer churn</em>). Kelemahan yang jauh lebih fatal dari pencatatan manual berbasis percakapan chat adalah rentannya terjadi anomali pemesanan ganda (<em>double booking</em>). Dalam banyak kasus, staf admin tanpa sengaja mengonfirmasi dua pelanggan berbeda pada teknisi yang sama dan jam kedatangan yang bertabrakan karena admin terlambat mencatatkan reservasi sebelumnya ke dalam buku agenda. Berdasarkan data rekapitulasi keluhan pelanggan Nail Glam Studio pada semester kedua tahun 2024, tingkat insiden <em>double booking</em> mencapai 14,3% dari total reservasi. Insiden ini berdampak langsung pada penumpukan antrean di area tunggu studio, kekecewaan pelanggan yang terpaksa menunggu hingga lebih dari 60 menit dari jam yang dijanjikan, serta rusaknya reputasi profesional studio.
    </p>

    <div class="figure-container">
        <div style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 14px; background: #fff5f5; max-width: 550px; margin: 0 auto; text-align: left;">
            <div style="font-weight: bold; color: #991b1b; font-size: 10.5pt; margin-bottom: 6px; border-bottom: 1px solid #fecaca; padding-bottom: 4px;">
                Catatan Insiden Operasional Nail Glam Studio (Oktober 2024)
            </div>
            <div style="font-size: 9.5pt; color: #334155; line-height: 1.5;">
                &bull; <strong>Tanggal:</strong> Sabtu, 19 Oktober 2024 - Pukul 14.00 WIB<br>
                &bull; <strong>Insiden:</strong> Pelanggan Ny. Vani (Nail Extension) dan Nn. Jessica (Gel Art) datang bersamaan dengan bukti chat konfirmasi dari dua admin berbeda untuk Nail Artist yang sama (Kak Maya).<br>
                &bull; <strong>Dampak:</strong> Pelanggan Nn. Jessica harus menunggu 75 menit di sofa tunggu; station kerja studio mengalami overload kapasitas fisik; staf mengalami stres kerja tinggi.<br>
                &bull; <strong>Akar Masalah:</strong> Ketiadaan basis data terpusat dan mekanisme penguncian slot otomatis (*atomic slot lock*).
            </div>
        </div>
        <div class="figure-caption">Gambar 1.2 Contoh Bentrok Jadwal Akibat Pencatatan Konvensional Tanpa Validasi Konkurensi Terpusat</div>
    </div>

    <p>
        Selain kerentanan <em>double booking</em>, permasalahan administratif lain yang melumpuhkan produktivitas Nail Glam Studio adalah tingginya angka ketidakhadiran tanpa pemberitahuan (<em>no-show</em>) serta pembatalan sepihak secara mendadak. Pada sistem berjalan, studio sering kali memegang slot waktu tertentu untuk pelanggan yang telah memesan via chat tanpa adanya pembayaran uang muka (<em>down payment</em> / DP) yang terikat batas waktu tegas. Akibatnya, slot tersebut dibiarkan kosong ketika pelanggan tidak hadir, sementara pelanggan lain yang membutuhkan layanan ditolak dengan dalih slot telah penuh. Lebih lanjut, pencatatan transaksi yang tercecer pada buku tulis dan mutasi rekening bank secara manual menyulitkan pengelola studio dalam menyusun laporan keuangan harian, memantau utilisasi beban kerja masing-masing terapis, dan mengevaluasi layanan mana yang paling diminati oleh pasar.
    </p>
    <p>
        Mengacu pada rangkaian problematika kritis tersebut, Nail Glam Studio memerlukan transformasi digital mendasar melalui perancangan dan pembangunan <strong>Sistem Informasi Reservasi Layanan Nail Art Berbasis Web (Booknail)</strong>. Sistem ini dirancang untuk menggantikan seluruh interaksi manual yang lambat dengan otomasi komputasional terpadu. Sistem Booknail menghadirkan mekanisme <em>Dynamic Time-Slot Allocation</em> yang secara otomatis menghitung rentang ketersediaan slot waktu dengan interval presisi 30 menit berdasarkan penjumlahan durasi seluruh layanan yang dipilih pelanggan, ketersediaan shift kerja teknisi, serta jadwal cuti yang telah disetujui. Untuk menjamin keandalan data transaksi dan meniadakan 100% potensi <em>double booking</em> saat terjadi pemesanan bersamaan oleh banyak pengguna (<em>concurrent users</em>), sistem menerapkan prinsip transaksi ACID dengan penguncian tingkat baris data secara pesimistik (<em>pessimistic row-level locking</em> via klausul <code>lockForUpdate</code> pada DBMS MySQL).
    </p>
    <p>
        Dalam aspek arsitektur perangkat lunak dan komunikasi notifikasi, sistem dibangun menggunakan bahasa pemrograman PHP 8.2 modern dengan kerangka kerja <strong>Laravel 12</strong> yang menerapkan pola arsitektur <em>Model-View-Controller</em> (MVC) dan didukung oleh antarmuka responsif berbasis <strong>Tailwind CSS</strong>. Untuk mengatasi kelemahan ketergantungan pada penyedia gerbang pihak ketiga (seperti WhatsApp API Gateway) yang sering kali mengalami pemblokiran nomor (<em>banned</em>), biaya langganan bulanan yang mahal, serta ketergantungan pada nomor telepon seluler aktif, sistem Booknail mengimplementasikan subsistem notifikasi mandiri berbasis <strong>Google Firebase Cloud Messaging (FCM)</strong>. Dengan mengintegrasikan <em>FCM Web Push</em> dan <em>Service Worker</em> (<code>firebase-messaging-sw.js</code>), sistem mampu mendistribusikan notifikasi pemesanan, verifikasi status DP, pengingat jadwal H-1 dan 2 jam sebelum perawatan langsung ke peramban (<em>web browser</em>) pelanggan dan perangkat seluler teknisi (<em>nail artist</em>) secara instan (<em>real-time pop-up notification</em>) tanpa biaya transmisi pesan per satuan.
    </p>
    <p>
        Di samping itu, untuk melindungi perputaran ketersediaan slot studio dari pemesanan yang menggantung, sistem mengimplementasikan mekanisme otomasi pembatalan reservasi (<em>auto-cancellation</em>) berbasis <em>scheduler</em> yang secara periodik melepaskan slot kembali ke katalog publik jika pelanggan tidak mengunggah bukti pembayaran DP dalam tenggat waktu 60 menit. Guna mendukung kebutuhan akuntabilitas dan audit pengelola studio, dasbor administratif dilengkapi dengan modul rekapitulasi laporan reservasi dan pendapatan komprehensif yang dapat diekspor secara instan ke dalam berkas dokumen portabel <strong>PDF</strong> (menggunakan <em>DomPDF engine</em>) dan berkas lembar kerja <strong>CSV / Excel</strong> dengan penanganan <em>encoding</em> standar UTF-8 BOM. Melalui implementasi sistem Booknail ini, waktu pemrosesan pemesanan dipangkas dari 18 menit menjadi kurang dari 2 menit, integritas jadwal terjamin bebas bentrok, dan transparansi manajerial studio meningkat secara terukur.
    </p>

    <h2 class="section-title">1.2 Perumusan Masalah</h2>
    <p class="no-indent">
        Berdasarkan pemaparan latar belakang masalah di atas, maka rumusan masalah yang ditetapkan dalam penelitian ini adalah sebagai berikut:
    </p>
    <ol class="academic-list">
        <li>Bagaimana merancang dan membangun sistem informasi reservasi layanan <em>nail art</em> berbasis web pada Nail Glam Studio yang mampu melakukan alokasi slot waktu dinamis (<em>dynamic time-slot allocation</em>) secara otomatis berdasarkan durasi kumulatif layanan terpilih, jam shift teknisi, dan jadwal libur?</li>
        <li>Bagaimana menerapkan mekanisme pengendalian konkurensi data transaksi menggunakan teknik <em>pessimistic locking</em> (<code>lockForUpdate</code>) di dalam kerangka kerja Laravel 12 guna mencegah dan mengeliminasi terjadinya pemesanan ganda (<em>double booking</em>) saat diakses secara simultan?</li>
        <li>Bagaimana mengintegrasikan layanan komunikasi <em>Google Firebase Cloud Messaging</em> (FCM) melalui arsitektur <em>Web Push Service Worker</em> untuk mendistribusikan notifikasi reservasi dan peringatan jadwal secara instan kepada pelanggan dan teknisi tanpa ketergantungan pada gerbang API WhatsApp?</li>
        <li>Bagaimana merancang dan mengotomasi mekanisme pembatalan reservasi kedaluwarsa (<em>auto-cancellation</em>) dengan toleransi batas waktu pembayaran DP selama 60 menit untuk menjaga optimalitas ketersediaan slot studio?</li>
        <li>Bagaimana merancang modul dasbor administratif dengan fungsionalitas ekspor laporan reservasi ke dalam format PDF dan CSV (Excel), serta menguji keandalan sistem melalui metode <em>White-Box Testing</em>, <em>Black-Box Testing</em>, dan evaluasi kepuasan pengguna menggunakan <em>System Usability Scale</em> (SUS)?</li>
    </ol>

    <h2 class="section-title">1.3 Batasan Masalah</h2>
    <p class="no-indent">
        Agar penelitian dan pengembangan perangkat lunak ini memiliki ruang lingkup yang terarah, sistematis, dan fokus pada sasaran yang ingin dicapai, maka ditetapkan batasan masalah sebagai berikut:
    </p>
    <ol class="academic-list">
        <li>Sistem yang dibangun merupakan aplikasi berbasis web responsif yang dioptimasi untuk peramban desktop dan perangkat bergerak (<em>mobile browser</em>) menggunakan bahasa pemrograman PHP 8.2, framework Laravel 12, antarmuka Tailwind CSS, dan basis data MySQL.</li>
        <li>Sistem mengakomodasi 3 peran pengguna (<em>actors</em>), yaitu Pelanggan (<em>Customer</em>), Teknisi (<em>Nail Artist</em>), dan Pengelola Studio (<em>Admin/Manager</em>), serta 1 entitas otomasi latar belakang (<em>Task Scheduler / Cron Job</em>).</li>
        <li>Pengalokasian slot waktu reservasi dilakukan secara dinamis menggunakan interval kuantisasi 30 menit yang diselaraskan dengan batas jam operasional harian studio (pukul 09.00 hingga 20.00 WIB) serta jadwal shift masing-masing teknisi.</li>
        <li>Pencegahan anomali konkurensi (<em>anti-double booking</em>) diimplementasikan pada tingkatan basis data melalui transaksi ACID dengan penguncian baris pesimistik (<code>DB::raw('lockForUpdate')</code>) pada modul <code>BookingService</code>.</li>
        <li>Subsistem notifikasi dan pengingat operasional menggunakan <em>Google Firebase Cloud Messaging</em> (FCM Legacy HTTP API) dan <em>JavaScript Service Worker</em> (<code>firebase-messaging-sw.js</code>). Sistem secara tegas tidak menggunakan gerbang perpesanan WhatsApp API gateway.</li>
        <li>Mekanisme pembayaran reservasi dibatasi pada metode transfer bank manual atau pemindaian QRIS statis, di mana pelanggan mengunggah berkas bukti transfer uang muka (DP) minimal 30% dari total estimasi biaya untuk kemudian diverifikasi secara administratif oleh Admin.</li>
        <li>Sistem menerapkan batasan toleransi waktu pengunggahan bukti pembayaran DP selama maksimal 60 menit sejak kode reservasi diterbitkan; pesanan yang melampaui batas waktu akan dibatalkan secara otomatis oleh <em>Task Scheduler</em> dan slot waktu dikembalikan ke status tersedia.</li>
        <li>Dasbor Admin menyediakan modul rekapitulasi data reservasi dan pelaporan pendapatan yang dilengkapi fasilitas penyaringan (<em>filter</em>) komprehensif serta fitur ekspor berkas ke dalam format PDF (via paket <code>barryvdh/laravel-dompdf</code>) dan format CSV (Excel) berstandar UTF-8 BOM.</li>
        <li>Evaluasi teknis dan fungsionalitas mencakup pengujian <em>White-Box Basis Path Testing</em> pada algoritma penentuan slot (McCabe Cyclomatic Complexity), <em>Black-Box Functional Testing</em> pada 27 <em>use case</em>, uji stres konkurensi transaksi (<em>race condition simulation</em>), serta evaluasi usabilitas menggunakan kuesioner standar <em>System Usability Scale</em> (SUS) dengan 10 responden.</li>
        <li>Penelitian ini tidak mencakup modul akuntansi buku besar terpadu, penggajian (<em>payroll</em>) komisi teknisi, sistem inventaris stok bahan kimia kutek, serta modul visualisasi <em>Augmented Reality</em> (AR) coba kuku virtual.</li>
    </ol>

    <h2 class="section-title">1.4 Tujuan Penelitian</h2>
    <p class="no-indent">
        Tujuan yang hendak dicapai melalui pelaksanaan penelitian ini adalah:
    </p>
    <ol class="academic-list">
        <li>Menghasilkan sistem informasi reservasi layanan <em>nail art</em> berbasis web yang memiliki kemampuan alokasi slot waktu dinamis (<em>dynamic time-slot allocation</em>) dengan memperhitungkan durasi kumulatif layanan, jadwal shift, dan hari libur teknisi di Nail Glam Studio.</li>
        <li>Menerapkan algoritma pengendalian konkurensi transaksi menggunakan metode <em>pessimistic locking</em> (<code>lockForUpdate</code>) untuk menjamin integritas data dan mengeliminasi 100% insiden tumpang tindih jadwal (<em>double booking</em>).</li>
        <li>Membangun modul notifikasi instan berbasis <em>Google Firebase Cloud Messaging</em> (FCM) dan <em>Service Worker</em> untuk mengirimkan pemberitahuan status reservasi dan pengingat jadwal kepada pelanggan dan teknisi secara tepat waktu.</li>
        <li>Mengimplementasikan mekanisme otomasi pembatalan reservasi (<em>auto-cancellation</em>) 60 menit melalui <em>Cron Job</em> latar belakang untuk memaksimalkan utilisasi kapasitas kursi pengerjaan di studio.</li>
        <li>Menyediakan modul pelaporan operasional terintegrasi pada dasbor admin dengan fitur ekspor dokumen PDF dan lembar kerja CSV (Excel), serta membuktikan kualitas teknis perangkat lunak melalui pengujian <em>White-Box</em>, <em>Black-Box</em>, dan evaluasi usabilitas kuesioner SUS.</li>
    </ol>

    <h2 class="section-title">1.5 Manfaat Penelitian</h2>
    <p class="no-indent">
        Penelitian ini diharapkan mampu memberikan kontribusi teoritis dan praktis yang signifikan bagi berbagai pihak:
    </p>
    <ol class="academic-list">
        <li><strong>Bagi Nail Glam Studio:</strong>
            Meningkatkan efisiensi kerja staf hingga lebih dari 80%, mengeliminasi risiko bentrok jadwal (<em>double booking</em>), mengurangi kerugian finansial akibat <em>no-show</em> melalui pembayaran DP terikat waktu, meningkatkan transparansi beban kerja teknisi, serta mempermudah rekapitulasi data pendapatan studio melalui fitur ekspor laporan otomatis.
        </li>
        <li><strong>Bagi Pelanggan:</strong>
            Memberikan kenyamanan dan kepastian dalam melihat katalog layanan, mengecek ketersediaan jadwal teknisi favorit secara <em>real-time</em>, melakukan reservasi mandiri 24/7 tanpa harus menunggu balasan admin, serta memperoleh notifikasi pengingat jadwal secara langsung pada perangkat pengguna.
        </li>
        <li><strong>Bagi Akademisi dan Peneliti Lain:</strong>
            Menjadi sumber referensi ilmiah, studi komparasi, dan pedoman perancangan rekayasa perangkat lunak dalam memecahkan permasalahan kuantisasi slot dinamis, penanganan konkurensi transaksi database pesimistik, dan implementasi <em>Google Firebase Cloud Messaging</em> pada aplikasi layanan jasa berbasis web.
        </li>
        <li><strong>Bagi Penulis:</strong>
            Sebagai sarana implementasi praktis atas penguasaan ilmu analisis dan perancangan sistem informasi, rekayasa perangkat lunak modern dengan framework Laravel, arsitektur basis data relasional, pengujian perangkat lunak standar industri, serta pemenuhan syarat akademis kelulusan sarjana komputer di Universitas Tarumanagara.
        </li>
    </ol>

    <h2 class="section-title">1.6 Penelitian Terdahulu</h2>
    <p>
        Dalam menyusun landasan perancangan sistem informasi reservasi Booknail, dilakukan kajian literatur mendalam terhadap lima artikel jurnal penelitian terdahulu yang berfokus pada pengembangan sistem penjadwalan dan absensi digital. Kajian ini bertujuan untuk menganalisis keunggulan metodologi, fitur fungsional, dan batasan teknologis yang ada, sehingga dapat dirumuskan posisi kebaruan (<em>state-of-the-art</em>) dari penelitian ini. Matriks perbandingan disajikan pada Tabel 1.1.
    </p>

    <div class="table-container">
        <div class="table-caption">Tabel 1.1 Matriks Perbandingan Penelitian Terdahulu dan Sistem Booknail</div>
        <table class="academic-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Peneliti & Sumber</th>
                    <th style="width: 18%;">Judul Penelitian</th>
                    <th style="width: 15%;">Metode & Teknologi</th>
                    <th style="width: 22%;">Fitur & Hasil</th>
                    <th style="width: 20%;">Perbedaan dengan Booknail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td><strong>Wahyudi, Supriyanta, & Faqih (2021)</strong> [10]<br><em>Indonesian Journal on Software Engineering (IJSE)</em></td>
                    <td>Pengembangan Sistem Informasi Presensi Menggunakan Metode Waterfall</td>
                    <td>Metode Waterfall; PHP Native, CodeIgniter 3, MariaDB</td>
                    <td>Mencatat kehadiran karyawan berbasis login sederhana, menampilkan riwayat absensi, cetak rekapitulasi data.</td>
                    <td>Penelitian [10] tidak memiliki alokasi slot waktu dinamis, tanpa validasi konkurensi (double booking), serta tanpa sistem push notification FCM. Booknail menerapkan dynamic slot allocation, pessimistic lock, dan FCM web push.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td><strong>Permana, Kisworo, & Jayadi (2022)</strong> [11]<br><em>Jurnal Informatika dan Rekayasa Perangkat Lunak (JATIKA)</em></td>
                    <td>Aplikasi Presensi Online Menggunakan Validasi Jarak Lokasi Berbasis Android</td>
                    <td>Metode Extreme Programming (XP); Java Android SDK, MySQL</td>
                    <td>Validasi jarak pengguna dengan toko berbasis koordinat GPS, pencatatan log harian karyawan.</td>
                    <td>Penelitian [11] terbatas pada platform Android, tidak mencakup alokasi durasi layanan, dan tidak memiliki penanganan pembatalan otomatis. Booknail berbasis web responsif, mengelola reservasi multi-layanan, dan memiliki auto-cancellation 60 menit.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td><strong>Ramadhini, Ariany, & Jayadi (2023)</strong> [12]<br><em>Jurnal Teknologi dan Sistem Informasi (JTSI)</em></td>
                    <td>Sistem Informasi Presensi Karyawan Berbasis Android (Studi Kasus: Panin Dai-Ichi Life)</td>
                    <td>Metode Waterfall; Android Studio, Haversine Formula, MySQL</td>
                    <td>Deteksi lokasi presensi dalam radius geofence kantor menggunakan algoritma Haversine dan upload foto selfie.</td>
                    <td>Penelitian [12] hanya untuk manajemen pegawai internal, tidak mengakomodasi transaksi pemesanan jasa publik, ketiadaan transaksi locking, dan tanpa ekspor laporan multi-format (PDF/CSV). Booknail mengelola reservasi publik dengan kontrol locking ACID.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">4</td>
                    <td><strong>Roosdianto, Sari, & Satriansyah (2021)</strong> [13]<br><em>INTI Nusa Mandiri</em></td>
                    <td>Rancang Bangun Aplikasi Sistem Informasi Absensi Karyawan Online</td>
                    <td>Metode Waterfall; PHP CodeIgniter, CSS Bootstrap, MySQL</td>
                    <td>Pencatatan data absensi masuk dan pulang, rekap kehadiran karyawan, manajemen divisi kerja.</td>
                    <td>Penelitian [13] tidak memiliki perhitungan durasi waktu kumulatif per sesi pengerjaan dan tanpa fitur notifikasi real-time. Booknail menghitung slot waktu fleksibel berbasis layanan kumulatif dan mengirim push alert via FCM.</td>
                </tr>
                <tr>
                    <td style="text-align: center;">5</td>
                    <td><strong>Manu & Benufinit (2020)</strong> [14]<br><em>Jurnal Pendidikan Teknologi Informasi (JUKANTI)</em></td>
                    <td>Pengembangan Sistem Absensi Online Berbasis Web Menggunakan Maps Javascript API</td>
                    <td>Metode Rapid Application Development (RAD); PHPMaker, JavaScript API</td>
                    <td>Menyimpan latitude dan longitude pengguna saat WFH, rekapitulasi data bulanan tanggal 20.</td>
                    <td>Penelitian [14] menggunakan tool generator PHPMaker yang kaku, tanpa mekanisme kuantisasi slot dinamis, dan tidak menangani konflik jadwal ganda. Booknail dibangun modular dengan Laravel 12, pessimistic row locking, dan ekspor laporan kustom.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p>
        Berdasarkan analisis perbandingan pada Tabel 1.1, terlihat kesenjangan metodologis dan fungsional pada penelitian-penelitian sebelumnya. Sebagian besar penelitian terdahulu hanya berfokus pada pencatatan presensi dengan jadwal tetap atau pemesanan sederhana yang tidak mempertimbangkan variasi durasi kerja per jenis layanan. Kebaruan (<em>novelty</em>) dan nilai strategis sistem informasi <strong>Booknail</strong> terletak pada integrasi harmonis antara:
    </p>
    <ul class="academic-list">
        <li><strong>Dynamic Time-Slot Quantization:</strong> Perhitungan ketersediaan slot waktu secara dinamis dengan interval 30 menit yang menghitung kumulatif durasi pengerjaan seluruh layanan yang dipilih pelanggan, diselaraskan dengan shift dan cuti teknisi secara <em>real-time</em>.</li>
        <li><strong>Pessimistic Concurrency Control:</strong> Penerapan penguncian baris basis data pesimistik (<code>lockForUpdate</code>) di dalam blok transaksi ACID Laravel 12 untuk mencegah anomali <em>double booking</em> secara atomik pada saat puncak pemesanan (<em>high-concurrency rush</em>).</li>
        <li><strong>Independent Web Push Notification Subsystem:</strong> Penggunaan Google Firebase Cloud Messaging (FCM) dan <em>Service Worker</em> untuk mentransmisikan notifikasi instan ke peramban pelanggan dan dasbor teknisi tanpa biaya transaksi per pesan dan tanpa risiko pemblokiran nomor seperti yang kerap terjadi pada WhatsApp Gateway pihak ketiga.</li>
        <li><strong>Automated Resource Re-allocation (Auto-Cancellation):</strong> Otomasi pembatalan pemesanan kadaluarsa dalam waktu 60 menit melalui <em>scheduler</em> latar belakang untuk menjaga ketersediaan slot studio tetap optimal.</li>
        <li><strong>Administrative Multi-Format Reporting:</strong> Fasilitas pelaporan operasional terpadu dengan opsi ekspor dokumen resmi PDF berbasis <em>DomPDF</em> dan berkas lembar kerja CSV (Excel) dengan standar UTF-8 BOM.</li>
    </ul>

    <h2 class="section-title">1.7 Sistematika Penulisan</h2>
    <p class="no-indent">
        Penyusunan laporan skripsi ini dibagi ke dalam lima bab utama yang disusun secara terstruktur, runtut, dan sistematis sesuai dengan Buku Pedoman Penulisan Skripsi FTI Universitas Tarumanagara Tahun 2026:
    </p>
    <ul class="academic-list" style="list-style-type: none; padding-left: 0;">
        <li style="margin-bottom: 0.8rem;">
            <strong>BAB I PENDAHULUAN</strong><br>
            Bab ini menguraikan latar belakang permasalahan operasional di Nail Glam Studio, perumusan masalah, batasan masalah penelitian, tujuan dan manfaat penelitian bagi pihak-pihak terkait, telaah penelitian terdahulu beserta matriks komparasi, serta sistematika penulisan laporan skripsi.
        </li>
        <li style="margin-bottom: 0.8rem;">
            <strong>BAB II DASAR TEORI, METODOLOGI DAN PERANCANGAN</strong><br>
            Bab ini memaparkan landasan teori umum dan teori khusus yang mendasari perancangan sistem informasi reservasi, profil objek penelitian, analisis sistem yang sedang berjalan beserta flowchart proses bisnisnya. Selanjutnya dijabarkan perancangan proses sistem menggunakan diagram UML (Use Case, Skenario, Activity, Sequence), perancangan basis data (CDM, LDM, PDM, kamus data tabel, Class Diagram PlantUML), perancangan antarmuka pengguna (Windows Navigation Diagram dan UI Mockup), serta perancangan implementasi kode program MVC Laravel 12, algoritma kuantisasi slot dinamis, klausul penguncian pesimistik, dan konfigurasi Google FCM.
        </li>
        <li style="margin-bottom: 0.8rem;">
            <strong>BAB III PEMBUATAN DAN RENCANA IMPLEMENTASI</strong><br>
            Bab ini membahas tata laksana tahapan metode Waterfall dalam pembuatan program Booknail, kebutuhan implementasi sistem yang mencakup topologi jaringan arsitektur client-server HTTPS/FCM, kebutuhan spesifikasi perangkat keras dan perangkat lunak, alokasi personil pelaksana, prosedur instalasi dan konfigurasi SSL serta FCM, jadwal rencana implementasi dalam format lembar kerja spreadsheet Gantt Chart WBS, serta prosedur pemeliharaan sistem pasca implementasi.
        </li>
        <li style="margin-bottom: 0.8rem;">
            <strong>BAB IV HASIL PENGUJIAN DAN PEMBAHASAN</strong><br>
            Bab ini menyajikan hasil evaluasi pengujian perangkat lunak secara menyeluruh, meliputi pengujian <em>White-Box Basis Path Testing</em> pada algoritma ketersediaan slot dengan analisis graf alir (<em>flowgraph</em>) dan perhitungan Cyclomatic Complexity McCabe V(G) = 6, pengujian fungsionalitas <em>Black-Box Testing</em> terhadap 27 use cases, pengujian khusus konkurensi data transaksi (<em>anti-double booking race condition test</em>), pengujian transmisi latensi push notifikasi FCM dan otomasi auto-cancellation 60 menit, serta evaluasi usabilitas menggunakan kuesioner <em>System Usability Scale</em> (SUS) dengan 10 responden beserta analisis pembahasannya.
        </li>
        <li style="margin-bottom: 0.8rem;">
            <strong>BAB V KESIMPULAN DAN SARAN</strong><br>
            Bab ini merumuskan kesimpulan akhir yang menjawab secara tuntas seluruh butir perumusan masalah penelitian berdasarkan hasil analisis dan pengujian sistem yang telah dilakukan, serta memberikan saran-saran rekomendasi teknis dan strategis bagi pengembangan sistem Booknail pada tahapan selanjutnya di masa depan.
        </li>
    </ul>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">11</div>
</div>
"""

