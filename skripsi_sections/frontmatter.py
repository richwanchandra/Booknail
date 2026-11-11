# skripsi_sections/frontmatter.py
# Bagian Awal Skripsi Booknail FTI UNTAR

def get_frontmatter():
    return """
<!-- ========================================== -->
<!-- COVER / HALAMAN JUDUL LUAR -->
<!-- ========================================== -->
<div class="cover-page">
    <div style="text-align: center;">
        <svg width="130" height="130" viewBox="0 0 100 100" style="margin-bottom: 15px;">
            <circle cx="50" cy="50" r="46" fill="none" stroke="#b91c1c" stroke-width="4"/>
            <polygon points="50,12 85,75 15,75" fill="none" stroke="#b91c1c" stroke-width="3"/>
            <circle cx="50" cy="54" r="16" fill="#b91c1c"/>
            <text x="50" y="58" font-family="'Times New Roman', serif" font-size="11" font-weight="bold" fill="#ffffff" text-anchor="middle">UNTAR</text>
            <text x="50" y="88" font-family="'Times New Roman', serif" font-size="7.5" font-weight="bold" fill="#b91c1c" text-anchor="middle">UNIVERSITAS TARUMANAGARA</text>
        </svg>
        <div style="font-size: 16pt; font-weight: bold; letter-spacing: 2px; margin-top: 5px;">SKRIPSI</div>
    </div>

    <div class="cover-title">
        JUDUL:<br>
        PERANCANGAN SISTEM INFORMASI RESERVASI LAYANAN NAIL ART BERBASIS WEB PADA NAIL GLAM STUDIO DENGAN NOTIFIKASI GOOGLE FIREBASE CLOUD MESSAGING DAN ALOKASI SLOT DINAMIS
    </div>

    <div class="cover-author">
        Disusun oleh:<br><br>
        <span style="font-size: 13pt; text-decoration: underline;">LEON FERNANDO WIJAYA</span><br>
        NIM. 825210088
    </div>

    <div class="cover-footer">
        PROGRAM STUDI SISTEM INFORMASI<br>
        FAKULTAS TEKNOLOGI INFORMASI<br>
        UNIVERSITAS TARUMANAGARA<br>
        JAKARTA<br>
        2026
    </div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- HALAMAN JUDUL DALAM -->
<!-- ========================================== -->
<div class="cover-page">
    <div style="text-align: center;">
        <svg width="110" height="110" viewBox="0 0 100 100" style="margin-bottom: 12px;">
            <circle cx="50" cy="50" r="46" fill="none" stroke="#1e293b" stroke-width="3"/>
            <polygon points="50,14 83,73 17,73" fill="none" stroke="#1e293b" stroke-width="2.5"/>
            <circle cx="50" cy="54" r="15" fill="#1e293b"/>
            <text x="50" y="58" font-family="'Times New Roman', serif" font-size="10" font-weight="bold" fill="#ffffff" text-anchor="middle">UNTAR</text>
        </svg>
        <div style="font-size: 14pt; font-weight: bold; letter-spacing: 1.5px;">SKRIPSI</div>
    </div>

    <div class="cover-title" style="font-size: 13.5pt;">
        PERANCANGAN SISTEM INFORMASI RESERVASI LAYANAN NAIL ART BERBASIS WEB PADA NAIL GLAM STUDIO DENGAN NOTIFIKASI GOOGLE FIREBASE CLOUD MESSAGING DAN ALOKASI SLOT DINAMIS<br><br>
        <span style="font-size: 11pt; font-weight: normal; text-transform: none; font-style: italic;">
            Diajukan sebagai salah satu syarat untuk memperoleh gelar Sarjana Komputer pada Program Studi Sistem Informasi
        </span>
    </div>

    <div class="cover-author">
        Disusun oleh:<br><br>
        <span style="font-size: 12pt; text-decoration: underline;">LEON FERNANDO WIJAYA</span><br>
        NIM. 825210088
    </div>

    <div class="cover-footer" style="font-size: 12pt;">
        PROGRAM STUDI SISTEM INFORMASI<br>
        FAKULTAS TEKNOLOGI INFORMASI<br>
        UNIVERSITAS TARUMANAGARA<br>
        JAKARTA<br>
        2026
    </div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- LEMBAR PERSETUJUAN SIDANG SKRIPSI -->
<!-- ========================================== -->
<div style="padding-top: 1cm;">
    <h1 class="chapter-title" style="margin-bottom: 2.5rem;">LEMBAR PERSETUJUAN SKRIPSI</h1>

    <table style="width: 100%; font-size: 12pt; line-height: 1.8; margin-bottom: 2rem;">
        <tr>
            <td style="width: 25%; vertical-align: top;">Nama</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 72%; font-weight: bold;">LEON FERNANDO WIJAYA</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">NIM</td>
            <td style="vertical-align: top;">:</td>
            <td>825210088</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Program Studi</td>
            <td style="vertical-align: top;">:</td>
            <td>SISTEM INFORMASI</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Fakultas</td>
            <td style="vertical-align: top;">:</td>
            <td>Teknologi Informasi</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Judul Skripsi</td>
            <td style="vertical-align: top;">:</td>
            <td style="text-align: justify; font-weight: bold;">
                Perancangan Sistem Informasi Reservasi Layanan Nail Art Berbasis Web Pada Nail Glam Studio Dengan Notifikasi Google Firebase Cloud Messaging Dan Alokasi Slot Dinamis
            </td>
        </tr>
    </table>

    <p class="no-indent" style="text-align: center; margin: 3rem 0 2rem 0; font-weight: bold;">
        Skripsi ini telah diperiksa dan disetujui untuk diujikan pada Sidang Skripsi.
    </p>

    <div style="margin-top: 4rem; text-align: right; padding-right: 2cm;">
        <p class="no-indent" style="text-align: right; margin-bottom: 0.5rem;">Jakarta, 15 Januari 2026</p>
        <p class="no-indent" style="text-align: right; margin-bottom: 4.5rem;">Pembimbing,</p>
        <div style="font-family: 'Brush Script MT', cursive; font-size: 20pt; text-align: right; margin-right: 1.5cm; color: #1e3a8a;">Tony</div>
        <p class="no-indent" style="text-align: right; font-weight: bold; text-decoration: underline; margin-bottom: 2px;">
            TONY, S.Kom., M.Kom., Ph.D.
        </p>
        <p class="no-indent" style="text-align: right;">NIK/NIP: 10805005</p>
    </div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- LEMBAR PENGESAHAN SKRIPSI -->
<!-- ========================================== -->
<div style="padding-top: 1cm;">
    <h1 class="chapter-title" style="margin-bottom: 2rem;">LEMBAR PENGESAHAN SKRIPSI</h1>

    <table style="width: 100%; font-size: 11.5pt; line-height: 1.6; margin-bottom: 1.5rem;">
        <tr>
            <td style="width: 25%; vertical-align: top;">Nama</td>
            <td style="width: 3%; vertical-align: top;">:</td>
            <td style="width: 72%; font-weight: bold;">LEON FERNANDO WIJAYA</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">NIM</td>
            <td style="vertical-align: top;">:</td>
            <td>825210088</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Program Studi</td>
            <td style="vertical-align: top;">:</td>
            <td>SISTEM INFORMASI</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Judul Skripsi</td>
            <td style="vertical-align: top;">:</td>
            <td style="text-align: justify; font-weight: bold;">
                Perancangan Sistem Informasi Reservasi Layanan Nail Art Berbasis Web Pada Nail Glam Studio Dengan Notifikasi Google Firebase Cloud Messaging Dan Alokasi Slot Dinamis
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; font-style: italic;">Title</td>
            <td style="vertical-align: top;">:</td>
            <td style="text-align: justify; font-style: italic;">
                Design of A Web-Based Nail Art Service Reservation Information System at Nail Glam Studio with Google Firebase Cloud Messaging Notifications and Dynamic Slot Allocation
            </td>
        </tr>
    </table>

    <p class="no-indent" style="text-align: justify; margin-bottom: 1.5rem;">
        Skripsi ini telah dipertahankan di hadapan Dewan Penguji Program Studi SISTEM INFORMASI Fakultas Teknologi Informasi Universitas Tarumanagara pada tanggal 28 Januari 2026 dan dinyatakan: <strong>LULUS</strong>.
    </p>

    <div style="margin: 1.5rem 0;">
        <p class="no-indent" style="font-weight: bold; margin-bottom: 0.8rem;">Dewan Penguji:</p>
        <table style="width: 100%; font-size: 11pt; line-height: 1.6;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    1. Ketua Penguji:<br><br><br>
                    <div style="font-family: 'Brush Script MT', cursive; font-size: 18pt; color: #1e3a8a;">Tony</div>
                    <strong><u>TONY, S.Kom., M.Kom., Ph.D.</u></strong><br>
                    NIK/NIP: 10805005
                </td>
                <td style="width: 50%; vertical-align: top;">
                    2. Anggota Penguji 1:<br><br><br>
                    <div style="font-family: 'Brush Script MT', cursive; font-size: 18pt; color: #1e3a8a;">Dedi Trisnawarman</div>
                    <strong><u>Dr. DEDI TRISNAWARMAN, S.Si., M.Kom.</u></strong><br>
                    NIK/NIP: 10802001
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 1.5rem; vertical-align: top;">
                    3. Anggota Penguji 2:<br><br><br>
                    <div style="font-family: 'Brush Script MT', cursive; font-size: 18pt; color: #1e3a8a;">Wasino</div>
                    <strong><u>WASINO, S.Kom., M.Kom.</u></strong><br>
                    NIK/NIP: 10806008
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 2rem; text-align: center;">
        <p class="no-indent" style="margin-bottom: 0.5rem;">Mengetahui,</p>
        <p class="no-indent" style="margin-bottom: 3.5rem;">Ketua Program Studi Sistem Informasi</p>
        <div style="font-family: 'Brush Script MT', cursive; font-size: 18pt; color: #1e3a8a;">Dedi Trisnawarman</div>
        <p class="no-indent" style="font-weight: bold; text-decoration: underline;">Dr. DEDI TRISNAWARMAN, S.Si., M.Kom.</p>
        <p class="no-indent">NIK/NIP: 10802001</p>
    </div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- LEMBAR PERNYATAAN KEASLIAN / ORISINALITAS -->
<!-- ========================================== -->
<div style="padding-top: 1cm;">
    <h1 class="chapter-title" style="margin-bottom: 2.5rem;">LEMBAR PERNYATAAN KEASLIAN SKRIPSI</h1>

    <table style="width: 100%; font-size: 12pt; line-height: 1.8; margin-bottom: 1.5rem;">
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 3%;">:</td>
            <td style="width: 72%; font-weight: bold;">LEON FERNANDO WIJAYA</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>:</td>
            <td>825210088</td>
        </tr>
        <tr>
            <td>Program Studi</td>
            <td>:</td>
            <td>SISTEM INFORMASI</td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td>Teknologi Informasi, Universitas Tarumanagara</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Judul Skripsi</td>
            <td style="vertical-align: top;">:</td>
            <td style="text-align: justify; font-weight: bold;">
                Perancangan Sistem Informasi Reservasi Layanan Nail Art Berbasis Web Pada Nail Glam Studio Dengan Notifikasi Google Firebase Cloud Messaging Dan Alokasi Slot Dinamis
            </td>
        </tr>
    </table>

    <p style="margin-top: 1.5rem;">
        Dengan ini saya menyatakan bahwa skripsi yang saya susun ini merupakan hasil karya ilmiah mandiri saya sendiri di bawah bimbingan Dosen Pembimbing yang telah ditugaskan, dan bukan merupakan plagiasi, tiruan, atau saduran dari karya orang lain baik sebagian maupun keseluruhan, kecuali bagian-bagian kutipan yang telah disebutkan sumber rujukannya secara resmi sesuai kaidah sitasi akademik IEEE.
    </p>
    <p>
        Apabila di kemudian hari terbukti atau dapat dibuktikan bahwa skripsi ini merupakan hasil jiplakan/plagiarisme atau kegiatan curang lainnya, maka saya bersedia menerima sanksi akademik yang berlaku di Universitas Tarumanagara, termasuk pencabutan gelar sarjana yang telah dianugerahkan kepada saya.
    </p>
    <p>
        Demikian lembar pernyataan keaslian ini saya buat dengan sadar, penuh tanggung jawab, dan tanpa ada paksaan dari pihak manapun untuk dipergunakan sebagaimana mestinya.
    </p>

    <div style="margin-top: 3.5rem; float: right; width: 60%; text-align: right; padding-right: 1.5cm;">
        <p class="no-indent" style="text-align: right; margin-bottom: 0.8rem;">Jakarta, 15 Januari 2026</p>
        <p class="no-indent" style="text-align: right; margin-bottom: 1.2rem;">Yang membuat pernyataan,</p>
        
        <div style="display: inline-block; border: 1.5px dashed #94a3b8; padding: 10px 14px; text-align: center; font-size: 8pt; color: #64748b; margin-bottom: 1.2rem; background: #fafafa;">
            METERAI TEMPEL<br>
            TETAP BERLAKU<br>
            <strong>Rp 10.000,-</strong>
        </div>
        <br>
        <div style="font-family: 'Brush Script MT', cursive; font-size: 20pt; text-align: right; color: #0f172a; margin-right: 1cm;">Leon F. Wijaya</div>
        <p class="no-indent" style="text-align: right; font-weight: bold; text-decoration: underline; margin-bottom: 2px;">
            (LEON FERNANDO WIJAYA)
        </p>
        <p class="no-indent" style="text-align: right;">NIM. 825210088</p>
    </div>
    <div style="clear: both;"></div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- ABSTRAK BAHASA INDONESIA -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">ABSTRAK</h1>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 0.8rem;">
        Nail Glam Studio merupakan bisnis studio estetika kecantikan kuku (<em>nail art</em>) independen di kawasan Jakarta Barat yang melayani 25 hingga 40 transaksi reservasi setiap hari dengan kapasitas 4 meja pengerjaan (<em>station</em>) dan 4 teknisi (<em>nail artist</em>). Karakteristik mendasar dari layanan <em>nail art</em> adalah rentang durasi pengerjaan yang dinamis dan bervariasi bergantung pada kombinasi layanan yang dipilih pelanggan, mulai dari Express Manicure (30 menit) hingga Complex 3D Nail Extension (150 menit). Saat ini, operasional reservasi masih mengandalkan pencatatan manual via percakapan aplikasi pesan instan WhatsApp, yang memicu berbagai kelemahan krusial: staf admin menghabiskan rata-rata 18 menit per pelanggan untuk mencocokkan jadwal secara manual, tingginya insiden tumpang tindih jadwal (<em>double booking</em>) hingga 14,3% dari total pemesanan mingguan, ketidakpastian antrean bagi pelanggan <em>walk-in</em>, serta ketiadaan sistem pelaporan omzet dan utilisasi terapis yang akurat.
    </p>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 0.8rem;">
        Untuk mengatasi permasalahan tersebut, dikembangkan sistem informasi reservasi layanan <em>nail art</em> berbasis web (Booknail) dengan memanfaatkan bahasa pemrograman PHP 8.2, kerangka kerja Laravel 12 dengan pola arsitektur <em>Model-View-Controller</em> (MVC), antarmuka responsif berbasis Tailwind CSS, serta basis data relasional MySQL. Sistem menerapkan algoritma kuantisasi slot waktu dinamis (<em>dynamic time-slot allocation</em>) dengan presisi interval 30 menit yang menghitung ketersediaan slot secara kumulatif berdasarkan total durasi layanan terpilih, jam shift kerja artis, dan status cuti teknisi. Penanganan konkurensi data transaksi guna mengeliminasi <em>double booking</em> diimplementasikan menggunakan teknik <em>pessimistic locking</em> (<code>lockForUpdate</code>) di dalam blok transaksi ACID. Subsistem komunikasi dan peringatan operasional dibangun secara mandiri menggunakan <em>Google Firebase Cloud Messaging</em> (FCM) dan <em>Service Worker</em> untuk mendorong <em>push notifications</em> instan ke peramban pelanggan dan <em>dashboard</em> teknisi tanpa ketergantungan pada gerbang API WhatsApp pihak ketiga. Sistem juga dilengkapi otomasi pembatalan reservasi kadaluarsa 60 menit melalui <em>Task Scheduler</em> dan modul ekspor laporan admin berformat PDF dan CSV (Excel).
    </p>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 1.2rem;">
        Pengujian internal dilakukan melalui metode <em>White-Box Basis Path Testing</em> pada fungsi penentuan ketersediaan slot (<code>SlotAvailabilityService</code>) dengan kompleksitas siklomatis McCabe V(G) = 6 yang menghasilkan status 100% lulus untuk 6 jalur independen. Pengujian fungsionalitas sistem melalui <em>Black-Box Testing</em> terhadap 27 <em>use cases</em> membuktikan seluruh skenario berjalan valid. Uji stres konkurensi dengan jeda 15 milidetik membuktikan integritas basis data terjaga dan mencegah pemesanan ganda. Pengujian transmisi notifikasi FCM mencatat latensi rata-rata &le; 450 milidetik. Evaluasi usabilitas menggunakan kuesioner <em>System Usability Scale</em> (SUS) terhadap 10 responden (1 pengelola/admin, 3 nail artist, dan 6 pelanggan) memperoleh skor rata-rata 82,50, yang tergolong dalam <em>Grade A</em> (kategori <em>Excellent</em>) dengan tingkat penerimaan <em>Acceptable</em>. Sistem Booknail terbukti memangkas waktu pemrosesan pemesanan menjadi kurang dari 2 menit dan meniadakan kasus <em>double booking</em> secara menyeluruh.
    </p>

    <p class="no-indent" style="font-size: 11pt; line-height: 1.4;">
        <strong>Kata Kunci:</strong> Reservasi Layanan, Nail Art, Dynamic Time-Slot, Firebase Cloud Messaging, System Usability Scale.
    </p>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">vi</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- ABSTRACT (ENGLISH) -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">ABSTRACT</h1>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 0.8rem; font-style: italic;">
        Nail Glam Studio is an independent nail art and beauty aesthetics studio located in West Jakarta, handling 25 to 40 reservation transactions daily across 4 service stations and 4 nail technicians (artists). A core characteristic of nail art services is their dynamic duration, which varies significantly depending on the combined treatments selected by the client, ranging from Express Manicure (30 minutes) to Complex 3D Nail Extension (150 minutes). Currently, reservation management relies entirely on manual chat interactions via WhatsApp, leading to severe operational bottlenecks: administrative staff spend an average of 18 minutes per booking matching technician schedules manually, double-booking incidents occur in 14.3% of weekly bookings, walk-in customers face unpredictable wait times, and comprehensive business reporting on revenue and technician workload remains unattainable.
    </p>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 0.8rem; font-style: italic;">
        To resolve these challenges, a web-based nail art service reservation information system (Booknail) was developed using PHP 8.2, Laravel 12 framework under the Model-View-Controller (MVC) architectural pattern, Tailwind CSS responsive frontend, and MySQL relational database. The system introduces a dynamic time-slot allocation algorithm with 30-minute interval quantization that cumulatively calculates slot availability based on total selected service duration, technician shift schedules, and approved leave records. Transaction concurrency control to eliminate double-booking is enforced via database row-level pessimistic locking (<code>lockForUpdate</code>) within atomic ACID database transactions. Operational alerting is engineered independently using Google Firebase Cloud Messaging (FCM) and Web Push Service Workers to deliver instant browser notifications to clients and real-time pop-up alerts to technician dashboards, completely free from third-party WhatsApp gateway dependencies. An automated 60-minute cancellation cron job releases unconfirmed slots, and an administrative reporting module generates exports in PDF and CSV (Excel) formats.
    </p>

    <p style="font-size: 11pt; line-height: 1.45; text-align: justify; margin-bottom: 1.2rem; font-style: italic;">
        Internal verification was conducted through White-Box Basis Path Testing on the slot availability logic (<code>SlotAvailabilityService</code>), yielding a McCabe Cyclomatic Complexity of V(G) = 6 and 100% pass across all 6 independent execution paths. Functional verification using Black-Box Testing across all 27 use cases verified 100% test validity. Concurrency stress testing with 15-millisecond race windows confirmed data integrity by successfully rejecting overlapping transactions. FCM push notifications achieved an average dispatch latency of &le; 450 ms. Usability evaluation using the System Usability Scale (SUS) with 10 participants (1 administrator, 3 nail artists, and 6 customers) yielded a mean score of 82.50, corresponding to Grade A (Excellent) with Acceptable status. Booknail successfully reduced booking processing time to under 2 minutes and eliminated double-booking anomalies completely.
    </p>

    <p class="no-indent" style="font-size: 11pt; line-height: 1.4;">
        <strong>Keywords:</strong> Service Reservation, Nail Art, Dynamic Time-Slot, Firebase Cloud Messaging, System Usability Scale.
    </p>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">vii</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- KATA PENGANTAR -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">KATA PENGANTAR</h1>

    <p>
        Puji dan syukur penulis panjatkan ke hadirat Tuhan Yang Maha Esa atas berkat, rahmat, dan karunia-Nya yang senantiasa melimpah sehingga penulis dapat menyelesaikan penyusunan skripsi yang berjudul <strong>"Perancangan Sistem Informasi Reservasi Layanan Nail Art Berbasis Web Pada Nail Glam Studio Dengan Notifikasi Google Firebase Cloud Messaging Dan Alokasi Slot Dinamis"</strong> dengan lancar dan tepat pada waktunya. Skripsi ini disusun sebagai salah satu syarat akademis guna menyelesaikan studi dan memperoleh gelar Sarjana Komputer (S.Kom.) pada Program Studi Sistem Informasi, Fakultas Teknologi Informasi, Universitas Tarumanagara, Jakarta.
    </p>
    <p>
        Penulis menyadari sepenuhnya bahwa keberhasilan penyusunan skripsi ini tidak lepas dari bimbingan, dorongan semangat, masukan, dan doa dari berbagai pihak. Oleh karena itu, pada kesempatan ini penulis ingin menyampaikan rasa terima kasih dan apresiasi yang setinggi-tingginya kepada:
    </p>

    <ol class="academic-list" style="line-height: 1.5;">
        <li><strong>Prof. Dr. Ir. Dyah Erny Herwindiati, M.Si.</strong>, selaku Dekan Fakultas Teknologi Informasi Universitas Tarumanagara.</li>
        <li><strong>Bapak Dr. Dedi Trisnawarman, S.Si., M.Kom.</strong>, selaku Ketua Program Studi Sistem Informasi Fakultas Teknologi Informasi Universitas Tarumanagara.</li>
        <li><strong>Bapak Novario Jaya Perdana, S.Kom., M.T.</strong>, selaku Sekretaris Program Studi Sistem Informasi Universitas Tarumanagara.</li>
        <li><strong>Bapak Wasino, S.Kom., M.Kom.</strong>, selaku Koordinator Skripsi Program Studi Sistem Informasi Fakultas Teknologi Informasi Universitas Tarumanagara.</li>
        <li><strong>Bapak Tony, S.Kom., M.Kom., Ph.D.</strong>, selaku Dosen Pembimbing Skripsi, yang telah dengan penuh dedikasi meluangkan waktu, memberikan bimbingan teknis yang mendalam, arahan arsitektur perangkat lunak, serta motivasi yang berharga kepada penulis sejak awal hingga penyelesaian skripsi ini.</li>
        <li><strong>Ibu Cindy Octavia</strong>, selaku pemilik pengelola Nail Glam Studio, yang telah memberikan izin penelitian, data operasional, wawancara mendalam, serta kesempatan bagi penulis untuk mengimplementasikan dan menguji sistem secara langsung di lingkungan studio.</li>
        <li>Seluruh staf teknisi (<em>nail artist</em>) dan pelanggan Nail Glam Studio yang telah berpartisipasi aktif dalam sesi wawancara, uji coba fungsionalitas, serta pengisian kuesioner <em>System Usability Scale</em> (SUS).</li>
        <li>Kedua orang tua tercinta dan keluarga besar penulis yang senantiasa melimpahkan doa restu, kasih sayang, dan dukungan moral maupun materiil yang tak terhingga.</li>
        <li>Rekan-rekan mahasiswa Program Studi Sistem Informasi angkatan 2021 Universitas Tarumanagara atas kebersamaan dan diskusinya selama masa perkuliahan.</li>
    </ol>

    <p>
        Penulis menyadari bahwa skripsi ini masih memiliki keterbatasan. Oleh karena itu, kritik dan saran yang konstruktif dari pembaca sangat diharapkan demi penyempurnaan karya ilmiah di masa mendatang. Akhir kata, penulis berharap skripsi ini dapat memberikan kontribusi nyata dan referensi bermanfaat bagi pengembangan ilmu sistem informasi.
    </p>

    <div style="margin-top: 2rem; float: right; width: 50%; text-align: right; padding-right: 1.5cm;">
        <p class="no-indent" style="text-align: right; margin-bottom: 0.3rem;">Jakarta, 15 Januari 2026</p>
        <p class="no-indent" style="text-align: right; margin-bottom: 3.5rem;">Penulis,</p>
        <div style="font-family: 'Brush Script MT', cursive; font-size: 20pt; text-align: right; color: #0f172a; margin-right: 1cm;">Leon F. Wijaya</div>
        <p class="no-indent" style="text-align: right; font-weight: bold; text-decoration: underline; margin-bottom: 2px;">
            (LEON FERNANDO WIJAYA)
        </p>
        <p class="no-indent" style="text-align: right;">NIM. 825210088</p>
    </div>
    <div style="clear: both;"></div>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">viii</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- DAFTAR ISI -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">DAFTAR ISI</h1>

    <table style="width: 100%; font-size: 11pt; line-height: 1.5; border-collapse: collapse;">
        <tr><td><strong>HALAMAN JUDUL LUAR</strong></td><td style="text-align: right;"><strong>i</strong></td></tr>
        <tr><td><strong>HALAMAN JUDUL DALAM</strong></td><td style="text-align: right;"><strong>ii</strong></td></tr>
        <tr><td><strong>LEMBAR PERSETUJUAN SKRIPSI</strong></td><td style="text-align: right;"><strong>iii</strong></td></tr>
        <tr><td><strong>LEMBAR PENGESAHAN SKRIPSI</strong></td><td style="text-align: right;"><strong>iv</strong></td></tr>
        <tr><td><strong>LEMBAR PERNYATAAN KEASLIAN</strong></td><td style="text-align: right;"><strong>v</strong></td></tr>
        <tr><td><strong>ABSTRAK</strong></td><td style="text-align: right;"><strong>vi</strong></td></tr>
        <tr><td><strong>ABSTRACT</strong></td><td style="text-align: right;"><strong>vii</strong></td></tr>
        <tr><td><strong>KATA PENGANTAR</strong></td><td style="text-align: right;"><strong>viii</strong></td></tr>
        <tr><td><strong>DAFTAR ISI</strong></td><td style="text-align: right;"><strong>ix</strong></td></tr>
        <tr><td><strong>DAFTAR TABEL</strong></td><td style="text-align: right;"><strong>xii</strong></td></tr>
        <tr><td><strong>DAFTAR GAMBAR</strong></td><td style="text-align: right;"><strong>xiv</strong></td></tr>
        <tr><td><strong>DAFTAR LAMPIRAN</strong></td><td style="text-align: right;"><strong>xvi</strong></td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>BAB I PENDAHULUAN</strong></td><td style="text-align: right;"><strong>1</strong></td></tr>
        <tr><td style="padding-left: 20px;">1.1 Latar Belakang Masalah</td><td style="text-align: right;">1</td></tr>
        <tr><td style="padding-left: 20px;">1.2 Perumusan Masalah</td><td style="text-align: right;">4</td></tr>
        <tr><td style="padding-left: 20px;">1.3 Batasan Masalah</td><td style="text-align: right;">5</td></tr>
        <tr><td style="padding-left: 20px;">1.4 Tujuan Penelitian</td><td style="text-align: right;">6</td></tr>
        <tr><td style="padding-left: 20px;">1.5 Manfaat Penelitian</td><td style="text-align: right;">6</td></tr>
        <tr><td style="padding-left: 20px;">1.6 Penelitian Terdahulu</td><td style="text-align: right;">7</td></tr>
        <tr><td style="padding-left: 20px;">1.7 Sistematika Penulisan</td><td style="text-align: right;">10</td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>BAB II DASAR TEORI, METODOLOGI DAN PERANCANGAN</strong></td><td style="text-align: right;"><strong>12</strong></td></tr>
        <tr><td style="padding-left: 20px;">2.1 Teori Dasar dan Khusus</td><td style="text-align: right;">12</td></tr>
        <tr><td style="padding-left: 40px;">2.1.1 Sistem Informasi dan Reservasi Layanan</td><td style="text-align: right;">12</td></tr>
        <tr><td style="padding-left: 40px;">2.1.2 Nail Art dan Manajemen Waktu Layanan</td><td style="text-align: right;">13</td></tr>
        <tr><td style="padding-left: 40px;">2.1.3 Kuantisasi Slot Waktu Dinamis (Dynamic Time-Slot)</td><td style="text-align: right;">13</td></tr>
        <tr><td style="padding-left: 40px;">2.1.4 Transaksi ACID dan Pessimistic Locking (lockForUpdate)</td><td style="text-align: right;">14</td></tr>
        <tr><td style="padding-left: 40px;">2.1.5 Google Firebase Cloud Messaging (FCM) & Web Push</td><td style="text-align: right;">15</td></tr>
        <tr><td style="padding-left: 40px;">2.1.6 Framework Laravel 12 dan Arsitektur MVC</td><td style="text-align: right;">16</td></tr>
        <tr><td style="padding-left: 40px;">2.1.7 Tailwind CSS dan Desain Responsif</td><td style="text-align: right;">17</td></tr>
        <tr><td style="padding-left: 40px;">2.1.8 RDBMS MySQL</td><td style="text-align: right;">17</td></tr>
        <tr><td style="padding-left: 40px;">2.1.9 Unified Modeling Language (UML)</td><td style="text-align: right;">18</td></tr>
        <tr><td style="padding-left: 40px;">2.1.10 White-Box Testing dan Basis Path Testing</td><td style="text-align: right;">19</td></tr>
        <tr><td style="padding-left: 40px;">2.1.11 Black-Box Testing</td><td style="text-align: right;">20</td></tr>
        <tr><td style="padding-left: 40px;">2.1.12 System Usability Scale (SUS)</td><td style="text-align: right;">20</td></tr>
        <tr><td style="padding-left: 20px;">2.2 Objek Penelitian dan Analisis Sistem Berjalan</td><td style="text-align: right;">21</td></tr>
        <tr><td style="padding-left: 40px;">2.2.1 Profil Nail Glam Studio</td><td style="text-align: right;">21</td></tr>
        <tr><td style="padding-left: 40px;">2.2.2 Analisis Sistem Reservasi Berjalan</td><td style="text-align: right;">22</td></tr>
        <tr><td style="padding-left: 40px;">2.2.3 Flowchart Proses Bisnis Berjalan</td><td style="text-align: right;">23</td></tr>
        <tr><td style="padding-left: 40px;">2.2.4 Evaluasi Kelemahan Sistem Berjalan</td><td style="text-align: right;">24</td></tr>
        <tr><td style="padding-left: 20px;">2.3 Perancangan Proses (UML)</td><td style="text-align: right;">25</td></tr>
        <tr><td style="padding-left: 40px;">2.3.1 Use Case Diagram (27 Use Cases)</td><td style="text-align: right;">25</td></tr>
        <tr><td style="padding-left: 40px;">2.3.2 Skenario Use Case Rinci</td><td style="text-align: right;">27</td></tr>
        <tr><td style="padding-left: 40px;">2.3.3 Activity Diagram Swimlane</td><td style="text-align: right;">32</td></tr>
        <tr><td style="padding-left: 40px;">2.3.4 Sequence Diagram</td><td style="text-align: right;">36</td></tr>
        <tr><td style="padding-left: 20px;">2.4 Perancangan Basis Data</td><td style="text-align: right;">40</td></tr>
        <tr><td style="padding-left: 40px;">2.4.1 Conceptual Data Model (CDM) - Mermaid Code</td><td style="text-align: right;">40</td></tr>
        <tr><td style="padding-left: 40px;">2.4.2 Logical Data Model (LDM) - Mermaid Code</td><td style="text-align: right;">42</td></tr>
        <tr><td style="padding-left: 40px;">2.4.3 Physical Data Model (PDM) - Mermaid Code</td><td style="text-align: right;">44</td></tr>
        <tr><td style="padding-left: 40px;">2.4.4 Kamus Data dan Spesifikasi Tabel Basis Data</td><td style="text-align: right;">46</td></tr>
        <tr><td style="padding-left: 40px;">2.4.5 Class Diagram - PlantUML Code</td><td style="text-align: right;">50</td></tr>
        <tr><td style="padding-left: 20px;">2.5 Perancangan Antarmuka Sistem</td><td style="text-align: right;">52</td></tr>
        <tr><td style="padding-left: 40px;">2.5.1 Windows Navigation Diagram (WND)</td><td style="text-align: right;">52</td></tr>
        <tr><td style="padding-left: 40px;">2.5.2 Perancangan Mockup Antarmuka Pengguna</td><td style="text-align: right;">54</td></tr>
        <tr><td style="padding-left: 20px;">2.6 Perancangan Implementasi Kode</td><td style="text-align: right;">59</td></tr>
        <tr><td style="padding-left: 40px;">2.6.1 Struktur Direktori MVC Laravel 12</td><td style="text-align: right;">59</td></tr>
        <tr><td style="padding-left: 40px;">2.6.2 Algoritma Alokasi Waktu Dinamis</td><td style="text-align: right;">60</td></tr>
        <tr><td style="padding-left: 40px;">2.6.3 Algoritma Penguncian Pesimistik Anti-Double Booking</td><td style="text-align: right;">61</td></tr>
        <tr><td style="padding-left: 40px;">2.6.4 Arsitektur Notifikasi Google Firebase Cloud Messaging</td><td style="text-align: right;">62</td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>BAB III PEMBUATAN DAN RENCANA IMPLEMENTASI</strong></td><td style="text-align: right;"><strong>64</strong></td></tr>
        <tr><td style="padding-left: 20px;">3.1 Tata Laksana Metode Waterfall</td><td style="text-align: right;">64</td></tr>
        <tr><td style="padding-left: 20px;">3.2 Kebutuhan Implementasi Sistem</td><td style="text-align: right;">66</td></tr>
        <tr><td style="padding-left: 40px;">3.2.1 Topologi Jaringan dan Arsitektur Lingkungan</td><td style="text-align: right;">66</td></tr>
        <tr><td style="padding-left: 40px;">3.2.2 Kebutuhan Perangkat Keras (Hardware)</td><td style="text-align: right;">67</td></tr>
        <tr><td style="padding-left: 40px;">3.2.3 Kebutuhan Perangkat Lunak (Software)</td><td style="text-align: right;">68</td></tr>
        <tr><td style="padding-left: 40px;">3.2.4 Personil Pelaksana</td><td style="text-align: right;">68</td></tr>
        <tr><td style="padding-left: 40px;">3.2.5 Prosedur Instalasi dan Konfigurasi SSL/FCM</td><td style="text-align: right;">69</td></tr>
        <tr><td style="padding-left: 20px;">3.3 Rencana Implementasi dan Jadwal Pengembangan (WBS Gantt Chart)</td><td style="text-align: right;">71</td></tr>
        <tr><td style="padding-left: 20px;">3.4 Prosedur Pemeliharaan Sistem</td><td style="text-align: right;">74</td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>BAB IV HASIL PENGUJIAN DAN PEMBAHASAN</strong></td><td style="text-align: right;"><strong>76</strong></td></tr>
        <tr><td style="padding-left: 20px;">4.1 Pengujian White-Box (Basis Path Testing McCabe)</td><td style="text-align: right;">76</td></tr>
        <tr><td style="padding-left: 20px;">4.2 Pengujian Black-Box (Matriks Uji 27 Use Cases)</td><td style="text-align: right;">81</td></tr>
        <tr><td style="padding-left: 20px;">4.3 Pengujian Konkurensi dan Anti-Double Booking</td><td style="text-align: right;">89</td></tr>
        <tr><td style="padding-left: 20px;">4.4 Pengujian Notifikasi FCM dan Pembatalan Otomatis</td><td style="text-align: right;">92</td></tr>
        <tr><td style="padding-left: 20px;">4.5 Evaluasi Usabilitas (System Usability Scale - SUS)</td><td style="text-align: right;">95</td></tr>
        <tr><td style="padding-left: 20px;">4.6 Pembahasan dan Analisis Hasil</td><td style="text-align: right;">99</td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>BAB V KESIMPULAN DAN SARAN</strong></td><td style="text-align: right;"><strong>102</strong></td></tr>
        <tr><td style="padding-left: 20px;">5.1 Kesimpulan</td><td style="text-align: right;">102</td></tr>
        <tr><td style="padding-left: 20px;">5.2 Saran Pengembangan</td><td style="text-align: right;">104</td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>DAFTAR PUSTAKA (Format IEEE)</strong></td><td style="text-align: right;"><strong>106</strong></td></tr>
        <tr><td colspan="2" style="padding-top: 8px;"><strong>LAMPIRAN</strong></td><td style="text-align: right;"><strong>110</strong></td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 1: Transkrip Wawancara Pemilik Nail Glam Studio</td><td style="text-align: right;">110</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 2: Form Kuesioner System Usability Scale (SUS)</td><td style="text-align: right;">113</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 3: Rekapitulasi Data Mentah dan Perhitungan SUS</td><td style="text-align: right;">115</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 4: Konfigurasi Service Worker Google FCM</td><td style="text-align: right;">117</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 5: Source Code SlotAvailabilityService.php</td><td style="text-align: right;">119</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 6: Source Code FcmService.php</td><td style="text-align: right;">123</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 7: Source Code CancelExpiredBookingsJob.php</td><td style="text-align: right;">126</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 8: Source Code ReportController.php (Export PDF & CSV)</td><td style="text-align: right;">128</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 9: Lembar Berita Acara UAT</td><td style="text-align: right;">132</td></tr>
        <tr><td style="padding-left: 20px;">Lampiran 10: Dokumentasi Observasi & Pengujian</td><td style="text-align: right;">134</td></tr>
    </table>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">x</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- DAFTAR TABEL -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">DAFTAR TABEL</h1>

    <table style="width: 100%; font-size: 11pt; line-height: 1.6; border-collapse: collapse;">
        <tr><td>Tabel 1.1 Matriks Perbandingan Penelitian Terdahulu</td><td style="text-align: right;">8</td></tr>
        <tr><td>Tabel 2.1 Identifikasi Aktor dan Peran dalam Sistem Booknail</td><td style="text-align: right;">26</td></tr>
        <tr><td>Tabel 2.2 Daftar Use Case Sistem Reservasi Booknail</td><td style="text-align: right;">27</td></tr>
        <tr><td>Tabel 2.3 Skenario Use Case UC-04: Membuat Reservasi Multi-Step</td><td style="text-align: right;">28</td></tr>
        <tr><td>Tabel 2.4 Skenario Use Case UC-11: Melihat Poin Loyalitas</td><td style="text-align: right;">29</td></tr>
        <tr><td>Tabel 2.5 Skenario Use Case UC-23: Konfirmasi Pembayaran DP</td><td style="text-align: right;">30</td></tr>
        <tr><td>Tabel 2.6 Skenario Use Case UC-26: Ekspor Rekapitulasi Laporan (PDF & CSV)</td><td style="text-align: right;">31</td></tr>
        <tr><td>Tabel 2.7 Skenario Use Case UC-30: Konfirmasi/Tolak Booking oleh Teknisi</td><td style="text-align: right;">31</td></tr>
        <tr><td>Tabel 2.8 Skenario Use Case UC-41: Pembatalan Otomatis (Auto-Cancel)</td><td style="text-align: right;">32</td></tr>
        <tr><td>Tabel 2.9 Spesifikasi Entitas dan Atribut CDM</td><td style="text-align: right;">41</td></tr>
        <tr><td>Tabel 2.10 Matriks Kardinalitas Relasi Antar-Entitas LDM</td><td style="text-align: right;">43</td></tr>
        <tr><td>Tabel 2.11 Spesifikasi Tabel users</td><td style="text-align: right;">46</td></tr>
        <tr><td>Tabel 2.12 Spesifikasi Tabel nail_artists</td><td style="text-align: right;">46</td></tr>
        <tr><td>Tabel 2.13 Spesifikasi Tabel artist_schedules</td><td style="text-align: right;">47</td></tr>
        <tr><td>Tabel 2.14 Spesifikasi Tabel artist_leaves</td><td style="text-align: right;">47</td></tr>
        <tr><td>Tabel 2.15 Spesifikasi Tabel categories</td><td style="text-align: right;">47</td></tr>
        <tr><td>Tabel 2.16 Spesifikasi Tabel services</td><td style="text-align: right;">48</td></tr>
        <tr><td>Tabel 2.17 Spesifikasi Tabel bookings</td><td style="text-align: right;">48</td></tr>
        <tr><td>Tabel 2.18 Spesifikasi Tabel booking_details</td><td style="text-align: right;">49</td></tr>
        <tr><td>Tabel 2.19 Spesifikasi Tabel payments</td><td style="text-align: right;">49</td></tr>
        <tr><td>Tabel 2.20 Spesifikasi Tabel fcm_tokens</td><td style="text-align: right;">50</td></tr>
        <tr><td>Tabel 3.1 Spesifikasi Kebutuhan Perangkat Keras (Hardware)</td><td style="text-align: right;">67</td></tr>
        <tr><td>Tabel 3.2 Spesifikasi Kebutuhan Perangkat Lunak (Software)</td><td style="text-align: right;">68</td></tr>
        <tr><td>Tabel 3.3 Struktur Pembagian Kerja (WBS) dan Alokasi Personil</td><td style="text-align: right;">69</td></tr>
        <tr><td>Tabel 3.4 Lembar Kerja Spreadsheet Gantt Chart Jadwal Pengembangan (16 Minggu)</td><td style="text-align: right;">72</td></tr>
        <tr><td>Tabel 4.1 Pemetaan Predikat Logika Algoritma Ketersediaan Slot</td><td style="text-align: right;">78</td></tr>
        <tr><td>Tabel 4.2 Matriks Eksekusi 6 Jalur Independen White-Box Testing</td><td style="text-align: right;">80</td></tr>
        <tr><td>Tabel 4.3 Matriks Pengujian Black-Box Group 1: Pelanggan (UC-01 s/d UC-11)</td><td style="text-align: right;">82</td></tr>
        <tr><td>Tabel 4.4 Matriks Pengujian Black-Box Group 2: Admin/Manager (UC-20 s/d UC-28)</td><td style="text-align: right;">85</td></tr>
        <tr><td>Tabel 4.5 Matriks Pengujian Black-Box Group 3: Nail Artist (UC-30 s/d UC-33)</td><td style="text-align: right;">87</td></tr>
        <tr><td>Tabel 4.6 Matriks Pengujian Black-Box Group 4: Scheduler (UC-40 s/d UC-42)</td><td style="text-align: right;">88</td></tr>
        <tr><td>Tabel 4.7 Hasil Uji Konkurensi Transaksi Slot Simultan (Race Condition)</td><td style="text-align: right;">91</td></tr>
        <tr><td>Tabel 4.8 Pengujian Latensi Push Notification Google FCM</td><td style="text-align: right;">93</td></tr>
        <tr><td>Tabel 4.9 Hasil Uji Otomasi Pembatalan Reservasi (Auto-Cancel 60 Menit)</td><td style="text-align: right;">94</td></tr>
        <tr><td>Tabel 4.10 Rekapitulasi Profil Responden Uji Usabilitas SUS</td><td style="text-align: right;">96</td></tr>
        <tr><td>Tabel 4.11 Rekapitulasi Skor Mentah dan Nilai SUS 10 Responden (Rata-rata 82,50)</td><td style="text-align: right;">97</td></tr>
        <tr><td>Tabel 4.12 Matriks Pemenuhan Tujuan Penelitian</td><td style="text-align: right;">100</td></tr>
    </table>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">xii</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- DAFTAR GAMBAR -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">DAFTAR GAMBAR</h1>

    <table style="width: 100%; font-size: 11pt; line-height: 1.6; border-collapse: collapse;">
        <tr><td>Gambar 1.1 Tangkapan Layar Percakapan Reservasi Manual via WhatsApp</td><td style="text-align: right;">2</td></tr>
        <tr><td>Gambar 1.2 Contoh Bentrok Jadwal Akibat Pencatatan Konvensional</td><td style="text-align: right;">3</td></tr>
        <tr><td>Gambar 2.1 Diagram Alur Kuantisasi Slot Waktu Dinamis 30 Menit</td><td style="text-align: right;">14</td></tr>
        <tr><td>Gambar 2.2 Mekanisme Transaksi Row-Level Locking (lockForUpdate)</td><td style="text-align: right;">15</td></tr>
        <tr><td>Gambar 2.3 Arsitektur Push Notification Google FCM dan Web Push Service Worker</td><td style="text-align: right;">16</td></tr>
        <tr><td>Gambar 2.4 Struktur Organisasi Nail Glam Studio</td><td style="text-align: right;">22</td></tr>
        <tr><td>Gambar 2.5 Flowchart Proses Bisnis Reservasi Manual yang Sedang Berjalan</td><td style="text-align: right;">23</td></tr>
        <tr><td>Gambar 2.6 Use Case Diagram Sistem Informasi Booknail (27 Use Cases)</td><td style="text-align: right;">26</td></tr>
        <tr><td>Gambar 2.7 Activity Diagram Swimlane: Pemesanan Slot Pelanggan</td><td style="text-align: right;">33</td></tr>
        <tr><td>Gambar 2.8 Activity Diagram Swimlane: Verifikasi Pembayaran DP & Push Notifikasi</td><td style="text-align: right;">34</td></tr>
        <tr><td>Gambar 2.9 Activity Diagram Swimlane: Ekspor Laporan Admin (PDF & CSV)</td><td style="text-align: right;">35</td></tr>
        <tr><td>Gambar 2.10 Activity Diagram Swimlane: Pembatalan Otomatis Scheduler</td><td style="text-align: right;">36</td></tr>
        <tr><td>Gambar 2.11 Sequence Diagram: Reservasi Slot Multi-Step dan Penguncian Pesimistik</td><td style="text-align: right;">37</td></tr>
        <tr><td>Gambar 2.12 Sequence Diagram: Verifikasi DP dan Pemicuan Push Notifikasi FCM</td><td style="text-align: right;">38</td></tr>
        <tr><td>Gambar 2.13 Sequence Diagram: Otomasi Pembatalan Reservasi Expired</td><td style="text-align: right;">39</td></tr>
        <tr><td>Gambar 2.14 Diagram Konseptual Basis Data (CDM) - Mermaid ERD</td><td style="text-align: right;">41</td></tr>
        <tr><td>Gambar 2.15 Diagram Logikal Basis Data (LDM) - Mermaid ERD</td><td style="text-align: right;">43</td></tr>
        <tr><td>Gambar 2.16 Diagram Fisikal Basis Data (PDM) - Mermaid ERD</td><td style="text-align: right;">45</td></tr>
        <tr><td>Gambar 2.17 Class Diagram Sistem Booknail - Model dan Service (PlantUML)</td><td style="text-align: right;">51</td></tr>
        <tr><td>Gambar 2.18 Windows Navigation Diagram (WND) - Area Pelanggan</td><td style="text-align: right;">52</td></tr>
        <tr><td>Gambar 2.19 Windows Navigation Diagram (WND) - Area Nail Artist</td><td style="text-align: right;">53</td></tr>
        <tr><td>Gambar 2.20 Windows Navigation Diagram (WND) - Area Administrator</td><td style="text-align: right;">54</td></tr>
        <tr><td>Gambar 2.21 Mockup Halaman Utama dan Katalog Layanan Responsif</td><td style="text-align: right;">55</td></tr>
        <tr><td>Gambar 2.22 Mockup Wizard Reservasi Step 1: Pemilihan Layanan Kumulatif</td><td style="text-align: right;">55</td></tr>
        <tr><td>Gambar 2.23 Mockup Wizard Reservasi Step 2: Pemilihan Tanggal & Slot Dinamis</td><td style="text-align: right;">56</td></tr>
        <tr><td>Gambar 2.24 Mockup Wizard Reservasi Step 3: Unggah Desain Custom & Catatan</td><td style="text-align: right;">56</td></tr>
        <tr><td>Gambar 2.25 Mockup Wizard Reservasi Step 4: Ringkasan DP & Instruksi Bayar</td><td style="text-align: right;">57</td></tr>
        <tr><td>Gambar 2.26 Mockup Dashboard Nail Artist dengan Pop-up Notifikasi FCM</td><td style="text-align: right;">57</td></tr>
        <tr><td>Gambar 2.27 Mockup Dashboard Admin: Modul Ekspor Laporan PDF & CSV</td><td style="text-align: right;">58</td></tr>
        <tr><td>Gambar 2.28 Visualisasi Dokumen PDF Hasil Ekspor Laporan Reservasi</td><td style="text-align: right;">58</td></tr>
        <tr><td>Gambar 2.29 Struktur Direktori Arsitektur Laravel 12 pada Visual Studio Code</td><td style="text-align: right;">60</td></tr>
        <tr><td>Gambar 3.1 Topologi Jaringan Client-Server dan Integrasi Layanan Google FCM</td><td style="text-align: right;">66</td></tr>
        <tr><td>Gambar 3.2 Tampilan Antarmuka Panel Konfigurasi Firebase Cloud Messaging Console</td><td style="text-align: right;">70</td></tr>
        <tr><td>Gambar 3.3 Visualisasi Timeline Pengembangan Sistem (WBS Spreadsheet)</td><td style="text-align: right;">73</td></tr>
        <tr><td>Gambar 4.1 Flowgraph White-Box Testing Metode check() SlotAvailabilityService</td><td style="text-align: right;">77</td></tr>
        <tr><td>Gambar 4.2 Simulasi Pengujian Konkurensi Slot Menggunakan HTTP Request Simultan</td><td style="text-align: right;">90</td></tr>
        <tr><td>Gambar 4.3 Tangkapan Layar Penolakan Transaksi Kedua (Anti-Double Booking Berhasil)</td><td style="text-align: right;">91</td></tr>
        <tr><td>Gambar 4.4 Tangkapan Layar Web Push Notification FCM pada Browser Desktop</td><td style="text-align: right;">93</td></tr>
        <tr><td>Gambar 4.5 Tangkapan Layar Push Notification FCM pada Mobile Android</td><td style="text-align: right;">94</td></tr>
        <tr><td>Gambar 4.6 Skala Penilaian Adjective Ratings dan Grade Scale SUS (Brooke, 1996)</td><td style="text-align: right;">96</td></tr>
        <tr><td>Gambar 4.7 Grafik Batang Distribusi Skor SUS 10 Responden (Rata-rata 82,50)</td><td style="text-align: right;">98</td></tr>
        <tr><td>Gambar 4.8 Diagram Komparasi Waktu Pemrosesan Reservasi: Sistem Lama vs Booknail</td><td style="text-align: right;">101</td></tr>
    </table>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">xiv</div>
</div>

<div class="page-break"></div>

<!-- ========================================== -->
<!-- DAFTAR LAMPIRAN -->
<!-- ========================================== -->
<div style="padding-top: 0.5cm;">
    <h1 class="chapter-title" style="margin-bottom: 1.5rem;">DAFTAR LAMPIRAN</h1>

    <table style="width: 100%; font-size: 11pt; line-height: 1.8; border-collapse: collapse;">
        <tr><td><strong>Lampiran 1</strong></td><td>Transkrip Wawancara Pemilik Pengelola Nail Glam Studio</td><td style="text-align: right;">110</td></tr>
        <tr><td><strong>Lampiran 2</strong></td><td>Formulir Instrumen Kuesioner System Usability Scale (SUS)</td><td style="text-align: right;">113</td></tr>
        <tr><td><strong>Lampiran 3</strong></td><td>Lembar Rekapitulasi Data Mentah dan Skor SUS 10 Responden</td><td style="text-align: right;">115</td></tr>
        <tr><td><strong>Lampiran 4</strong></td><td>Berkas Konfigurasi Web Push Service Worker (firebase-messaging-sw.js)</td><td style="text-align: right;">117</td></tr>
        <tr><td><strong>Lampiran 5</strong></td><td>Berkas Kode Program SlotAvailabilityService.php</td><td style="text-align: right;">119</td></tr>
        <tr><td><strong>Lampiran 6</strong></td><td>Berkas Kode Program Layanan Notifikasi FcmService.php</td><td style="text-align: right;">123</td></tr>
        <tr><td><strong>Lampiran 7</strong></td><td>Berkas Kode Program Job Pembatalan Otomatis (CancelExpiredBookingsJob.php)</td><td style="text-align: right;">126</td></tr>
        <tr><td><strong>Lampiran 8</strong></td><td>Berkas Kode Program Modul Ekspor Laporan PDF & CSV (ReportController.php)</td><td style="text-align: right;">128</td></tr>
        <tr><td><strong>Lampiran 9</strong></td><td>Lembar Berita Acara Uji Penerimaan Pengguna (User Acceptance Testing - UAT)</td><td style="text-align: right;">132</td></tr>
        <tr><td><strong>Lampiran 10</strong></td><td>Dokumentasi Foto Observasi Studio dan Sesi Pengujian Lapangan</td><td style="text-align: right;">134</td></tr>
    </table>
    <div style="text-align: center; margin-top: 1.5rem; font-size: 11pt;">xvi</div>
</div>
"""

