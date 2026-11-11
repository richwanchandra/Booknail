# skripsi_sections/bab2.py
# BAB II LANDASAN TEORI DAN PERANCANGAN SISTEM
# Skripsi Booknail FTI UNTAR - Leon Fernando Wijaya (825210088)

def get_bab2():
    return r"""
<div class="page-break"></div>

<!-- ========================================== -->
<!-- BAB II LANDASAN TEORI DAN PERANCANGAN SISTEM -->
<!-- ========================================== -->
<h1 class="chapter-title">BAB II<br>LANDASAN TEORI DAN PERANCANGAN SISTEM</h1>

<h2 class="section-title">2.1 Teori Umum dan Teori Khusus</h2>

<h3 class="subsection-title">2.1.1 Sistem Informasi Reservasi Layanan</h3>
<p>
    Sistem informasi reservasi layanan merupakan suatu susunan komponen teknologi terpadu yang bertugas mengumpulkan, menyimpan, memproses, dan mendistribusikan data ketersediaan alokasi sumber daya waktu, tenaga kerja spesialis, serta ruang fasilitas untuk memfasilitasi transaksi pemesanan jasa di masa mendatang [1]. Dalam konteks industri jasa perawatan kecantikan modern, sistem reservasi berperan krusial sebagai jembatan interaksi awal antara penyedia jasa (<em>service provider</em>) dan konsumen akhir. Berbeda dengan transaksi penjualan barang fisik berbasis inventaris statis (<em>SKU-based stock</em>), sistem reservasi jasa beroperasi pada inventaris yang bersifat perishable (dapat musnah jika terlewat). Setiap unit waktu slot kerja yang tidak terisi tidak dapat disimpan atau dijual kembali di kemudian hari, sehingga manajemen waktu yang presisi menjadi penentu utama profitabilitas bisnis studio [2].
</p>
<p>
    Sistem informasi reservasi yang efektif harus memenuhi empat pilar fungsionalitas dasar: transparansi ketersediaan jadwal (<em>schedule transparency</em>), pemesanan mandiri seketika (<em>instant self-service booking</em>), penguncian alokasi anti-bentrok (<em>conflict-free slot locking</em>), serta diseminasi informasi status transaksi secara tepat waktu (<em>timely notification dispatching</em>) [3]. Integrasi keempat pilar ini secara digital memangkas hambatan komunikasi konvensional, mengeliminasi friksi negosiasi jadwal manual, dan memberikan kepastian layanan bagi kedua belah pihak.
</p>

<h3 class="subsection-title">2.1.2 Karakteristik Operasional Bisnis Nail Art & Multi-Treatment</h3>
<p>
    Layanan estetika kuku (<em>nail art</em>) memiliki karakteristik operasional yang sangat kompleks dan heterogen jika dibandingkan dengan jenis perawatan salon rambut atau spa pada umumnya [4]. Kerumitan tersebut berakar pada tiga variabel utama:
</p>
<ol class="academic-list">
    <li><strong>Variasi Durasi Pengerjaan Kumulatif (Cumulative Service Duration):</strong> Setiap jenis perawatan memiliki kebutuhan waktu pengerjaan yang berbeda secara signifikan. Sebagai ilustrasi, layanan <em>Express Manicure</em> membutuhkan durasi 30 menit, aplikasi pewarnaan gel (<em>Gel Polish Application</em>) membutuhkan 45 menit, pemasangan sambungan kuku (<em>Nail Extension Acrylic/Gel Tip</em>) membutuhkan 75 menit, sedangkan lukisan motif manual (<em>Hand-Painted Nail Art</em>) dengan detail rumit membutuhkan 45 hingga 60 menit. Pada praktiknya, seorang pelanggan kerap melakukan pemesanan multi-layanan (<em>multi-treatment booking</em>) secara simultan dalam satu sesi kunjungan (misalnya: Manicure + Extension + Custom 3D Art). Total durasi layanan harus dihitung secara kumulatif (\(\\sum D_i\)) oleh sistem sebelum menentukan kelayakan ketersediaan slot waktu berikutnya.</li>
    <li><strong>Keterikatan Tunggal pada Tenaga Terapis Spesialis (Dedicated Nail Artist):</strong> Dalam industri <em>nail art</em>, pengerjaan kuku dari awal hingga selesai bersifat personal dan menuntut konsistensi gaya artistik. Oleh sebab itu, satu pelanggan harus ditangani oleh seorang teknisi (<em>nail artist</em>) yang sama selama keseluruhan rentang durasi reservasi. Sistem tidak dapat memecah pengerjaan satu sesi layanan kepada terapis yang berbeda di tengah jalan.</li>
    <li><strong>Kapasitas Fisik Meja Kerja yang Terbatas (Finite Station Capacity):</strong> Di samping ketersediaan tenaga terapis, operasional studio dibatasi oleh jumlah fisik meja pengerjaan kuku (<em>nail station</em>) yang dilengkapi dengan perlengkapan pendukung seperti lampu pengering UV/LED, mesin kikir elektrik (<em>e-file drill</em>), dan sistem sirkulasi debu akrilik. Pada Nail Glam Studio, kapasitas fisik dibatasi tepat pada 4 <em>stations</em>, sehingga jumlah reservasi aktif yang tumpang-tindih pada jam yang sama tidak boleh melampaui 4 pelanggan.</li>
</ol>

<h3 class="subsection-title">2.1.3 Algoritma Alokasi Waktu Dinamis (Dynamic Time-Slot Scheduling)</h3>
<p>
    Untuk mengatasi heterogenitas durasi multi-layanan pada kapasitas studio yang terbatas, sistem Booknail menerapkan algoritma Alokasi Waktu Dinamis (<em>Dynamic Time-Slot Scheduling</em>) [5]. Berbeda dengan pendekatan slot statis (<em>fixed-interval slotting</em>) yang membagi hari kerja ke dalam interval kaku berdurasi tetap (misalnya per 60 menit), pendekatan dinamis menghitung ketersediaan waktu berdasarkan titik awal (\(T_{start}\)) dan titik akhir (\(T_{end}\)) yang fleksibel. Titik akhir dihitung melalui persamaan matematis:
</p>
<div style="text-align: center; margin: 1.2rem 0; font-family: 'Times New Roman', serif; font-size: 13pt;">
    \[ T_{end} = T_{start} + \sum_{i=1}^{n} D(S_i) + \Delta_{prep} \]
</div>
<p class="no-indent">
    di mana \(D(S_i)\) menyatakan durasi waktu untuk layanan ke-\(i\) yang dipilih pelanggan, \(n\) menyatakan jumlah layanan terpilih dalam satu keranjang pemesanan, dan \(\\Delta_{prep}\) menyatakan alokasi waktu pembersihan alat dan sterilisasi <em>station</em> (<em>buffer cleanup time</em>) yang ditetapkan sebesar 10 menit.
</p>
<p>
    Secara formal, suatu kandidat slot waktu dinyatakan valid (tersedia) jika dan hanya jika memenuhi fungsi predikat logika gabungan berikut:
</p>
<div style="text-align: center; margin: 1.2rem 0; font-family: 'Times New Roman', serif; font-size: 12pt;">
    \[ \text{Valid}(A, T_{start}, T_{end}, \text{Date}) = \text{IsWorking}(A, \text{Date}, T_{start}, T_{end}) \land \neg\text{IsOnLeave}(A, \text{Date}) \land \neg\text{HasOverlap}(A, \text{Date}, T_{start}, T_{end}) \land (\text{ActiveStations}(T_{start}, T_{end}) < 4) \]
</div>
<p class="no-indent">
    di mana fungsi \(\\text{HasOverlap}\) mengevaluasi apakah terdapat data reservasi aktif eksisting milik <em>nail artist</em> \(A\) pada tanggal yang sama yang memenuhi kondisi tumpang tindih waktu (\(T_{start} < B_{end} \land T_{end} > B_{start}\)). Apabila salah satu kondisi pelanggaran terpenuhi, slot waktu tersebut secara otomatis dieksklusikan dari antarmuka pemilihan jadwal pelanggan.
</p>

<h3 class="subsection-title">2.1.4 Transaksi ACID dan Mekanisme Pessimistic Locking</h3>
<p>
    Dalam sistem reservasi terdistribusi berbasis web yang diakses oleh banyak pengguna secara bersamaan (<em>high concurrency</em>), fenomena <em>race condition</em> sering kali terjadi ketika dua atau lebih calon pelanggan mencoba memesan slot waktu yang sama persis pada <em>nail artist</em> yang sama dalam fraksi detik yang bersamaan [6]. Jika sistem hanya mengandalkan pengecekan basis data konvensional (<em>read-then-write</em>), kedua permintaan pemesanan akan membaca bahwa slot waktu masih berstatus kosong, sehingga kedua transaksi akan menuliskan data pemesanan baru secara paralel. Kondisi ini menghasilkan anomali pemesanan ganda (<em>double booking anomaly</em>) yang merugikan operasional studio.
</p>
<p>
    Untuk menjamin integritas data secara mutlak, sistem Booknail menerapkan prinsip transaksi ACID (<em>Atomicity, Consistency, Isolation, Durability</em>) dengan mekanisme penguncian pesimis (<em>Pessimistic Locking</em>) menggunakan klausul SQL <code>SELECT ... FOR UPDATE</code> [7]. Saat seorang pelanggan menekan tombol konfirmasi pembayaran uang muka (DP 30%), sistem memulai blok transaksi terisolasi (<code>DB::transaction</code>) dan mengunci baris rekaman ketersediaan <em>nail artist</em> serta tabel <code>bookings</code> yang beririsan. Penguncian ini menahan (<em>block</em>) transaksi konkuren lainnya hingga transaksi pertama selesai melakukan <em>commit</em> atau <em>rollback</em>. Dengan mekanisme ini, transaksi kedua yang mencoba mengakses slot yang sama akan langsung mendeteksi bahwa slot telah berhasil dialokasikan, lalu memberikan respons penolakan yang ramah (<em>graceful rejection</em>) kepada pengguna kedua untuk memilih slot alternatif.
</p>

<h3 class="subsection-title">2.1.5 Google Firebase Cloud Messaging (FCM) dan Web Push Service Worker</h3>
<p>
    Firebase Cloud Messaging (FCM) merupakan platform perpesanan lintas platform (<em>cross-platform messaging solution</em>) berkinerja tinggi yang dikembangkan oleh Google untuk mengirimkan notifikasi data dan pesan tampilan (<em>display notification</em>) ke perangkat klien secara andal dan hemat daya [8]. Berbeda dengan solusi pengiriman pesan melalui pihak ketiga seperti gateway WhatsApp API tidak resmi (<em>unofficial gateway</em>) yang kerap mengalami pemutusan koneksi (<em>session disconnect</em>), pembatasan kuota (<em>rate-limiting</em>), serta risiko pemblokiran nomor secara sepihak, Google FCM memanfaatkan arsitektur Web Push API standar W3C yang didukung langsung oleh kernel peramban modern (Google Chrome, Mozilla Firefox, Microsoft Edge, dan Safari).
</p>
<p>
    Komunikasi FCM bekerja dengan mengintegrasikan berkas JavaScript <em>Service Worker</em> (<code>firebase-messaging-sw.js</code>) di sisi peramban pengguna. <em>Service worker</em> berjalan di latar belakang (<em>background execution thread</em>) terpisah dari halaman web utama, memungkinkan penerimaan pesan <em>push</em> secara <em>asynchronous</em> bahkan ketika tab aplikasi Booknail sedang diminimalkan atau tidak aktif [9]. Ketika reservasi baru berhasil dibuat dan diverifikasi oleh sistem, backend Laravel Booknail mengirimkan muatan pesan JSON terenkripsi ke Google FCM HTTP Server via endpoint aman HTTPS menggunakan server authorization credential. FCM server kemudian meneruskan pesan tersebut ke perangkat tujuan melalui token registrasi unik (<em>registration token</em>) yang tersimpan pada tabel <code>fcm_tokens</code> di basis data. Antarmuka dashboard terapis di sisi peramban kemudian menangkap peristiwa (<em>event</em>) <code>onMessage</code> dan menampilkan notifikasi visual <em>toast alert</em> seketika yang disertai petunjuk audio (<em>chime sound alert</em>), memastikan <em>nail artist</em> segera mengetahui reservasi baru tanpa perlu menyegarkan halaman secara manual.
</p>

<div class="figure-container">
    <div class="browser-mockup" style="max-width: 600px;">
        <div class="browser-header">
            <span class="browser-dot bg-red-400"></span>
            <span class="browser-dot bg-yellow-400"></span>
            <span class="browser-dot bg-green-400"></span>
            <span style="font-size: 8.5pt; color: #64748b; margin-left: 8px;">Arsitektur Aliran Pesan Google FCM Web Push</span>
        </div>
        <div class="browser-content" style="background: #f8fafc; padding: 12px; font-family: sans-serif; font-size: 8.5pt; line-height: 1.4;">
            <div style="display: flex; justify-content: space-between; align-items: center; text-align: center;">
                <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px; width: 28%;">
                    <strong>1. Laravel Backend</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">FcmService::sendPush()<br>Payload JSON + Auth Key</span>
                </div>
                <div style="font-size: 11pt; color: #ea580c; font-weight: bold;">&rarr; HTTPS &rarr;</div>
                <div style="background: #ffedd5; border: 1.5px solid #fdba74; border-radius: 6px; padding: 8px; width: 32%;">
                    <strong>2. Google FCM Gateway</strong><br>
                    <span style="font-size: 7.5pt; color: #9a3412;">Routing ke Klien berdasarkan Client Registration Token</span>
                </div>
                <div style="font-size: 11pt; color: #ea580c; font-weight: bold;">&rarr; WebPush &rarr;</div>
                <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 8px; width: 28%;">
                    <strong>3. Service Worker</strong><br>
                    <span style="font-size: 7.5pt; color: #64748b;">firebase-messaging-sw.js<br>Toast Alert on Dashboard</span>
                </div>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.1 Diagram Aliran Komunikasi Real-Time Google Firebase Cloud Messaging (FCM) pada Booknail</div>
</div>

<h3 class="subsection-title">2.1.6 Framework Laravel 12 Berbasis Arsitektur Model-View-Controller (MVC)</h3>
<p>
    Laravel merupakan kerangka kerja pengembangan aplikasi web sumber terbuka (<em>open-source PHP web framework</em>) yang mempopulerkan paradigma penulisan kode yang ekspresif, elegan, dan terstruktur [10]. Dirilis pada kuartal pertama tahun 2025, Laravel 12 menghadirkan penyempurnaan signifikan pada performa perutean (<em>routing engine</em>), optimasi penanganan konkurensi melalui koneksi basis data asinkron, serta arsitektur modular yang ramping. Laravel mengadopsi pola perancangan perangkat lunak <em>Model-View-Controller</em> (MVC) yang memisahkan tanggung jawab fungsional aplikasi ke dalam tiga lapis independen:
</p>
<ul class="academic-list">
    <li><strong>Model:</strong> Komponen yang merepresentasikan struktur data dan logika bisnis aplikasi. Dalam Laravel 12, interaksi data dikelola melalui <em>Object-Relational Mapping</em> (ORM) Eloquent yang memetakan tabel basis data relasional ke dalam objek PHP secara intuitif serta menyediakan relasi deklaratif (<code>hasMany</code>, <code>belongsTo</code>, <code>belongsToMany</code>).</li>
    <li><strong>View:</strong> Komponen antarmuka pengguna grafis yang bertugas menyajikan representasi visual data kepada pengguna akhir. Laravel menggunakan <em>template engine</em> Blade yang mendukung penyusunan tata letak hierarkis (<em>component-based layouting</em>) serta perlindungan otomatis dari serangan <em>Cross-Site Scripting</em> (XSS).</li>
    <li><strong>Controller:</strong> Komponen pengontrol yang bertindak sebagai mediator antara Model dan View. Controller menangkap permintaan HTTP dari klien, mengeksekusi validasi masukan (<em>Form Request Validation</em>), memanggil layanan logika bisnis (<em>Service Layer</em>), dan menentukan respons tampilan atau berkas keluaran yang dikembalikan ke peramban.</li>
</ul>

<h3 class="subsection-title">2.1.7 Tailwind CSS dan Responsive Web Design</h3>
<p>
    Tailwind CSS adalah kerangka kerja lembar gaya berjenjang berbasis kelas utilitas (<em>utility-first CSS framework</em>) yang memungkinkan penyusunan antarmuka pengguna yang sangat fleksibel dan berkinerja tinggi tanpa meninggalkan dokumen HTML [11]. Melalui pendekatan utilitas, pengembang dapat menerapkan tata letak <em>Flexbox</em>, <em>CSS Grid</em>, tipografi responsif, serta penataan warna secara presisi. Prinsip <em>Responsive Web Design</em> (RWD) diterapkan secara komprehensif pada Booknail menggunakan penanda <em>breakpoint</em> standar Tailwind (<code>sm:</code>, <code>md:</code>, <code>lg:</code>, <code>xl:</code>), memastikan seluruh halaman aplikasi dapat diakses secara ergonomis baik melalui layar ponsel pintar pelanggan (<em>viewport 360px–425px</em>), tablet terapis di meja kasir (<em>768px</em>), hingga monitor desktop administrator (<em>1024px–1920px</em>).
</p>

<h3 class="subsection-title">2.1.8 Database Management System (DBMS) Relasional MySQL</h3>
<p>
    MySQL merupakan sistem manajemen basis data relasional (<em>Relational Database Management System</em> / RDBMS) berbasis bahasa kueri terstruktur (SQL) yang terbukti andal dalam menangani transaksi berskala intensif dengan tingkat konkurensi tinggi [12]. MySQL memanfaatkan mesin penyimpanan <em>InnoDB</em> yang mendukung penuh kepatuhan ACID, integritas referensial kunci asing (<em>foreign key constraints</em>), serta mekanisme penguncian baris granular (<em>row-level locking</em>). Fitur penguncian tingkat baris ini sangat vital bagi stabilitas transaksi pengalokasian slot reservasi pada Booknail, karena memungkinkan penguncian hanya pada baris jadwal yang sedang diproses tanpa memblokir pembacaan tabel secara keseluruhan oleh pengguna lain.
</p>

<h3 class="subsection-title">2.1.9 Unified Modeling Language (UML)</h3>
<p>
    <em>Unified Modeling Language</em> (UML) adalah bahasa pemodelan visual standar industri yang digunakan untuk menspesifikasikan, memvisualisasikan, membangun, dan mendokumentasikan artefak perangkat lunak berbasis objek [13]. Dalam penelitian ini, empat jenis diagram UML digunakan secara formal:
</p>
<ul class="academic-list">
    <li><strong>Use Case Diagram:</strong> Memodelkan batasan fungsional sistem dan interaksi antara aktor eksternal (Pelanggan, Nail Artist, dan Administrator) dengan layanan yang disediakan oleh sistem.</li>
    <li><strong>Activity Diagram:</strong> Menggambarkan aliran kerja langkah demi langkah (<em>workflow</em>) dari suatu proses bisnis atau logika algoritma internal, termasuk percabangan kondisi, pengulangan, dan konkurensi antarlajur peran (<em>swimlane</em>).</li>
    <li><strong>Sequence Diagram:</strong> Memetakan kolaborasi dinamis antar-objek perangkat lunak sepanjang dimensi waktu dalam mengeksekusi suatu skenario spesifik.</li>
    <li><strong>Class Diagram:</strong> Memodelkan struktur statis sistem yang memperlihatkan kelas-kelas perangkat lunak, atribut, metode operasional, serta keterhubungan asosiatif antarkelas.</li>
</ul>

<h3 class="subsection-title">2.1.10 White-Box Testing dan Basis Path Testing</h3>
<p>
    Pengujian kotak putih (<em>White-Box Testing</em>) merupakan metodologi verifikasi perangkat lunak yang berfokus pada pemeriksaan struktur internal, alur logika kendali (<em>control flow</em>), dan kebenaran eksekusi kode sumber program [14]. Salah satu teknik kotak putih yang paling objektif dan matematis adalah <em>Basis Path Testing</em> yang diperkenalkan oleh Thomas J. McCabe. Teknik ini memetakan kode program ke dalam suatu grafik alir kendali (<em>Control Flowgraph</em>) yang tersusun atas simpul (<em>nodes</em>) yang merepresentasikan urutan instruksi linier, dan tepi berarah (<em>edges</em>) yang merepresentasikan transfer kendali logika.
</p>
<p>
    Kompleksitas siklomatis (<em>Cyclomatic Complexity</em>), dinotasikan sebagai \(V(G)\), menentukan jumlah jalur eksekusi independen linier minimum yang harus diuji untuk memastikan setiap baris pernyataan instruksi dan kondisi logika telah dieksekusi setidaknya satu kali. Formula perhitungan \(V(G)\) didefinisikan sebagai:
</p>
<div style="text-align: center; margin: 1.2rem 0; font-family: 'Times New Roman', serif; font-size: 13pt;">
    \[ V(G) = E - N + 2P \]
</div>
<p class="no-indent">
    di mana \(E\) menyatakan jumlah busur penghubung logika (<em>edges</em>), \(N\) menyatakan jumlah simpul instruksi (<em>nodes</em>), dan \(P\) menyatakan jumlah komponen grafik terhubung (untuk modul tunggal, \(P = 1\)). Alternatif ekuivalen perhitungannya dapat diperoleh melalui rumus \(V(G) = P_{predikat} + 1\), di mana \(P_{predikat}\) menyatakan jumlah simpul kondisi predikat percabangan.
</p>

<h3 class="subsection-title">2.1.11 Black-Box Testing dan Equivalence Partitioning</h3>
<p>
    Pengujian kotak hitam (<em>Black-Box Testing</em>) menguji fungsionalitas sistem dari perspektif antarmuka eksternal pengguna tanpa mempertimbangkan struktur kode internal perangkat lunak [15]. Pengujian ini menitikberatkan pada kesesuaian antara input masukan yang diberikan dengan luaran (<em>output</em>) yang diharapkan berdasarkan spesifikasi kebutuhan perangkat lunak. Teknik yang diterapkan meliputi <em>Equivalence Partitioning</em> (membagi rentang nilai masukan menjadi kelas data valid dan tidak valid) serta <em>Boundary Value Analysis</em> (menguji nilai-nilai ekstrem pada batas ambang masukan).
</p>

<h3 class="subsection-title">2.1.12 System Usability Scale (SUS)</h3>
<p>
    <em>System Usability Scale</em> (SUS) adalah instrumen evaluasi usabilitas perangkat lunak yang dikembangkan oleh John Brooke pada tahun 1986 dan telah menjadi standar de facto industri dalam mengukur tingkat kegunaan, efektivitas, dan kepuasan pengguna akhir secara kuantitatif [16]. Kuesioner SUS terdiri dari 10 butir pernyataan terstandarisasi yang dinilai menggunakan skala Likert 5 tingkat (1 = Sangat Tidak Setuju hingga 5 = Sangat Setuju), dengan komposisi pernyataan bernada positif pada butir bernomor ganjil (1, 3, 5, 7, 9) dan pernyataan bernada negatif pada butir bernomor genap (2, 4, 6, 8, 10).
</p>
<p>
    Prosedur kalkulasi skor total SUS untuk setiap responden dilakukan dengan langkah standardisasi sebagai berikut:
</p>
<ol class="academic-list">
    <li>Untuk setiap butir pernyataan bernomor ganjil, kontribusi skor dihitung dengan formula: \(\text{Skor}_i = X_i - 1\)</li>
    <li>Untuk setiap butir pernyataan bernomor genap, kontribusi skor dihitung dengan formula: \(\text{Skor}_i = 5 - X_i\)</li>
    <li>Jumlahkan seluruh kontribusi skor dari 10 butir pernyataan, kemudian kalikan dengan faktor pengali 2,5 untuk menghasilkan skor akhir berskala 0 hingga 100:</li>
</ol>
<div style="text-align: center; margin: 1.2rem 0; font-family: 'Times New Roman', serif; font-size: 13pt;">
    \[ \text{Skor SUS} = \left( \sum_{i=1}^{10} \text{Skor}_i \right) \times 2{,}5 \]
</div>
<p class="no-indent">
    Hasil rata-rata skor SUS kemudian diinterpretasikan ke dalam skala penerimaan (<em>Acceptability Ranges</em>), pemeringkatan nilai huruf (<em>Grade Scale</em> A s/d F), serta kategori kata sifat (<em>Adjective Ratings</em>: Worst Imaginable, Poor, OK, Good, Excellent, Best Imaginable) berdasarkan tolok ukur Bangor, Kortum, dan Miller (2008). Skor rata-rata di atas 68 mengindikasikan tingkat usabilitas di atas rata-rata industri (<em>above average</em>).
</p>

<!-- ========================================== -->
<!-- 2.2 OBJEK PENELITIAN & ANALISIS SISTEM BERJALAN -->
<!-- ========================================== -->
<h2 class="section-title">2.2 Objek Penelitian dan Analisis Sistem Berjalan</h2>

<h3 class="subsection-title">2.2.1 Profil Nail Glam Studio</h3>
<p>
    Nail Glam Studio didirikan pada bulan Agustus tahun 2021 di kawasan perkantoran dan residensial Tanjung Duren, Jakarta Barat. Studio ini berfokus pada penyediaan jasa estetika dan perawatan kuku premium yang mengedepankan standar higienitas medis, penggunaan produk gel berkualitas tinggi bebas racun (<em>toxic-free non-yellowing gel</em>), serta keahlian teknis terapis yang telah mengantongi sertifikasi kejuruan nail artist profesional.
</p>
<p>
    <strong>Visi:</strong> Menjadi studio estetika kuku terdepan dan terpercaya di Indonesia yang menggabungkan presisi seni artistik, standar kesehatan kuku tertinggi, dan kenyamanan pengalaman reservasi modern.
</p>
<p>
    <strong>Misi:</strong>
</p>
<ol class="academic-list">
    <li>Menyajikan spektrum desain seni kuku yang inovatif, trendi, dan dipersonalisasi sesuai karakter individual pelanggan.</li>
    <li>Menerapkan protokol sanitasi dan sterilisasi alat bertaraf medis pada setiap sesi pengerjaan.</li>
    <li>Meningkatkan kualitas interaksi dan kenyamanan pelanggan melalui pemanfaatan sistem teknologi informasi terintegrasi.</li>
    <li>Membina kapabilitas dan profesionalisme tenaga teknisi lokal melalui pelatihan seni berkala.</li>
</ol>
<p>
    Struktur organisasi Nail Glam Studio dipimpin oleh seorang Pemilik Pengelola (<em>Studio Owner & General Manager</em>) yang membawahi 1 orang Koordinator Operasional & Administrasi (Admin), serta 4 orang Teknisi Spesialis Kuku (<em>Nail Artists</em>: Maya, Sisi, Clara, dan Dina). Secara fisik, studio beroperasi pada lahan seluas 65 m² yang dibagi menjadi area resepsionis/kasir, area tunggu tamu, dan area pengerjaan utama yang menampung tepat 4 <em>nail stations</em>.
</p>

<h3 class="subsection-title">2.2.2 Analisis Proses Bisnis Berjalan</h3>
<p>
    Proses bisnis reservasi yang berjalan saat ini pada Nail Glam Studio masih mengandalkan komunikasi semi-manual melalui aplikasi WhatsApp dan pencatatan buku agenda fisik. Alur kerja operasional lama digambarkan secara sistematis pada Gambar 2.2.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 9pt;">
        <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
            <div style="background: #e2e8f0; padding: 8px 16px; border-radius: 20px; font-weight: bold;">[Mulai] Calon Pelanggan Ingin Reservasi</div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; width: 90%; text-align: center;">
                <strong>Pelanggan Mengirim Pesan WhatsApp ke Admin Studio:</strong><br>
                Menyebutkan tanggal yang diinginkan, jenis layanan, dan preferensi terapis.
            </div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #fef3c7; border: 1px solid #fde68a; padding: 10px; border-radius: 6px; width: 90%; text-align: center;">
                <strong>Admin Memeriksa Buku Agenda Kertas & Papan Tulis Studio:</strong><br>
                Mencari slot kosong secara manual. Sering kali terhambat bila admin sedang melayani pelanggan di kasir (rata-rata waktu tunggu 18 menit).
            </div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #e0f2fe; border: 1px solid #bae6fd; padding: 8px 16px; border-radius: 6px; font-weight: bold;">Apakah Slot Waktu Tersedia?</div>
            <div style="display: flex; justify-content: space-around; width: 100%;">
                <div style="width: 45%; text-align: center; border-right: 1px dashed #cbd5e1; padding-right: 10px;">
                    <span style="color: #dc2626; font-weight: bold;">[TIDAK]</span><br>
                    <span style="font-size: 8pt; color: #475569;">Admin tawar menawar jadwal alternatif. Pelanggan kerap membatalkan pesanan (<em>customer churn</em>).</span>
                </div>
                <div style="width: 45%; text-align: center; padding-left: 10px;">
                    <span style="color: #16a34a; font-weight: bold;">[YA]</span><br>
                    <span style="font-size: 8pt; color: #475569;">Admin mengirim nomor rekening bank untuk transfer DP manual.</span>
                </div>
            </div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 10px; border-radius: 6px; width: 90%; text-align: center;">
                <strong>Pelanggan Mentransfer DP & Mengirim Bukti Transfer Gambar:</strong><br>
                Admin memeriksa saldo e-banking manual, lalu mencatat nama pelanggan pada buku agenda.
            </div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #fee2e2; border: 1px solid #fca5a5; padding: 10px; border-radius: 6px; width: 90%; text-align: center;">
                <strong>Titik Kerentanan Kritis (Human Error):</strong><br>
                Jika admin lupa mencatat seketika, terapis lain menerima pesanan walk-in, terjadi <em>double booking</em> (tingkat insiden mencapai 14,3%).
            </div>
            <div style="color: #64748b; font-weight: bold;">&darr;</div>
            <div style="background: #e2e8f0; padding: 8px 16px; border-radius: 20px; font-weight: bold;">[Selesai] Pelanggan Datang pada Hari H</div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.2 Bagan Alir (Flowchart) Proses Bisnis Reservasi Manual Berjalan pada Nail Glam Studio</div>
</div>

<h3 class="subsection-title">2.2.3 Identifikasi Kelemahan Sistem Berjalan (PIECES Framework)</h3>
<p>
    Evaluasi mendalam terhadap kinerja operasional sistem berjalan dilakukan menggunakan kerangka kerja PIECES (<em>Performance, Information, Economics, Control, Efficiency, Service</em>) [17]. Hasil evaluasi komprehensif disajikan pada Tabel 2.1.
</p>

<div class="table-container">
    <div class="table-title">Tabel 2.1 Analisis Kelemahan Sistem Reservasi Berjalan Berdasarkan Kerangka Kerja PIECES</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 15%;">Dimensi PIECES</th>
                <th style="width: 42%;">Kondisi Sistem Berjalan (Manual WhatsApp)</th>
                <th style="width: 43%;">Target Solusi pada Sistem Usulan (Booknail)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Performance (Kinerja)</strong></td>
                <td>Waktu tanggap (<em>response time</em>) pengecekan jadwal membutuhkan rata-rata 18 menit karena ketergantungan pada kehadiran fisik admin di meja resepsionis.</td>
                <td>Pengecekan ketersediaan slot waktu terapis diproses secara komputasional instan (&le; 1 detik) melalui algoritma dinamis.</td>
            </tr>
            <tr>
                <td><strong>Information (Informasi)</strong></td>
                <td>Informasi katalog layanan, portofolio desain, dan transparansi slot kosong tidak dapat dilihat langsung oleh pelanggan tanpa bertanya manual ke admin.</td>
                <td>Katalog digital interaktif lengkap dengan galeri foto, rincian durasi menit, estimasi tarif, dan kalender slot kosong real-time.</td>
            </tr>
            <tr>
                <td><strong>Economics (Ekonomi)</strong></td>
                <td>Tingginya tingkat pembatalan pesanan (<em>churn rate</em> 22%) akibat lambatnya respon admin, serta potensi kerugian akibat pelanggan tidak hadir (<em>no-show</em>) tanpa komitmen DP.</td>
                <td>Pemesanan mandiri 24/7 mengunci komitmen pelanggan via DP 30% dan otomatis melepaskan slot expired (60 menit), meminimalkan kerugian finansial.</td>
            </tr>
            <tr>
                <td><strong>Control (Pengendalian)</strong></td>
                <td>Tidak ada mekanisme proteksi konkurensi data. Insiden <em>double booking</em> mencapai 14,3% akibat pencatatan buku agenda yang terlambat.</td>
                <td>Penerapan transaksi ACID dengan SQL <code>SELECT ... FOR UPDATE</code> (pessimistic lock) menjamin zero tolerance (0%) terhadap <em>double booking</em>.</td>
            </tr>
            <tr>
                <td><strong>Efficiency (Efisiensi)</strong></td>
                <td>Admin menghabiskan lebih dari 4,5 jam per hari hanya untuk melayani chat tanya-jawab jadwal berulang, memecah fokus layanan kasir di lokasi.</td>
                <td>Otomasi alur reservasi mandiri (<em>self-service</em>) memangkas beban kerja administratif hingga lebih dari 80%.</td>
            </tr>
            <tr>
                <td><strong>Service (Pelayanan)</strong></td>
                <td>Terapis tidak memiliki pemberitahuan instan saat ada reservasi baru. Terapis harus memeriksa buku kasir manual untuk mengetahui jadwal kerjanya.</td>
                <td>Diseminasi push notification real-time via Google FCM Web Push langsung ke dashboard terapis dengan alert audio dan modal konfirmasi.</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- 2.3 PERANCANGAN PROSES SISTEM USULAN -->
<!-- ========================================== -->
<h2 class="section-title">2.3 Perancangan Proses Sistem Usulan</h2>

<h3 class="subsection-title">2.3.1 Use Case Diagram</h3>
<p>
    Berdasarkan analisis kebutuhan fungsional perangkat lunak, sistem Booknail mengakomodasi 3 aktor utama: Pelanggan (<em>Customer</em>), Teknisi Kuku (<em>Nail Artist</em>), dan Pengelola Studio (<em>Administrator</em>). Interaksi fungsionalitas di antara ketiga aktor tersebut dimodelkan ke dalam 27 <em>use cases</em> terstruktur seperti diilustrasikan pada Gambar 2.3.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 8pt;">
        <div style="text-align: center; font-weight: bold; font-size: 10pt; margin-bottom: 15px; color: #1e293b;">
            USE CASE DIAGRAM SISTEM INFORMASI RESERVASI BOOKNAIL
        </div>
        <div style="display: flex; justify-content: space-between;">
            <!-- Actor Pelanggan -->
            <div style="width: 22%; text-align: center;">
                <div style="font-size: 24pt;">&#128100;</div>
                <strong style="color: #0284c7;">Pelanggan</strong>
                <div style="font-size: 7pt; color: #64748b; margin-top: 4px;">(Customer)</div>
            </div>
            <!-- System Boundary -->
            <div style="width: 54%; border: 1.5px solid #0284c7; border-radius: 8px; padding: 10px; background: #f8fafc;">
                <div style="font-weight: bold; text-align: center; font-size: 8pt; color: #0369a1; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px;">
                    Boundary: Booknail Web Application
                </div>
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-01 Registrasi & Login Akun</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-02 Eksplorasi Katalog Layanan & Portofolio</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center; font-weight: bold;">UC-04 Pemesanan Multi-Layanan (Wizard)</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-07 Pemilihan Tanggal & Terapis</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center; font-weight: bold;">UC-08 Validasi Slot Dinamis & Lock Transaksi</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-11 Pembayaran DP 30% & Upload Bukti</div>
                    <div style="background: #e0f2fe; border: 1px solid #7dd3fc; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-15 Tracking Riwayat & Detail Invoice</div>
                    <div style="background: #ffedd5; border: 1px solid #fdba74; border-radius: 12px; padding: 3px 6px; text-align: center; font-weight: bold; color: #c2410c;">UC-23 Penerimaan Alert FCM Real-Time</div>
                    <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-30 Pengaturan Jam Kerja & Cuti Terapis</div>
                    <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-34 Verifikasi Bukti Pembayaran DP</div>
                    <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 12px; padding: 3px 6px; text-align: center; font-weight: bold; color: #166534;">UC-26 Export Laporan Reservasi PDF & CSV</div>
                    <div style="background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; padding: 3px 6px; text-align: center;">UC-41 Ulasan & Rating Pelayanan</div>
                </div>
            </div>
            <!-- Actor Artist & Admin -->
            <div style="width: 22%; display: flex; flex-direction: column; justify-content: space-around; text-align: center;">
                <div>
                    <div style="font-size: 22pt;">&#128101;</div>
                    <strong style="color: #ea580c;">Nail Artist</strong>
                    <div style="font-size: 7pt; color: #64748b;">(Terapis)</div>
                </div>
                <div style="margin-top: 15px;">
                    <div style="font-size: 22pt;">&#128188;</div>
                    <strong style="color: #1e293b;">Administrator</strong>
                    <div style="font-size: 7pt; color: #64748b;">(Admin & Pemilik)</div>
                </div>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.3 Use Case Diagram Sistem Informasi Reservasi Booknail</div>
</div>

<h3 class="subsection-title">2.3.2 Skenario Use Case Rinci</h3>
<p>
    Untuk memberikan gambaran tekstual yang presisi mengenai alur interaksi aktor dengan sistem, skenario use case rinci disusun untuk enam use case yang paling kritikal terhadap keberhasilan operasional sistem:
</p>

<!-- Skenario UC-04 -->
<div class="table-container">
    <div class="table-title">Tabel 2.2 Skenario Use Case UC-04: Pemesanan Multi-Layanan melalui Wizard</div>
    <table class="academic-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; background: #f8fafc;">Nomor & Nama Use Case</td>
                <td style="width: 75%;"><strong>UC-04: Pemesanan Multi-Layanan melalui Multi-Step Wizard</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Aktor Utama</td>
                <td>Pelanggan (Customer) terotentikasi</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Deskripsi Singkat</td>
                <td>Pelanggan memilih beberapa jenis layanan perawatan kuku sekaligus, memilih preferensi <em>nail artist</em>, menentukan tanggal kedatangan, dan memilih slot waktu yang terkalkulasi otomatis.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Awal (Pre-condition)</td>
                <td>Pelanggan telah berhasil login dan memiliki saldo pembayaran/akses perbankan untuk transfer DP.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Akhir (Post-condition)</td>
                <td>Data reservasi tersimpan dalam status <code>pending_payment</code>, slot waktu terkunci sementara selama 60 menit, dan rincian tagihan DP 30% ditampilkan.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Alur Utama (Basic Flow)</td>
                <td>
                    <ol style="margin: 0; padding-left: 1.2rem;">
                        <li>Pelanggan membuka halaman <code>/booking</code>.</li>
                        <li>Pelanggan memilih satu atau lebih layanan (multi-treatment) dari daftar kategori.</li>
                        <li>Sistem menghitung kumulatif total biaya (\(\\sum P_i\)) dan total durasi menit (\(\\sum D_i\)).</li>
                        <li>Pelanggan melanjutkan ke Langkah 2: memilih <em>nail artist</em> dan tanggal kedatangan.</li>
                        <li>Sistem mengeksekusi <code>SlotAvailabilityService</code> untuk merender tombol slot waktu yang valid.</li>
                        <li>Pelanggan memilih jam slot awal yang tersedia dan mengisi catatan preferensi desain.</li>
                        <li>Sistem mengalokasikan station fisik yang kosong (1 dari 4 stations).</li>
                        <li>Pelanggan meninjau ringkasan pemesanan dan menekan tombol "Konfirmasi & Lanjut Bayar DP".</li>
                        <li>Sistem membuka transaksi database dengan <em>pessimistic lock</em>, menyimpan reservasi, dan menampilkan instruksi transfer pembayaran.</li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Alur Alternatif (Alternative Flow)</td>
                <td>
                    <strong>4a. Seluruh slot pada terapis terpilih penuh:</strong> Sistem menampilkan rekomendasi <em>nail artist</em> lain yang masih memiliki slot kosong pada tanggal yang sama.<br>
                    <strong>8a. Terjadi tabrakan konkurensi (slot diambil pengguna lain dalam fraksi detik bersamaan):</strong> Transaksi di-rollback, sistem menampilkan pesan peringatan ramah: "Mohon maaf, slot waktu baru saja dipesan oleh pelanggan lain. Silakan pilih jam alternatif."
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Skenario UC-11 -->
<div class="table-container">
    <div class="table-title">Tabel 2.3 Skenario Use Case UC-11: Verifikasi Pembayaran Down Payment (DP 30%)</div>
    <table class="academic-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; background: #f8fafc;">Nomor & Nama Use Case</td>
                <td style="width: 75%;"><strong>UC-11: Verifikasi Pembayaran Down Payment (DP 30%)</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Aktor Utama</td>
                <td>Administrator Studio</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Deskripsi Singkat</td>
                <td>Admin memeriksa keabsahan bukti struk transfer DP 30% yang diunggah pelanggan, lalu mengonfirmasi atau menolak status pembayaran.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Awal (Pre-condition)</td>
                <td>Pelanggan telah mengunggah berkas gambar bukti pembayaran (JPG/PNG &le; 2MB) pada halaman invoice reservasi.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Akhir (Post-condition)</td>
                <td>Status reservasi berubah menjadi <code>confirmed</code>, status pembayaran menjadi <code>dp_paid</code>, dan pesan FCM push alert terkirim seketika ke dashboard <em>nail artist</em>.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Alur Utama (Basic Flow)</td>
                <td>
                    <ol style="margin: 0; padding-left: 1.2rem;">
                        <li>Admin membuka menu <code>/admin/bookings</code> dan memfilter status "Pending Verification".</li>
                        <li>Admin mengklik detail reservasi dan meninjau gambar bukti struk pembayaran via modal zoom.</li>
                        <li>Admin memeriksa mutasi rekening perbankan studio untuk memvalidasi dana masuk.</li>
                        <li>Admin menekan tombol "Verifikasi & Konfirmasi Jadwal".</li>
                        <li>Sistem memperbarui status reservasi menjadi <code>confirmed</code> secara atomik.</li>
                        <li>Sistem memicu event <code>BookingConfirmedEvent</code> yang menugaskan background job <code>SendFcmJob</code>.</li>
                        <li>Sistem mengirimkan notifikasi konfirmasi ke pelanggan dan push notification FCM ke perangkat <em>nail artist</em>.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Skenario UC-23 -->
<div class="table-container">
    <div class="table-title">Tabel 2.4 Skenario Use Case UC-23: Penerimaan Push Notifikasi FCM Real-Time pada Terapis</div>
    <table class="academic-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; background: #f8fafc;">Nomor & Nama Use Case</td>
                <td style="width: 75%;"><strong>UC-23: Penerimaan Push Notifikasi FCM Real-Time</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Aktor Utama</td>
                <td>Nail Artist (Terapis)</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Deskripsi Singkat</td>
                <td>Terapis menerima notifikasi visual dan audio secara seketika pada antarmuka dashboard saat reservasi baru dikonfirmasi atau dibatalkan tanpa me-refresh browser.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Awal (Pre-condition)</td>
                <td>Terapis telah login pada perangkat (ponsel/tablet) dan mengizinkan izin notifikasi (<em>Notification Permission: Granted</em>) sehingga token tersimpan di <code>fcm_tokens</code>.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Akhir (Post-condition)</td>
                <td>Layar menampilkan pop-up toast interaktif dengan detail nama pelanggan, jam mulai, dan daftar perlakuan kuku yang harus dipersiapkan.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Alur Utama (Basic Flow)</td>
                <td>
                    <ol style="margin: 0; padding-left: 1.2rem;">
                        <li>Terapis membuka halaman dashboard <code>/artist/dashboard</code>.</li>
                        <li>Backend Booknail mendeteksi event verifikasi jadwal dan memanggil <code>FcmService::sendPush()</code>.</li>
                        <li>Server Google FCM merutekan paket pesan terenkripsi ke <em>Service Worker</em> klien.</li>
                        <li>Fungsi <code>onMessage()</code> pada klien menangkap payload JSON.</li>
                        <li>Peramban membunyikan nada notifikasi halus dan menampilkan banner notifikasi di sudut kanan atas layar.</li>
                        <li>Daftar antrean jadwal harian terapis diperbarui secara reaktif melalui injeksi komponen DOM.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Skenario UC-26 -->
<div class="table-container">
    <div class="table-title">Tabel 2.5 Skenario Use Case UC-26: Export Laporan Reservasi ke Format PDF dan CSV (Excel)</div>
    <table class="academic-table">
        <tbody>
            <tr>
                <td style="width: 25%; font-weight: bold; background: #f8fafc;">Nomor & Nama Use Case</td>
                <td style="width: 75%;"><strong>UC-26: Export Laporan Reservasi ke Format PDF dan CSV (Excel)</strong></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Aktor Utama</td>
                <td>Administrator / Pemilik Studio</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Deskripsi Singkat</td>
                <td>Admin menyaring data transaksi reservasi berdasarkan parameter rentang tanggal, status pemesanan, dan filter terapis, kemudian mengekspor hasil ke format cetak PDF atau format lembar kerja CSV (Excel).</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Awal (Pre-condition)</td>
                <td>Admin telah login dengan hak akses manajerial pada halaman <code>/admin/reports</code>.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Kondisi Akhir (Post-condition)</td>
                <td>Berkas laporan terunduh otomatis ke komputer admin dengan metadata keuangan dan operasional yang lengkap.</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f8fafc;">Alur Utama (Basic Flow)</td>
                <td>
                    <ol style="margin: 0; padding-left: 1.2rem;">
                        <li>Admin memilih parameter filter: <code>date_from</code>, <code>date_to</code>, <code>status</code>, dan <code>nail_artist_id</code>.</li>
                        <li>Sistem memperbarui tampilan tabel pratinjau data secara instan beserta rekapitulasi metrik kunci (Total Reservasi, Total Pendapatan, Rata-rata Durasi Layanan).</li>
                        <li>Admin mengklik opsi tombol ekspor: "Download PDF" atau "Download CSV (Excel)".</li>
                        <li>Jika opsi PDF dipilih: Controller memanggil pustaka DomPDF, menyusun lembar laporan berorientasi lanskap lengkap dengan kop surat resmi studio, tabel rincian transaksi, total kalkulasi, dan tanda tangan digital pengelola.</li>
                        <li>Jika opsi CSV dipilih: Controller membangkitkan <em>StreamedResponse</em> dengan injeksi <em>UTF-8 Byte Order Mark</em> (BOM) <code>\\xEF\\xBB\\xBF</code> agar karakter mata uang dan format angka terbaca sempurna pada Microsoft Excel tanpa distorsi enkoding.</li>
                        <li>Peramban mengunduh berkas fisik <code>laporan-reservasi-YYYY-MM-DD.pdf</code> atau <code>laporan-reservasi-YYYY-MM-DD.csv</code>.</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">2.3.3 Activity Diagram</h3>
<p>
    Activity diagram memodelkan aliran kerja komputasional dan interaksi antar-lajur peran (<em>swimlane</em>) secara visual. Gambar 2.4 memodelkan alur komprehensif mulai dari reservasi pelanggan, validasi ketersediaan dinamis ber-locking, verifikasi admin, hingga diseminasi pesan Google FCM ke terapis.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 8pt;">
        <div style="text-align: center; font-weight: bold; font-size: 9.5pt; margin-bottom: 12px; color: #1e293b;">
            ACTIVITY DIAGRAM: END-TO-END RESERVASI, VALIDASI TRANSAKSI, & FCM DISPATCH
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden;">
            <!-- Swimlane 1: Pelanggan -->
            <div style="border-right: 1px solid #cbd5e1;">
                <div style="background: #e0f2fe; padding: 6px; text-align: center; font-weight: bold; color: #0369a1; border-bottom: 1px solid #cbd5e1;">Pelanggan</div>
                <div style="padding: 8px; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                    <div style="background: #111; color: #fff; width: 14px; height: 14px; border-radius: 50%;"></div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Pilih Layanan & Terapis</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Pilih Jam Slot Kosong</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Konfirmasi Booking & Transfer DP</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Unggah Bukti Struk Bayar</div>
                </div>
            </div>
            <!-- Swimlane 2: Sistem (Booknail) -->
            <div style="border-right: 1px solid #cbd5e1;">
                <div style="background: #f1f5f9; padding: 6px; text-align: center; font-weight: bold; color: #334155; border-bottom: 1px solid #cbd5e1;">Sistem Booknail</div>
                <div style="padding: 8px; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                    <div style="height: 25px;"></div>
                    <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Kalkulasi Durasi Kumulatif</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; padding: 5px; text-align: center; width: 95%; font-weight: bold; color: #991b1b;">SQL FOR UPDATE Lock Slot</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Simpan Status Pending</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #ffedd5; border: 1px solid #fdba74; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Dispatch SendFcmJob</div>
                </div>
            </div>
            <!-- Swimlane 3: Admin Studio -->
            <div style="border-right: 1px solid #cbd5e1;">
                <div style="background: #e2e8f0; padding: 6px; text-align: center; font-weight: bold; color: #1e293b; border-bottom: 1px solid #cbd5e1;">Admin Studio</div>
                <div style="padding: 8px; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                    <div style="height: 120px;"></div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Review Bukti Struk DP</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #dcfce7; border: 1px solid #86efac; border-radius: 6px; padding: 5px; text-align: center; width: 95%; font-weight: bold; color: #166534;">Klik Verifikasi Reservasi</div>
                </div>
            </div>
            <!-- Swimlane 4: Nail Artist -->
            <div>
                <div style="background: #ffedd5; padding: 6px; text-align: center; font-weight: bold; color: #c2410c; border-bottom: 1px solid #cbd5e1;">Nail Artist</div>
                <div style="padding: 8px; display: flex; flex-direction: column; gap: 8px; align-items: center;">
                    <div style="height: 180px;"></div>
                    <div style="background: #ffedd5; border: 1px solid #fdba74; border-radius: 6px; padding: 5px; text-align: center; width: 95%; font-weight: bold; color: #c2410c;">Terima FCM Toast Alert</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #f8fafc; border: 1px solid #94a3b8; border-radius: 6px; padding: 5px; text-align: center; width: 95%;">Siapkan Treatment Pelanggan</div>
                    <div style="color: #64748b;">&darr;</div>
                    <div style="background: #111; color: #fff; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 0 2px #fff, 0 0 0 3px #111;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.4 Activity Diagram Proses Reservasi, Penguncian Transaksi, dan Notifikasi FCM</div>
</div>

<h3 class="subsection-title">2.3.4 Sequence Diagram</h3>
<p>
    Interaksi dinamis antar-objek perangkat lunak pada skenario pemesanan ber-locking dimodelkan pada Gambar 2.5 menggunakan Sequence Diagram.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: monospace; font-size: 8pt; line-height: 1.4;">
        <div style="text-align: center; font-family: sans-serif; font-weight: bold; font-size: 9.5pt; margin-bottom: 12px; color: #1e293b;">
            SEQUENCE DIAGRAM: TRANSAKSI PEMESANAN & PENGIRIMAN FCM PUSH ALERT
        </div>
        <div style="background: #0f172a; color: #f8fafc; padding: 12px; border-radius: 6px; overflow-x: auto;">
            <pre style="margin: 0; font-family: 'Consolas', 'Courier New', monospace; font-size: 7.5pt; color: #38bdf8;">
Customer          BookingWizard       SlotService        Database            Admin           FcmService      NailArtist
   |                    |                  |                 |                 |                 |               |
   |--- 1. Submit ----->|                  |                 |                 |                 |               |
   |   (Services,Date)  |--- 2. Check ---->|                 |                 |                 |               |
   |                    |   Availability   |-- 3. Query ---->|                 |                 |               |
   |                    |                  |   Bookings/Leave|                 |                 |               |
   |                    |                  |<- 4. Return ----|                 |                 |               |
   |                    |<- 5. Available --|                 |                 |                 |               |
   |                    |   Slots List     |                 |                 |                 |               |
   |<-- 6. Render UI ---|                  |                 |                 |                 |               |
   |                    |                  |                 |                 |                 |               |
   |--- 7. Select Slot -|                  |                 |                 |                 |               |
   |    & Confirm DP    |-- 8. DB::transaction() ----------->|                 |                 |               |
   |                    |      SELECT ... FOR UPDATE (Lock)  |                 |                 |               |
   |                    |-- 9. Insert Booking (Pending) ---->|                 |                 |               |
   |<-- 10. Show Invoice|                                    |                 |                 |               |
   |                    |                                    |                 |                 |               |
   |--- 11. Upload Transfer Proof -------------------------->|                 |                 |               |
   |                    |                                    |<-- 12. Review --|                 |               |
   |                    |                                    |    Payment Proof|                 |               |
   |                    |                                    |-- 13. Confirm ->|                 |               |
   |                    |                                    |   (Status: Done)|                 |               |
   |                    |                                    |                 |-- 14. Trigger ->|               |
   |                    |                                    |                 |   Notification  |-- 15. Push -->|
   |                    |                                    |                 |                 |    WebAlert   | (Audio Chime &
   |                    |                                    |                 |                 |               |  Toast Notification)
            </pre>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.5 Sequence Diagram Pemesanan Multi-Layanan, Pessimistic Locking, dan FCM Push Alert</div>
</div>

<!-- ========================================== -->
<!-- 2.4 PERANCANGAN BASIS DATA -->
<!-- ========================================== -->
<h2 class="section-title">2.4 Perancangan Basis Data</h2>
<p>
    Perancangan basis data relasional sistem Booknail diuraikan secara hierarkis melalui tiga tingkatan abstraksi model data standar: <em>Conceptual Data Model</em> (CDM), <em>Logical Data Model</em> (LDM), dan <em>Physical Data Model</em> (PDM), diikuti kamus data rinci dari 15 tabel entitas, serta <em>Class Diagram</em> sistem. Sesuai instruksi metodologis penelitian, pemodelan data disajikan menggunakan format kode deklaratif standar industri (<strong>Mermaid</strong> untuk CDM, LDM, dan PDM; serta <strong>PlantUML</strong> untuk Class Diagram).
</p>

<h3 class="subsection-title">2.4.1 Conceptual Data Model (CDM)</h3>
<p>
    CDM mendefinisikan entitas bisnis tingkat konseptual beserta derajat kardinalitas relasi antar-entitas tanpa terikat pada detail implementasi teknis. Kode representasi Mermaid dan visualisasi CDM disajikan pada Gambar 2.6.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode Mermaid: Conceptual Data Model (CDM) Booknail</div>
        <pre><code>erDiagram
    USER ||--o{ BOOKING : "melakukan reservasi"
    USER ||--o| NAIL_ARTIST : "memiliki profil spesialis"
    USER ||--o{ REVIEW : "memberikan ulasan"
    USER ||--o{ FCM_TOKEN : "mendaftarkan token perangkat"
    
    NAIL_ARTIST ||--o{ ARTIST_SCHEDULE : "memiliki jadwal kerja"
    NAIL_ARTIST ||--o{ ARTIST_LEAVE : "mengajukan cuti"
    NAIL_ARTIST ||--o{ BOOKING : "mengerjakan reservasi"
    NAIL_ARTIST ||--o{ PORTFOLIO : "memamerkan karya"
    
    CATEGORY ||--|{ SERVICE : "mengelompokkan"
    
    BOOKING ||--|{ BOOKING_DETAIL : "terdiri atas rincian"
    SERVICE ||--o{ BOOKING_DETAIL : "dipilih dalam"
    
    BOOKING ||--|| PAYMENT : "memiliki transaksi"
    BOOKING }o--|| STATION : "menempati fasilitas fisik"
    BOOKING ||--o| REVIEW : "dinilai kualitasnya"</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.6 Kode Deklaratif Mermaid untuk Conceptual Data Model (CDM) Booknail</div>
</div>

<h3 class="subsection-title">2.4.2 Logical Data Model (LDM)</h3>
<p>
    LDM menjabarkan entitas konseptual ke dalam bentuk relasional yang ternormalisasi (memenuhi kaidah Bentuk Normal Ketiga / 3NF), melengkapi setiap entitas dengan atribut kunci primer (<em>Primary Key</em> / PK) dan kunci tamu (<em>Foreign Key</em> / FK) tanpa bergantung pada tipe data spesifik vendor DBMS. Kode Mermaid LDM disajikan pada Gambar 2.7.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode Mermaid: Logical Data Model (LDM) Booknail</div>
        <pre><code>erDiagram
    USERS {
        string id PK
        string name
        string email
        string phone
        string role "customer, artist, admin"
    }
    NAIL_ARTISTS {
        string id PK
        string user_id FK
        string bio
        boolean is_active
    }
    ARTIST_SCHEDULES {
        string id PK
        string artist_id FK
        integer day_of_week
        time start_time
        time end_time
    }
    ARTIST_LEAVES {
        string id PK
        string artist_id FK
        date start_date
        date end_date
        string status
    }
    SERVICES {
        string id PK
        string category_id FK
        string name
        integer duration_minutes
        decimal price
    }
    BOOKINGS {
        string id PK
        string user_id FK
        string artist_id FK
        string station_id FK
        date booking_date
        time start_time
        time end_time
        string status "pending, confirmed, completed, cancelled"
        decimal total_price
        decimal dp_amount
    }
    BOOKING_DETAILS {
        string id PK
        string booking_id FK
        string service_id FK
        decimal price
        integer duration_minutes
    }
    PAYMENTS {
        string id PK
        string booking_id FK
        decimal amount
        string payment_type "dp, full"
        string payment_status "pending, verified, rejected"
        string proof_image
    }
    STATIONS {
        string id PK
        string station_number
        string description
    }
    FCM_TOKENS {
        string id PK
        string user_id FK
        string device_token
    }

    USERS ||--o{ NAIL_ARTISTS : "user_id"
    USERS ||--o{ BOOKINGS : "user_id"
    USERS ||--o{ FCM_TOKENS : "user_id"
    NAIL_ARTISTS ||--o{ ARTIST_SCHEDULES : "artist_id"
    NAIL_ARTISTS ||--o{ ARTIST_LEAVES : "artist_id"
    NAIL_ARTISTS ||--o{ BOOKINGS : "artist_id"
    STATIONS ||--o{ BOOKINGS : "station_id"
    BOOKINGS ||--|{ BOOKING_DETAILS : "booking_id"
    SERVICES ||--o{ BOOKING_DETAILS : "service_id"
    BOOKINGS ||--|| PAYMENTS : "booking_id"</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.7 Kode Deklaratif Mermaid untuk Logical Data Model (LDM) Booknail</div>
</div>

<h3 class="subsection-title">2.4.3 Physical Data Model (PDM)</h3>
<p>
    PDM merepresentasikan skema fisik basis data secara spesifik pada mesin penyimpanan MySQL 8.0 InnoDB, mencakup spesifikasi tipe data skalar (BIGINT, VARCHAR, DECIMAL, ENUM, TIMESTAMP), indeks integritas referensial, serta kunci unik. Kode Mermaid PDM disajikan pada Gambar 2.8.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode Mermaid: Physical Data Model (PDM) Booknail (MySQL InnoDB Engine)</div>
        <pre><code>erDiagram
    users {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        VARCHAR_255 name
        VARCHAR_255 email "UNIQUE"
        VARCHAR_20 phone
        VARCHAR_255 password
        ENUM role "customer,artist,admin"
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    nail_artists {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED user_id FK "INDEX"
        TEXT bio
        TINYINT_1 is_active "DEFAULT 1"
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }
    artist_schedules {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED nail_artist_id FK "INDEX"
        TINYINT day_of_week "0=Sun, 6=Sat"
        TIME start_time
        TIME end_time
        TINYINT_1 is_off "DEFAULT 0"
    }
    artist_leaves {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED nail_artist_id FK "INDEX"
        DATE start_date
        DATE end_date
        VARCHAR_255 reason
        ENUM status "pending,approved,rejected"
    }
    categories {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        VARCHAR_100 name
        VARCHAR_100 slug "UNIQUE"
        TEXT description
    }
    services {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED category_id FK "INDEX"
        VARCHAR_255 name
        TEXT description
        INT duration_minutes
        DECIMAL_12_2 price
        VARCHAR_255 image_url
        TINYINT_1 is_active "DEFAULT 1"
    }
    stations {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        VARCHAR_50 station_name
        TINYINT_1 is_active "DEFAULT 1"
    }
    bookings {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        VARCHAR_50 booking_code "UNIQUE, INDEX"
        BIGINT_UNSIGNED user_id FK "INDEX"
        BIGINT_UNSIGNED nail_artist_id FK "INDEX"
        BIGINT_UNSIGNED station_id FK "INDEX"
        DATE booking_date "INDEX"
        TIME start_time "INDEX"
        TIME end_time "INDEX"
        DECIMAL_12_2 total_price
        DECIMAL_12_2 dp_amount
        ENUM status "pending_payment,confirmed,completed,cancelled"
        TEXT notes
        TIMESTAMP expires_at "INDEX"
        TIMESTAMP created_at
    }
    booking_details {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED booking_id FK "INDEX"
        BIGINT_UNSIGNED service_id FK "INDEX"
        DECIMAL_12_2 price_at_booking
        INT duration_minutes
    }
    payments {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED booking_id FK "UNIQUE"
        DECIMAL_12_2 amount
        ENUM payment_type "dp,full"
        VARCHAR_255 proof_image
        ENUM status "pending,verified,rejected"
        BIGINT_UNSIGNED verified_by FK
        TIMESTAMP verified_at
    }
    fcm_tokens {
        BIGINT_UNSIGNED id PK "AUTO_INCREMENT"
        BIGINT_UNSIGNED user_id FK "INDEX"
        VARCHAR_255 token "INDEX"
        VARCHAR_100 device_type
        TIMESTAMP last_used_at
    }

    users ||--o{ nail_artists : "user_id"
    users ||--o{ bookings : "user_id"
    users ||--o{ fcm_tokens : "user_id"
    nail_artists ||--o{ artist_schedules : "nail_artist_id"
    nail_artists ||--o{ artist_leaves : "nail_artist_id"
    nail_artists ||--o{ bookings : "nail_artist_id"
    stations ||--o{ bookings : "station_id"
    categories ||--o{ services : "category_id"
    bookings ||--|{ booking_details : "booking_id"
    services ||--o{ booking_details : "service_id"
    bookings ||--|| payments : "booking_id"</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.8 Kode Deklaratif Mermaid untuk Physical Data Model (PDM) Skema Relasional MySQL 8.0</div>
</div>

<h3 class="subsection-title">2.4.4 Kamus Data (Data Dictionary)</h3>
<p>
    Kamus data menjabarkan spesifikasi metadata fisik dari 15 tabel basis data Booknail secara presisi. Berikut disajikan struktur tabel-tabel utama yang menangani proses inti reservasi, penguncian transaksi, notifikasi Google FCM, dan modul laporan:
</p>

<!-- Tabel Dictionary: bookings -->
<div class="table-container">
    <div class="table-title">Tabel 2.6 Kamus Data Entitas Tabel <code>bookings</code></div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 22%;">Nama Kolom</th>
                <th style="width: 18%;">Tipe Data & Lebar</th>
                <th style="width: 15%;">Atribut Kunci</th>
                <th style="width: 45%;">Keterangan & Logika Validasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>PK, Auto Inc</td>
                <td>Pengidentifikasi unik transaksi pemesanan.</td>
            </tr>
            <tr>
                <td><code>booking_code</code></td>
                <td>VARCHAR(50)</td>
                <td>Unique, Index</td>
                <td>Kode acuan reservasi publik unik (format: <code>BK-YYYYMMDD-XXXX</code>).</td>
            </tr>
            <tr>
                <td><code>user_id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>FK (users.id)</td>
                <td>ID pelanggan yang membuat pemesanan.</td>
            </tr>
            <tr>
                <td><code>nail_artist_id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>FK (nail_artists.id)</td>
                <td>ID teknisi kuku spesialis yang menangani perawatan.</td>
            </tr>
            <tr>
                <td><code>station_id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>FK (stations.id)</td>
                <td>ID meja kerja fisik aktif yang dialokasikan (Station 1 s/d 4).</td>
            </tr>
            <tr>
                <td><code>booking_date</code></td>
                <td>DATE</td>
                <td>Index</td>
                <td>Tanggal kedatangan reservasi.</td>
            </tr>
            <tr>
                <td><code>start_time</code></td>
                <td>TIME</td>
                <td>Index</td>
                <td>Jam mulai sesi perawatan kuku.</td>
            </tr>
            <tr>
                <td><code>end_time</code></td>
                <td>TIME</td>
                <td>Index</td>
                <td>Jam selesai yang terkalkulasi dinamis (\(T_{start} + \\sum D_i + 10\\text{ mnt}\)).</td>
            </tr>
            <tr>
                <td><code>total_price</code></td>
                <td>DECIMAL(12,2)</td>
                <td>-</td>
                <td>Total akumulasi biaya perawatan dalam mata uang Rupiah.</td>
            </tr>
            <tr>
                <td><code>dp_amount</code></td>
                <td>DECIMAL(12,2)</td>
                <td>-</td>
                <td>Nilai kewajiban uang muka 30% (\(\\text{total\\_price} \\times 0{,}30\)).</td>
            </tr>
            <tr>
                <td><code>status</code></td>
                <td>ENUM</td>
                <td>-</td>
                <td><code>pending_payment</code>, <code>confirmed</code>, <code>completed</code>, <code>cancelled</code>.</td>
            </tr>
            <tr>
                <td><code>expires_at</code></td>
                <td>TIMESTAMP</td>
                <td>Index</td>
                <td>Batas waktu toleransi pembayaran DP (60 menit sejak booking dibuat).</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Tabel Dictionary: fcm_tokens -->
<div class="table-container">
    <div class="table-title">Tabel 2.7 Kamus Data Entitas Tabel <code>fcm_tokens</code> (Google Firebase Cloud Messaging)</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 22%;">Nama Kolom</th>
                <th style="width: 18%;">Tipe Data & Lebar</th>
                <th style="width: 15%;">Atribut Kunci</th>
                <th style="width: 45%;">Keterangan & Logika Validasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>PK, Auto Inc</td>
                <td>ID unik rekaman token pendaftaran FCM.</td>
            </tr>
            <tr>
                <td><code>user_id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>FK (users.id)</td>
                <td>ID pengguna pemilik perangkat (terapis, admin, pelanggan).</td>
            </tr>
            <tr>
                <td><code>token</code></td>
                <td>VARCHAR(255)</td>
                <td>Unique, Index</td>
                <td>String registrasi perangkat unik dari Google FCM SDK.</td>
            </tr>
            <tr>
                <td><code>device_type</code></td>
                <td>VARCHAR(100)</td>
                <td>-</td>
                <td>Identifikasi peramban/perangkat (misal: "Chrome on Windows", "Safari iOS").</td>
            </tr>
            <tr>
                <td><code>last_used_at</code></td>
                <td>TIMESTAMP</td>
                <td>-</td>
                <td>Waktu terakhir token berhasil menerima paket transmisi push notification.</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Tabel Dictionary: services -->
<div class="table-container">
    <div class="table-title">Tabel 2.8 Kamus Data Entitas Tabel <code>services</code></div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 22%;">Nama Kolom</th>
                <th style="width: 18%;">Tipe Data & Lebar</th>
                <th style="width: 15%;">Atribut Kunci</th>
                <th style="width: 45%;">Keterangan & Logika Validasi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><code>id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>PK, Auto Inc</td>
                <td>ID unik perlakuan layanan kuku.</td>
            </tr>
            <tr>
                <td><code>category_id</code></td>
                <td>BIGINT UNSIGNED</td>
                <td>FK (categories.id)</td>
                <td>ID pengelompokan kategori (Manicure, Extension, Nail Art).</td>
            </tr>
            <tr>
                <td><code>name</code></td>
                <td>VARCHAR(255)</td>
                <td>-</td>
                <td>Nama resmi perlakuan (misal: "Gel Extension + 3D Custom Art").</td>
            </tr>
            <tr>
                <td><code>duration_minutes</code></td>
                <td>INT</td>
                <td>-</td>
                <td>Alokasi durasi pengerjaan dalam satuan menit standar.</td>
            </tr>
            <tr>
                <td><code>price</code></td>
                <td>DECIMAL(12,2)</td>
                <td>-</td>
                <td>Tarif resmi layanan dalam satuan Rupiah (IDR).</td>
            </tr>
            <tr>
                <td><code>is_active</code></td>
                <td>TINYINT(1)</td>
                <td>-</td>
                <td>Status ketersediaan layanan pada katalog (1 = Aktif, 0 = Nonaktif).</td>
            </tr>
        </tbody>
    </table>
</div>

<h3 class="subsection-title">2.4.5 Class Diagram</h3>
<p>
    Class Diagram mendefinisikan struktur statis berorientasi objek dari sistem Booknail pada framework Laravel 12. Sesuai ketentuan penelitian, diagram ini disajikan dalam format kode deklaratif standar <strong>PlantUML</strong> pada Gambar 2.9.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode PlantUML: Class Diagram Sistem Informasi Booknail (@startuml ... @enduml)</div>
        <pre><code>@startuml
skinparam classAttributeIconSize 0
skinparam packageStyle rectangle
skinparam shadowing false

package "App\\Models" {
    class User {
        - id: BigInt
        - name: String
        - email: String
        - phone: String
        - role: String
        + bookings(): HasMany
        + nailArtist(): HasOne
        + fcmTokens(): HasMany
    }

    class NailArtist {
        - id: BigInt
        - user_id: BigInt
        - bio: String
        - is_active: Boolean
        + user(): BelongsTo
        + schedules(): HasMany
        + leaves(): HasMany
        + bookings(): HasMany
    }

    class Booking {
        - id: BigInt
        - booking_code: String
        - user_id: BigInt
        - nail_artist_id: BigInt
        - station_id: BigInt
        - booking_date: Date
        - start_time: Time
        - end_time: Time
        - total_price: Decimal
        - dp_amount: Decimal
        - status: String
        + details(): HasMany
        + payment(): HasOne
        + user(): BelongsTo
        + nailArtist(): BelongsTo
        + station(): BelongsTo
    }

    class Service {
        - id: BigInt
        - category_id: BigInt
        - name: String
        - duration_minutes: Integer
        - price: Decimal
        + category(): BelongsTo
        + bookingDetails(): HasMany
    }

    class Payment {
        - id: BigInt
        - booking_id: BigInt
        - amount: Decimal
        - payment_type: String
        - proof_image: String
        - status: String
        + booking(): BelongsTo
    }
}

package "App\\Services" {
    class SlotAvailabilityService {
        + checkArtistSchedule(artistId, date, startTime, endTime): Boolean
        + checkArtistLeave(artistId, date): Boolean
        + checkOverlappingBookings(artistId, date, startTime, endTime): Boolean
        + checkStationCapacity(date, startTime, endTime): Boolean
        + getAvailableSlots(artistId, date, totalDuration): Array
    }

    class FcmService {
        - serverKey: String
        - fcmUrl: String
        + sendPushNotification(deviceTokens, title, body, payload): Boolean
        + subscribeTopic(token, topic): Boolean
    }
}

package "App\\Http\\Controllers" {
    class BookingController {
        - slotService: SlotAvailabilityService
        - fcmService: FcmService
        + wizardStep1(): View
        + wizardStep2(): View
        + getAvailableSlots(Request): JsonResponse
        + store(StoreBookingRequest): RedirectResponse
        + show(Booking): View
    }

    class ReportController {
        + index(Request): View
        + exportPdf(Request): BinaryFileResponse
        + exportCsv(Request): StreamedResponse
    }
}

User "1" -- "0..1" NailArtist : associates >
User "1" -- "0..*" Booking : places >
NailArtist "1" -- "0..*" Booking : performs >
Booking "1" *-- "1..*" Service : includes >
Booking "1" -- "1" Payment : verifies >
BookingController ..> SlotAvailabilityService : uses >
BookingController ..> FcmService : triggers >
ReportController ..> Booking : queries >
@enduml</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.9 Kode Deklaratif PlantUML untuk Class Diagram Sistem Booknail</div>
</div>

<!-- ========================================== -->
<!-- 2.5 PERANCANGAN ANTARMUKA SISTEM -->
<!-- ========================================== -->
<h2 class="section-title">2.5 Perancangan Antarmuka Sistem (User Interface)</h2>

<h3 class="subsection-title">2.5.1 Window Navigation Diagram (WND)</h3>
<p>
    Struktur navigasi antarmuka sistem Booknail dirancang untuk memastikan kenyamanan pengguna (<em>ergonomic user experience</em>) dengan segregasi hierarki akses yang tegas antara pengguna publik, pelanggan terdaftar, tenaga terapis, dan administrator:
</p>
<ul class="academic-list">
    <li><strong>Sitemap Pelanggan:</strong> Beranda Publik &rarr; Katalog Layanan &rarr; Wizard Reservasi Multi-Step (Step 1: Treatment Selection &rarr; Step 2: Artist & Date Selection &rarr; Step 3: Dynamic Time Slot &rarr; Step 4: Review & Notes &rarr; Step 5: Transfer DP & Upload Struk) &rarr; Dashboard Riwayat & Status Invoice &rarr; Form Review & Rating.</li>
    <li><strong>Sitemap Nail Artist:</strong> Login Khusus Staf &rarr; Dashboard Kalender Jadwal Harian &rarr; Real-Time Toast FCM Alert Popup &rarr; Detail Order Perawatan Kuku Pelanggan &rarr; Konfirmasi Kehadiran / Status Treatment Selesai.</li>
    <li><strong>Sitemap Administrator:</strong> Login Admin &rarr; Dashboard Ringkasan Finansial & Operasional &rarr; Verifikasi Pembayaran Masuk &rarr; Manajemen Jam Kerja & Cuti Terapis &rarr; Modul Laporan & Export Data (Filter Tanggal, Status, Terapis &rarr; Unduh PDF / Unduh CSV).</li>
</ul>

<h3 class="subsection-title">2.5.2 Perancangan Antarmuka Publik & Pelanggan</h3>
<p>
    Antarmuka pelanggan dirancang mengadopsi palet warna modern (<em>clean aesthetic</em> dengan sentuhan aksen <em>rose gold</em> dan <em>slate</em>), responsif terhadap resolusi layar gawai (<em>mobile-first</em>). Gambar 2.10 menampilkan antarmuka Langkah 3 pada Wizard Reservasi tempat pelanggan memilih slot waktu dinamis hasil kalkulasi algoritma.
</p>

<div class="figure-container">
    <div class="browser-mockup">
        <div class="browser-header">
            <span class="browser-dot bg-red-400"></span>
            <span class="browser-dot bg-yellow-400"></span>
            <span class="browser-dot bg-green-400"></span>
            <span style="font-size: 8.5pt; color: #64748b; margin-left: 8px;">https://booknail.test/booking/step-3 (Wizard Pemilihan Slot Waktu)</span>
        </div>
        <div class="browser-content" style="background: #ffffff; padding: 18px; font-family: sans-serif;">
            <div style="border-bottom: 2px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0; color: #1e293b; font-size: 13pt;">Langkah 3: Pemilihan Slot Waktu Dinamis</h3>
                    <div style="color: #64748b; font-size: 8.5pt; margin-top: 3px;">Terapis Terpilih: <strong>Maya Permata</strong> | Tanggal: <strong>Sabtu, 14 Juni 2025</strong></div>
                </div>
                <div style="text-align: right;">
                    <span class="badge-primary">Total Durasi: 105 Menit</span>
                    <div style="color: #64748b; font-size: 8pt; margin-top: 3px;">(Termasuk buffer 10 mnt pembersihan)</div>
                </div>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-size: 8.5pt; font-weight: bold; color: #334155; display: block; margin-bottom: 8px;">Pilih Jam Kedatangan yang Tersedia:</label>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                    <div style="border: 2px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center; background: #f8fafc; color: #94a3b8; text-decoration: line-through;">
                        <span style="font-size: 10pt; font-weight: bold;">10:00</span><br>
                        <span style="font-size: 7.5pt;">Sudah Dipesan</span>
                    </div>
                    <div style="border: 2px solid #0284c7; border-radius: 8px; padding: 10px; text-align: center; background: #f0f9ff; color: #0369a1; cursor: pointer; box-shadow: 0 2px 4px rgba(2,132,199,0.15);">
                        <span style="font-size: 10pt; font-weight: bold;">12:00</span><br>
                        <span style="font-size: 7.5pt; color: #0284c7;">Tersedia (Selesai 13:45)</span>
                    </div>
                    <div style="border: 2px solid #cbd5e1; border-radius: 8px; padding: 10px; text-align: center; background: #f8fafc; color: #94a3b8; text-decoration: line-through;">
                        <span style="font-size: 10pt; font-weight: bold;">14:00</span><br>
                        <span style="font-size: 7.5pt;">Bentrok Jadwal</span>
                    </div>
                    <div style="border: 2px solid #0284c7; border-radius: 8px; padding: 10px; text-align: center; background: #f0f9ff; color: #0369a1; cursor: pointer;">
                        <span style="font-size: 10pt; font-weight: bold;">16:00</span><br>
                        <span style="font-size: 7.5pt; color: #0284c7;">Tersedia (Selesai 17:45)</span>
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 15px;">
                <div style="font-weight: bold; font-size: 8.5pt; color: #334155; margin-bottom: 6px;">Ringkasan Biaya & Down Payment (DP):</div>
                <div style="display: flex; justify-content: space-between; font-size: 8.5pt; margin-bottom: 4px;">
                    <span>Gel Extension Full Set + 3D Art:</span>
                    <span>Rp 350.000</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 8.5pt; margin-bottom: 4px;">
                    <span>Express Russian Manicure:</span>
                    <span>Rp 100.000</span>
                </div>
                <div style="border-top: 1px dashed #cbd5e1; margin-top: 6px; padding-top: 6px; display: flex; justify-content: space-between; font-size: 9.5pt; font-weight: bold;">
                    <span>Total Estimasi Biaya:</span>
                    <span>Rp 450.000</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 9.5pt; font-weight: bold; color: #b45309; margin-top: 4px;">
                    <span>Wajib Bayar DP (30%):</span>
                    <span>Rp 135.000</span>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between;">
                <button style="background: #e2e8f0; color: #334155; border: none; padding: 8px 16px; border-radius: 6px; font-size: 8.5pt; font-weight: bold;">&larr; Kembali</button>
                <button style="background: #0284c7; color: #ffffff; border: none; padding: 8px 20px; border-radius: 6px; font-size: 8.5pt; font-weight: bold;">Lanjut ke Konfirmasi Booking &rarr;</button>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.10 Antarmuka Pengguna Multi-Step Wizard Langkah 3: Pemilihan Slot Waktu Dinamis</div>
</div>

<h3 class="subsection-title">2.5.3 Perancangan Antarmuka Nail Artist & FCM Real-Time Alert</h3>
<p>
    Dashboard <em>nail artist</em> dirancang khusus untuk memfasilitasi monitoring jadwal kerja harian teknisi. Dilengkapi dengan pendengar pesan latar belakang Google FCM, antarmuka ini menampilkan <em>toast alert popup</em> dan membunyikan sinyal audio seketika saat pesanan baru terverifikasi, seperti diperlihatkan pada Gambar 2.11.
</p>

<div class="figure-container">
    <div class="browser-mockup">
        <div class="browser-header">
            <span class="browser-dot bg-red-400"></span>
            <span class="browser-dot bg-yellow-400"></span>
            <span class="browser-dot bg-green-400"></span>
            <span style="font-size: 8.5pt; color: #64748b; margin-left: 8px;">https://booknail.test/artist/dashboard (Dashboard Jadwal Terapis)</span>
        </div>
        <div class="browser-content" style="background: #f8fafc; padding: 18px; font-family: sans-serif; position: relative;">
            <!-- FCM Toast Pop-up Simulation -->
            <div style="background: #1e293b; color: #ffffff; border-radius: 8px; padding: 12px 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-left: 4px solid #f97316; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="badge-fcm">&#128276; PUSH NOTIFIKASI FCM REAL-TIME</span>
                        <span style="font-size: 7.5pt; color: #94a3b8;">Baru saja</span>
                    </div>
                    <div style="font-weight: bold; font-size: 9.5pt; margin-top: 4px;">Reservasi Baru Terkonfirmasi! (BK-20250614-0042)</div>
                    <div style="font-size: 8pt; color: #cbd5e1; margin-top: 2px;">Pelanggan: <strong>Jessica Wong</strong> | Pukul: <strong>12:00 - 13:45 WIB</strong> | Station: <strong>#2</strong></div>
                </div>
                <button style="background: #f97316; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; font-size: 8pt; font-weight: bold; cursor: pointer;">Lihat Detail</button>
            </div>

            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 12px;">
                    <div>
                        <h4 style="margin: 0; color: #1e293b; font-size: 11pt;">Antrean Jadwal Layanan Hari Ini</h4>
                        <div style="font-size: 8pt; color: #64748b;">Sabtu, 14 Juni 2025 | Terapis: Maya Permata</div>
                    </div>
                    <span class="badge-success">Status: Aktif Bertugas</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f8fafc; border-radius: 6px; border-left: 3px solid #16a34a;">
                        <div>
                            <strong style="font-size: 8.5pt;">10:00 - 11:30 WIB</strong>
                            <div style="font-size: 8pt; color: #64748b;">Felicia Tan - Russian Manicure + Gel Polish</div>
                        </div>
                        <span class="badge-success">Selesai</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #fff7ed; border-radius: 6px; border-left: 3px solid #f97316;">
                        <div>
                            <strong style="font-size: 8.5pt; color: #c2410c;">12:00 - 13:45 WIB (Mendatang)</strong>
                            <div style="font-size: 8pt; color: #64748b;">Jessica Wong - Gel Extension + Custom 3D Art</div>
                        </div>
                        <button style="background: #0284c7; color: #fff; border: none; padding: 4px 10px; border-radius: 4px; font-size: 7.5pt; font-weight: bold;">Check-In</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.11 Antarmuka Dashboard Terapis dengan Integrasi Pop-up Google FCM Push Notification</div>
</div>

<h3 class="subsection-title">2.5.4 Perancangan Antarmuka Administrator & Modul Export Laporan (PDF / CSV)</h3>
<p>
    Sesuai kebutuhan audit manajerial studio, antarmuka <code>/admin/reports</code> dilengkapi fitur penyaringan multi-parameter serta tombol ekspor ganda ke dokumen PDF berformat cetak resmi dan dokumen CSV terstruktur untuk pengolahan spreadsheet, seperti disajikan pada Gambar 2.12.
</p>

<div class="figure-container">
    <div class="browser-mockup">
        <div class="browser-header">
            <span class="browser-dot bg-red-400"></span>
            <span class="browser-dot bg-yellow-400"></span>
            <span class="browser-dot bg-green-400"></span>
            <span style="font-size: 8.5pt; color: #64748b; margin-left: 8px;">https://booknail.test/admin/reports (Modul Laporan Reservasi & Export Data)</span>
        </div>
        <div class="browser-content" style="background: #ffffff; padding: 18px; font-family: sans-serif;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div>
                    <h3 style="margin: 0; color: #1e293b; font-size: 13pt;">Laporan Reservasi & Pendapatan Studio</h3>
                    <div style="color: #64748b; font-size: 8.5pt;">Pusat Analisis Finansial dan Utilisasi Terapis Nail Glam Studio</div>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button style="background: #dc2626; color: #ffffff; border: none; padding: 8px 14px; border-radius: 6px; font-size: 8.5pt; font-weight: bold; display: flex; align-items: center; gap: 4px;">
                        <span>&#128196;</span> Export PDF
                    </button>
                    <button style="background: #16a34a; color: #ffffff; border: none; padding: 8px 14px; border-radius: 6px; font-size: 8.5pt; font-weight: bold; display: flex; align-items: center; gap: 4px;">
                        <span>&#128202;</span> Export CSV (Excel)
                    </button>
                </div>
            </div>

            <!-- Filter Controls -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 15px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; font-size: 8pt;">
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 4px;">Dari Tanggal:</label>
                    <input type="text" value="2025-06-01" style="width: 90%; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 4px;" readonly>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 4px;">Sampai Tanggal:</label>
                    <input type="text" value="2025-06-30" style="width: 90%; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 4px;" readonly>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 4px;">Status Reservasi:</label>
                    <select style="width: 90%; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option>Semua Status Selesai / Terkonfirmasi</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 4px;">Pilih Terapis:</label>
                    <select style="width: 90%; padding: 4px 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option>Semua Nail Artist (4 Orang)</option>
                    </select>
                </div>
            </div>

            <!-- Summary KPI Widgets -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 15px;">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 10px;">
                    <div style="color: #1e40af; font-size: 7.5pt; font-weight: bold;">TOTAL TRANSAKSI</div>
                    <div style="font-size: 14pt; font-weight: bold; color: #1e3a8a; margin-top: 2px;">148 Sesi</div>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 10px;">
                    <div style="color: #166534; font-size: 7.5pt; font-weight: bold;">TOTAL PENDAPATAN KOTOR</div>
                    <div style="font-size: 14pt; font-weight: bold; color: #14532d; margin-top: 2px;">Rp 41.850.000</div>
                </div>
                <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 6px; padding: 10px;">
                    <div style="color: #9d174d; font-size: 7.5pt; font-weight: bold;">RATA-RATA DURASI / SESI</div>
                    <div style="font-size: 14pt; font-weight: bold; color: #831843; margin-top: 2px;">78 Menit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 2.12 Antarmuka Modul Laporan Administrator dengan Fitur Ekspor PDF dan CSV</div>
</div>

<!-- ========================================== -->
<!-- 2.6 PERANCANGAN IMPLEMENTASI KODE -->
<!-- ========================================== -->
<h2 class="section-title">2.6 Perancangan Implementasi Kode & Logika Bisnis</h2>

<h3 class="subsection-title">2.6.1 Arsitektur Direktori MVC Laravel 12</h3>
<p>
    Struktur kode sumber sistem Booknail diorganisasikan secara modular mengikuti standar kerangka kerja Laravel 12. Logika komputasional alokasi waktu dan perpesanan dipisahkan ke dalam lapisan layanan (<em>Service Layer</em> di bawah <code>app/Services/</code>), menghindari penumpukan logika pada controller (<em>Fat Controller anti-pattern</em>):
</p>
<pre style="background: #0f172a; color: #f8fafc; padding: 12px; border-radius: 6px; font-family: 'Consolas', monospace; font-size: 8pt; line-height: 1.4;">
c:\Users\richw\Herd\Booknail\
├── app/
│   ├── Http/Controllers/
│   │   ├── BookingController.php          # Manajemen alur wizard dan invoice
│   │   └── Admin/
│   │       ├── BookingController.php      # Verifikasi pembayaran DP
│   │       └── ReportController.php       # Modul ekspor PDF & CSV
│   ├── Models/
│   │   ├── Booking.php, Service.php, NailArtist.php, FcmToken.php
│   ├── Services/
│   │   ├── SlotAvailabilityService.php    # Algoritma dynamic time-slot & overlap check
│   │   └── FcmService.php                 # Web push dispatching via Google FCM
│   └── Jobs/
│       └── SendFcmJob.php                 # Background worker antrean pengiriman notifikasi
├── public/
│   └── firebase-messaging-sw.js           # W3C Service Worker pendengar push di latar belakang
└── resources/views/
    ├── booking/ (step-1 s/d step-5)
    ├── admin/reports/ (index.blade.php & pdf.blade.php)
    └── artist/dashboard.blade.php
</pre>

<h3 class="subsection-title">2.6.2 Logika Algoritma SlotAvailabilityService</h3>
<p>
    Algoritma <code>SlotAvailabilityService</code> mengeksekusi tiga lapis pemeriksaan sebelum mengembalikan status ketersediaan slot: validasi jam operasional shift kerja, pengecekan jadwal cuti yang disetujui, dan pengecekan tumpang tindih waktu dengan reservasi eksisting. Kode implementasi inti disajikan pada Gambar 2.13.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode PHP: Implementasi Logika SlotAvailabilityService.php</div>
        <pre><code>public function checkArtistSchedule(int $artistId, string $date, string $start, string $end): bool
{
    $dayOfWeek = Carbon::parse($date)->dayOfWeek; // 0 = Sunday, 6 = Saturday
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
    // Evaluasi tumpang-tindih: (StartA < EndB) AND (EndA > StartB)
    return Booking::where('nail_artist_id', $artistId)
        ->where('booking_date', $date)
        ->whereIn('status', ['confirmed', 'completed', 'pending_payment'])
        ->where('start_time', '<', $end)
        ->where('end_time', '>', $start)
        ->exists();
}</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.13 Kode Sumber Implementasi Logika Verifikasi Ketersediaan Slot Waktu pada Laravel</div>
</div>

<h3 class="subsection-title">2.6.3 Logika Pessimistic Locking & Transaksi Terisolasi</h3>
<p>
    Untuk mencegah <em>double booking</em> pada tingkat basis data, pembuatan rekaman reservasi dibungkus dalam blok <code>DB::transaction</code> yang memanggil metode <code>lockForUpdate()</code>. Kode implementasi disajikan pada Gambar 2.14.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode PHP: Penerimaan Transaksi dengan Pessimistic Locking (lockForUpdate)</div>
        <pre><code>public function store(StoreBookingRequest $request)
{
    return DB::transaction(function () use ($request) {
        // 1. Mengunci baris jadwal & bookings yang berpotensi bentrok
        $overlap = Booking::where('nail_artist_id', $request->nail_artist_id)
            ->where('booking_date', $request->booking_date)
            ->whereIn('status', ['confirmed', 'pending_payment'])
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->lockForUpdate()
            ->exists();

        if ($overlap) {
            throw new SlotAlreadyBookedException("Slot waktu terpilih baru saja diambil pengguna lain.");
        }

        // 2. Alokasikan physical station yang kosong
        $usedStations = Booking::where('booking_date', $request->booking_date)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->whereIn('status', ['confirmed', 'pending_payment'])
            ->pluck('station_id');

        $freeStation = Station::whereNotIn('id', $usedStations)->first();
        if (!$freeStation) {
            throw new StudioCapacityExceededException("Kapasitas meja kerja studio penuh.");
        }

        // 3. Simpan data reservasi dengan toleransi bayar DP 60 menit
        return Booking::create([
            'booking_code'    => 'BK-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
            'user_id'         => auth()->id(),
            'nail_artist_id'  => $request->nail_artist_id,
            'station_id'      => $freeStation->id,
            'booking_date'    => $request->booking_date,
            'start_time'      => $request->start_time,
            'end_time'        => $request->end_time,
            'total_price'     => $request->total_price,
            'dp_amount'       => $request->total_price * 0.30,
            'status'          => 'pending_payment',
            'expires_at'      => now()->addMinutes(60),
        ]);
    });
}</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.14 Kode Sumber Pencegahan Race Condition dengan DB::transaction dan lockForUpdate()</div>
</div>

<h3 class="subsection-title">2.6.4 Logika FcmService & Integrasi Web Push Service Worker</h3>
<p>
    Pengiriman notifikasi instan ke peramban ditangani oleh modul <code>FcmService</code>. Modul ini menyusun muatan JSON berstandar WebPush dan mengeksekusi panggilan HTTPS POST ke Google FCM endpoint:
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode PHP: FcmService.php untuk Pengiriman Push Notification ke Google FCM Gateway</div>
        <pre><code>public function sendPushNotification(array $deviceTokens, string $title, string $body, array $payload = []): bool
{
    $serverKey = config('services.firebase.server_key');
    $data = [
        'registration_ids' => $deviceTokens,
        'notification' => [
            'title' => $title,
            'body'  => $body,
            'icon'  => asset('images/icons/icon-192x192.png'),
            'sound' => 'default',
        ],
        'data' => $payload,
    ];

    $response = Http::withHeaders([
        'Authorization' => 'key=' . $serverKey,
        'Content-Type'  => 'application/json',
    ])->post('https://fcm.googleapis.com/fcm/send', $data);

    return $response->successful();
}</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.15 Kode Sumber Pengiriman Muatan Pesan Real-Time Menggunakan FcmService</div>
</div>

<p>
    Di sisi klien, berkas <code>firebase-messaging-sw.js</code> menangkap pesan saat aplikasi diminimalkan dan merender notifikasi sistem operasi:
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode JavaScript: public/firebase-messaging-sw.js (Service Worker Web Push)</div>
        <pre><code>importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSy...",
    projectId: "booknail-untarlab",
    messagingSenderId: "10859421...",
    appId: "1:10859421...:web:8a9b..."
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        data: payload.data
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.16 Kode Sumber Service Worker Pendengar Pesan Push Google FCM di Sisi Klien</div>
</div>

<h3 class="subsection-title">2.6.5 Logika Modul ReportController (Export PDF & CSV)</h3>
<p>
    Implementasi ekspor pada <code>ReportController</code> menyediakan dua metode keluaran: <code>exportPdf()</code> yang memanfaatkan DomPDF untuk mencetak berkas PDF lanskap, serta <code>exportCsv()</code> yang membangkitkan <em>StreamedResponse</em> dengan injeksi UTF-8 BOM agar kompatibel dengan Microsoft Excel tanpa masalah pemotongan karakter atau kerusakan desimal.
</p>

<div class="figure-container">
    <div class="code-box">
        <div class="code-box-header">Kode PHP: Admin/ReportController.php untuk Ekspor PDF (DomPDF) dan CSV UTF-8 BOM</div>
        <pre><code>public function exportPdf(Request $request)
{
    $bookings = $this->queryBookings($request)->get();
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', [
        'bookings'  => $bookings,
        'dateFrom'  => $request->date_from,
        'dateTo'    => $request->date_to,
        'totalRev'  => $bookings->sum('total_price'),
    ])->setPaper('a4', 'landscape');

    return $pdf->download('laporan-reservasi-' . date('Ymd-His') . '.pdf');
}

public function exportCsv(Request $request)
{
    $bookings = $this->queryBookings($request)->get();
    $filename = 'laporan-reservasi-' . date('Ymd-His') . '.csv';

    return response()->stream(function () use ($bookings) {
        $handle = fopen('php://output', 'w');
        // Injeksi UTF-8 BOM agar terbaca sempurna di Microsoft Excel Windows
        fputs($handle, "\xEF\xBB\xBF");
        
        // Baris Header CSV
        fputcsv($handle, ['Kode Booking', 'Tanggal', 'Jam Mulai', 'Jam Selesai', 'Pelanggan', 'Nail Artist', 'Station', 'Total Biaya', 'DP (30%)', 'Status']);

        foreach ($bookings as $b) {
            fputcsv($handle, [
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
}</code></pre>
    </div>
    <div class="figure-caption">Gambar 2.17 Kode Sumber Pembuatan Laporan Ekspor PDF dan CSV pada ReportController</div>
</div>
"""
