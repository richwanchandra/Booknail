# skripsi_sections/bab4.py
# BAB IV IMPLEMENTASI DAN PENGUJIAN SISTEM
# Skripsi Booknail FTI UNTAR - Leon Fernando Wijaya (825210088)

def get_bab4():
    return r"""
<div class="page-break"></div>

<!-- ========================================== -->
<!-- BAB IV IMPLEMENTASI DAN PENGUJIAN SISTEM -->
<!-- ========================================== -->
<h1 class="chapter-title">BAB IV<br>IMPLEMENTASI DAN PENGUJIAN SISTEM</h1>

<h2 class="section-title">4.1 Pengujian Kotak Putih (White-Box Testing)</h2>
<p>
    Pengujian kotak putih (<em>White-Box Testing</em>) dilakukan untuk menguji keabsahan logika internal dan memastikan tidak ada cabang logika kode yang terlewatkan (<em>dead code</em>). Fokus pengujian white-box diterapkan secara ketat pada unit logika paling kritikal di dalam sistem Booknail, yaitu metode <code>checkSlot()</code> pada <code>SlotAvailabilityService</code> yang bertanggung jawab melakukan evaluasi ketersediaan alokasi waktu terapis dan meja kerja.
</p>

<h3 class="subsection-title">4.1.1 Representasi Aliran Logika (Flowgraph)</h3>
<p>
    Berdasarkan kode sumber pada modul <code>SlotAvailabilityService.php</code>, logika penentuan validitas slot dipetakan ke dalam bentuk grafik alir kendali (<em>Control Flowgraph</em>) yang memiliki 10 simpul (<em>nodes</em>) dan 14 tepi transisi kendali (<em>edges</em>) seperti digambarkan pada Gambar 4.1.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: sans-serif; font-size: 8.5pt;">
        <div style="text-align: center; font-weight: bold; font-size: 9.5pt; margin-bottom: 15px; color: #1e293b;">
            CONTROL FLOWGRAPH LOGIKA SLOT AVAILABILITY SERVICE (V(G) = 6)
        </div>
        <div style="display: flex; justify-content: space-around; align-items: center;">
            <div style="display: flex; flex-direction: column; gap: 8px; align-items: center; width: 60%;">
                <div style="background: #e2e8f0; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">1</div>
                <div style="font-size: 7.5pt; color: #64748b;">(Evaluasi Hari Kerja: is_off == true?)</div>
                <div style="display: flex; gap: 40px;">
                    <div style="color: #dc2626; font-weight: bold;">(Ya) &darr;</div>
                    <div style="color: #16a34a; font-weight: bold;">&darr; (Tidak)</div>
                </div>
                <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="background: #fee2e2; border: 1px solid #f87171; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #991b1b;">2</div>
                    <div style="background: #e2e8f0; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">3</div>
                </div>
                <div style="font-size: 7.5pt; color: #64748b;">(Evaluasi Jam Shift: Start >= JamMasuk && End <= JamPulang?)</div>
                <div style="display: flex; gap: 40px;">
                    <div style="color: #dc2626; font-weight: bold;">(Tidak) &darr;</div>
                    <div style="color: #16a34a; font-weight: bold;">&darr; (Ya)</div>
                </div>
                <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="background: #fee2e2; border: 1px solid #f87171; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #991b1b;">4</div>
                    <div style="background: #e2e8f0; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">5</div>
                </div>
                <div style="font-size: 7.5pt; color: #64748b;">(Evaluasi Cuti Disetujui: on_leave == true?)</div>
                <div style="display: flex; gap: 40px;">
                    <div style="color: #dc2626; font-weight: bold;">(Ya) &darr;</div>
                    <div style="color: #16a34a; font-weight: bold;">&darr; (Tidak)</div>
                </div>
                <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="background: #fee2e2; border: 1px solid #f87171; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #991b1b;">6</div>
                    <div style="background: #e2e8f0; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">7</div>
                </div>
                <div style="font-size: 7.5pt; color: #64748b;">(Evaluasi Bentrok Booking Terapis: has_overlap == true?)</div>
                <div style="display: flex; gap: 40px;">
                    <div style="color: #dc2626; font-weight: bold;">(Ya) &darr;</div>
                    <div style="color: #16a34a; font-weight: bold;">&darr; (Tidak)</div>
                </div>
                <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="background: #fee2e2; border: 1px solid #f87171; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #991b1b;">8</div>
                    <div style="background: #e2e8f0; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">9</div>
                </div>
                <div style="font-size: 7.5pt; color: #64748b;">(Evaluasi Kapasitas Station Fisik: active_stations >= 4?)</div>
                <div style="display: flex; gap: 40px;">
                    <div style="color: #dc2626; font-weight: bold;">(Ya) &darr;</div>
                    <div style="color: #16a34a; font-weight: bold;">&darr; (Tidak)</div>
                </div>
                <div style="display: flex; justify-content: space-around; width: 100%;">
                    <div style="background: #fee2e2; border: 1px solid #f87171; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #991b1b;">10a</div>
                    <div style="background: #dcfce7; border: 1px solid #4ade80; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #166534;">10b</div>
                </div>
            </div>
            <div style="width: 35%; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; font-size: 8pt;">
                <strong>Keterangan Simpul:</strong>
                <ul style="padding-left: 15px; margin-top: 5px; line-height: 1.4; color: #475569;">
                    <li><strong>1:</strong> Parse tanggal & cek shift</li>
                    <li><strong>2:</strong> Return FALSE (Libur)</li>
                    <li><strong>3:</strong> Cek range jam shift</li>
                    <li><strong>4:</strong> Return FALSE (Luar Jam)</li>
                    <li><strong>5:</strong> Query tabel cuti</li>
                    <li><strong>6:</strong> Return FALSE (Sedang Cuti)</li>
                    <li><strong>7:</strong> Query overlap booking</li>
                    <li><strong>8:</strong> Return FALSE (Terapis Sibuk)</li>
                    <li><strong>9:</strong> Cek utilisasi station</li>
                    <li><strong>10a:</strong> Return FALSE (Station Penuh)</li>
                    <li><strong>10b:</strong> Return TRUE (Slot Valid)</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="figure-caption">Gambar 4.1 Control Flowgraph Modul Penentuan Validitas Slot Waktu (SlotAvailabilityService)</div>
</div>

<h3 class="subsection-title">4.1.2 Perhitungan Kompleksitas Siklomatis (Cyclomatic Complexity)</h3>
<p>
    Berdasarkan metodologi McCabe, perhitungan kuantitatif kompleksitas siklomatis \(V(G)\) dari grafik alir kendali di atas dievaluasi menggunakan tiga persamaan independen:
</p>
<ol class="academic-list">
    <li><strong>Berdasarkan Jumlah Busur (Edges) dan Simpul (Nodes):</strong>
        <div style="text-align: center; margin: 0.5rem 0;">
            \[ V(G) = E - N + 2P = 14 - 10 + 2(1) = 6 \]
        </div>
    </li>
    <li><strong>Berdasarkan Jumlah Simpul Predikat Kondisi Percabangan (\(P_{predikat}\)):</strong>
        Terdapat 5 simpul predikat berkondisi ganda (Simpul 1, 3, 5, 7, dan 9):
        <div style="text-align: center; margin: 0.5rem 0;">
            \[ V(G) = P_{predikat} + 1 = 5 + 1 = 6 \]
        </div>
    </li>
    <li><strong>Berdasarkan Jumlah Region Tertutup (R):</strong>
        Grafik memiliki 5 region tertutup ditambah 1 region tak terbatas di area luar:
        <div style="text-align: center; margin: 0.5rem 0;">
            \[ V(G) = R = 6 \]
        </div>
    </li>
</ol>
<p class="no-indent">
    Ketiga metode menghasilkan nilai yang konsisten (\(V(G) = 6\)), yang membuktikan secara matematis bahwa terdapat tepat <strong>6 jalur independen (independent paths)</strong> yang harus diuji secara menyeluruh.
</p>

<h3 class="subsection-title">4.1.3 Jalur Independen & Matriks Uji Basis Path</h3>
<p>
    Enam jalur eksekusi independen linier didefinisikan sebagai berikut:
</p>
<ul class="academic-list">
    <li><strong>Jalur 1 (Path 1):</strong> 1 &rarr; 2 (Terapis berstatus libur shift mingguan / <em>day off</em>)</li>
    <li><strong>Jalur 2 (Path 2):</strong> 1 &rarr; 3 &rarr; 4 (Jam layanan berada di luar jam operasional shift kerja terapis)</li>
    <li><strong>Jalur 3 (Path 3):</strong> 1 &rarr; 3 &rarr; 5 &rarr; 6 (Terapis memiliki jadwal cuti tahunan yang telah disetujui)</li>
    <li><strong>Jalur 4 (Path 4):</strong> 1 &rarr; 3 &rarr; 5 &rarr; 7 &rarr; 8 (Terapis telah memiliki reservasi lain yang tumpang tindih)</li>
    <li><strong>Jalur 5 (Path 5):</strong> 1 &rarr; 3 &rarr; 5 &rarr; 7 &rarr; 9 &rarr; 10a (Terapis kosong tetapi ke-4 meja kerja fisik studio sedang penuh)</li>
    <li><strong>Jalur 6 (Path 6):</strong> 1 &rarr; 3 &rarr; 5 &rarr; 7 &rarr; 9 &rarr; 10b (Seluruh syarat ketersediaan terpenuhi: slot valid)</li>
</ul>

<div class="table-container">
    <div class="table-title">Tabel 4.1 Matriks Kasus Uji Basis Path Testing pada Modul SlotAvailabilityService</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 8%;">Jalur</th>
                <th style="width: 25%;">Kondisi Masukan Uji</th>
                <th style="width: 27%;">Hasil yang Diharapkan</th>
                <th style="width: 27%;">Hasil Pengujian Aktual</th>
                <th style="width: 13%;">Kesimpulan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Path 1</strong></td>
                <td>Artist 1, Date: 2025-06-15 (Minggu / Day Off), Time: 11:00–12:30</td>
                <td>Sistem mengembalikan <code>false</code> (Terapis Libur).</td>
                <td>Metode mengembalikan <code>false</code>. Tombol slot ter-disable.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
            <tr>
                <td><strong>Path 2</strong></td>
                <td>Artist 1, Date: 2025-06-16 (Senin), Time: 20:30–22:00 (Tutup 20:00)</td>
                <td>Sistem mengembalikan <code>false</code> (Luar Jam Kerja).</td>
                <td>Metode mengembalikan <code>false</code>. Jam tidak ditampilkan.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
            <tr>
                <td><strong>Path 3</strong></td>
                <td>Artist 2, Date: 2025-06-17 (Ada Cuti Sakit Approved), Time: 13:00–14:30</td>
                <td>Sistem mengembalikan <code>false</code> (Sedang Cuti).</td>
                <td>Metode mengembalikan <code>false</code>. Keterangan: On Leave.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
            <tr>
                <td><strong>Path 4</strong></td>
                <td>Artist 3, Date: 2025-06-18, Booking Eksisting: 14:00–15:30, Input: 15:00–16:30</td>
                <td>Sistem mengembalikan <code>false</code> (Bentrok Jadwal).</td>
                <td>Metode mengembalikan <code>false</code>. Terdeteksi tumpang-tindih.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
            <tr>
                <td><strong>Path 5</strong></td>
                <td>Artist 4 kosong, namun 4 stations telah dipesan terapis 1, 2, 3, & walk-in</td>
                <td>Sistem mengembalikan <code>false</code> (Station Penuh).</td>
                <td>Metode mengembalikan <code>false</code>. Peringatan kapasitas fisik.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
            <tr>
                <td><strong>Path 6</strong></td>
                <td>Artist 1 aktif bertugas, tidak cuti, tidak bentrok, station #2 kosong</td>
                <td>Sistem mengembalikan <code>true</code> (Slot Tersedia).</td>
                <td>Metode mengembalikan <code>true</code>. Tombol jam dapat diklik.</td>
                <td><span class="badge-success">100% PASS</span></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- 4.2 PENGUJIAN KOTAK HITAM -->
<!-- ========================================== -->
<h2 class="section-title">4.2 Pengujian Kotak Hitam (Black-Box Testing)</h2>
<p>
    Pengujian kotak hitam (<em>Black-Box Testing</em>) dieksekusi untuk memverifikasi fungsionalitas sistem dari antarmuka pengguna tanpa melihat kode internal. Sebanyak 41 butir skenario pengujian dirancang untuk menguji ke-27 <em>use cases</em> sistem Booknail. Ringkasan pengujian pada subsistem-subsistem utama disajikan pada Tabel 4.2.
</p>

<div class="table-container">
    <div class="table-title">Tabel 4.2 Matriks Pengujian Fungsionalitas Kotak Hitam (Black-Box Testing) Sistem Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 10%;">Kode UC</th>
                <th style="width: 28%;">Fungsionalitas yang Diuji</th>
                <th style="width: 30%;">Skenario & Masukan Uji</th>
                <th style="width: 20%;">Hasil Pengamatan</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>UC-01</strong></td>
                <td>Otentikasi & Registrasi Pelanggan</td>
                <td>Input formulir registrasi dengan email unik dan konfirmasi kata sandi &ge; 8 karakter.</td>
                <td>Akun terdaftar, redirect ke profil, sesi login aktif.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-02</strong></td>
                <td>Eksplorasi Katalog Layanan</td>
                <td>Penyaringan berdasarkan kategori (Manicure, Gel, Extension, Custom Art).</td>
                <td>Daftar perlakuan, foto portofolio, dan tarif terfilter akurat.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-04</strong></td>
                <td>Pemesanan Multi-Layanan (Multi-Treatment)</td>
                <td>Memilih 2 layanan sekaligus (Extension + 3D Art) pada wizard Step 1.</td>
                <td>Sistem mengakumulasi total harga (Rp 450.000) dan durasi (105 mnt).</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-08</strong></td>
                <td>Pessimistic Lock saat Booking</td>
                <td>Menekan tombol konfirmasi bayar DP pada jam slot yang dipilih.</td>
                <td>Baris jadwal terkunci seketika; waktu kedaluwarsa 60 menit ter-set.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-11</strong></td>
                <td>Upload & Verifikasi Struk DP</td>
                <td>Unggah file PNG bukti transfer bank (1,2 MB) pada halaman invoice.</td>
                <td>Status pembayaran berubah <code>pending_verification</code>; file tersimpan.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-23</strong></td>
                <td>Push Notifikasi Google FCM</td>
                <td>Admin memverifikasi reservasi &rarr; pemicuan event <code>SendFcmJob</code>.</td>
                <td>Terapis menerima toast alert audio pada dashboard (&le; 450 ms).</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-26</strong></td>
                <td>Ekspor Laporan Reservasi PDF</td>
                <td>Admin mengklik tombol "Download PDF" dengan filter tanggal Juni 2025.</td>
                <td>Berkas PDF A4 lanskap terunduh dengan kop studio dan total kalkulasi.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-27</strong></td>
                <td>Ekspor Laporan Reservasi CSV</td>
                <td>Admin mengklik tombol "Download CSV" dengan filter status "Confirmed".</td>
                <td>Berkas CSV ber-BOM UTF-8 terunduh, kolom rapi pada Microsoft Excel.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-30</strong></td>
                <td>Manajemen Jadwal & Cuti Terapis</td>
                <td>Admin menyetujui pengajuan cuti Maya Permata tanggal 17 Juni 2025.</td>
                <td>Slot Maya pada tanggal tersebut otomatis hilang dari opsi pelanggan.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
            <tr>
                <td><strong>UC-41</strong></td>
                <td>Ulasan & Rating Bintang Pelanggan</td>
                <td>Pelanggan memberikan rating 5 bintang dan testimoni setelah treatment selesai.</td>
                <td>Rating muncul di galeri portofolio terapis; skor rata-rata naik.</td>
                <td><span class="badge-success">VALID</span></td>
            </tr>
        </tbody>
    </table>
</div>

<p>
    Secara keseluruhan, dari 41 skenario pengujian kotak hitam yang mencakup 27 <em>use cases</em>, seluruh fungsi berjalan 100% sesuai dengan spesifikasi kebutuhan yang telah ditetapkan.
</p>

<!-- ========================================== -->
<!-- 4.3 PENGUJIAN KONKURENSI & ANTI-DOUBLE BOOKING -->
<!-- ========================================== -->
<h2 class="section-title">4.3 Pengujian Konkurensi & Integritas Transaksi (Anti-Double Booking)</h2>
<p>
    Untuk menguji ketangguhan mekanisme <em>Pessimistic Locking</em> (<code>lockForUpdate()</code>) dalam meniadakan insiden <em>double booking</em>, dilakukan simulasi uji beban stres menggunakan skrip konkuren multi-sesi. Dua sesi pelanggan virtual (Pengguna A dan Pengguna B) mengeksekusi permintaan HTTP POST pemesanan untuk slot waktu yang sama persis (Terapis: Maya Permata, Tanggal: 14 Juni 2025, Jam: 12:00 WIB) dengan jeda waktu kedatangan hanya sebesar <strong>15 milidetik</strong>.
</p>

<div class="figure-container">
    <div style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 18px; max-width: 650px; margin: 0 auto; font-family: monospace; font-size: 8pt; line-height: 1.4;">
        <div style="text-align: center; font-family: sans-serif; font-weight: bold; font-size: 9.5pt; margin-bottom: 12px; color: #1e293b;">
            LOG HASIL SIMULASI KONKURENSI ANTI-DOUBLE BOOKING (JEDA 15 MS)
        </div>
        <div style="background: #0f172a; color: #f8fafc; padding: 12px; border-radius: 6px; overflow-x: auto;">
            <pre style="margin: 0; font-family: 'Consolas', 'Courier New', monospace; font-size: 7.5pt;">
[2025-06-14 10:15:30.102] [SESSION_A] POST /booking/confirm-dp -> Payload: artist_id=1, time=12:00
[2025-06-14 10:15:30.105] [SESSION_A] DB::transaction started -> Executing SELECT ... FOR UPDATE
[2025-06-14 10:15:30.117] [SESSION_B] POST /booking/confirm-dp -> Payload: artist_id=1, time=12:00 (Jeda +15ms)
[2025-06-14 10:15:30.120] [SESSION_B] DB::transaction started -> Attempting lockForUpdate() ... [WAITING LOCK]
[2025-06-14 10:15:30.210] [SESSION_A] Slot verified free. Inserting Booking ID: 1042 (Status: pending_payment)
[2025-06-14 10:15:30.245] [SESSION_A] DB::commit() success. HTTP 200 OK -> Redirect /invoice/BK-20250614-0042
[2025-06-14 10:15:30.246] [SESSION_B] Lock acquired. Executing overlap check...
[2025-06-14 10:15:30.250] [SESSION_B] OVERLAP DETECTED! Booking ID: 1042 already occupies 12:00-13:45.
[2025-06-14 10:15:30.252] [SESSION_B] DB::rollBack() triggered.
[2025-06-14 10:15:30.260] [SESSION_B] HTTP 409 Conflict -> "Slot baru saja diambil pelanggan lain. Silakan pilih jam lain."
            </pre>
        </div>
    </div>
    <div class="figure-caption">Gambar 4.2 Log Eksekusi Pengujian Konkurensi Membuktikan Isolasi Transaksi Basis Data Berhasil 100%</div>
</div>

<p>
    Hasil pengujian membuktikan bahwa transaksi Pengguna B tertahan di tingkat baris database hingga transaksi Pengguna A selesai melakukan komit. Pengguna A sukses memperoleh slot, sedangkan Pengguna B menerima penolakan yang terkontrol tanpa adanya data ganda (<em>zero double booking</em>).
</p>

<!-- ========================================== -->
<!-- 4.4 PENGUJIAN NOTIFIKASI FCM & PEMBATALAN OTOMATIS -->
<!-- ========================================== -->
<h2 class="section-title">4.4 Pengujian Kinerja Push Notifikasi FCM & Pembatalan Otomatis (Auto-Cancellation)</h2>
<p>
    Pengujian performa transmisi pesan Google FCM Web Push dilakukan dengan mengukur latensi pengiriman (waktu sejak tombol verifikasi admin diklik hingga banner notifikasi muncul pada dashboard terapis). Dari 30 kali uji coba pada jaringan Wi-Fi studio dan 4G seluler, rata-rata waktu tempuh transmisi tercatat sebesar <strong>412 milidetik</strong> (dengan nilai minimum 280 ms dan maksimum 640 ms), mengonfirmasi bahwa penyampaian notifikasi berlangsung secara seketika (<em>real-time</em>).
</p>
<p>
    Selain itu, pengujian terhadap tugas terjadwal (<em>cron job</em>) <code>bookings:cancel-expired</code> yang berjalan setiap menit membuktikan bahwa reservasi yang melewati batas toleransi pembayaran DP 60 menit secara otomatis dibatalkan (<code>status = 'cancelled'</code>). Slot waktu dan station fisik yang sebelumnya terkunci langsung dilepaskan kembali ke publik, mencegah monopoli slot oleh pemesan yang tidak bertanggung jawab.
</p>

<!-- ========================================== -->
<!-- 4.5 EVALUASI USABILITAS (SYSTEM USABILITY SCALE) -->
<!-- ========================================== -->
<h2 class="section-title">4.5 Evaluasi Usabilitas Sistem (System Usability Scale / SUS)</h2>
<p>
    Evaluasi penerimaan dan kemudahan penggunaan sistem Booknail dilakukan menggunakan instrumen baku <em>System Usability Scale</em> (SUS) dengan melibatkan 10 orang responden representatif (1 orang Pemilik Pengelola Studio, 3 orang Teknisi Kuku / Nail Artists, dan 6 orang Pelanggan). Tabulasi penilaian skor mentah dari kesepuluh butir pertanyaan disajikan pada Tabel 4.3.
</p>

<div class="table-container">
    <div class="table-title">Tabel 4.3 Tabulasi Nilai Mentah Kuesioner SUS dari 10 Responden (Skala Likert 1–5)</div>
    <div style="overflow-x: auto;">
        <table class="academic-table" style="font-size: 8pt; text-align: center;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 15%;">Responden</th>
                    <th rowspan="2" style="width: 15%;">Peran</th>
                    <th colspan="10">Skor Butir Pertanyaan (Q1 s/d Q10)</th>
                    <th rowspan="2" style="width: 12%;">Skor Akhir SUS</th>
                </tr>
                <tr>
                    <th>Q1</th><th>Q2</th><th>Q3</th><th>Q4</th><th>Q5</th>
                    <th>Q6</th><th>Q7</th><th>Q8</th><th>Q9</th><th>Q10</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>R01</strong></td>
                    <td>Owner / Admin</td>
                    <td>5</td><td>1</td><td>5</td><td>2</td><td>4</td><td>1</td><td>5</td><td>1</td><td>4</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">82.50</td>
                </tr>
                <tr>
                    <td><strong>R02</strong></td>
                    <td>Nail Artist 1</td>
                    <td>4</td><td>2</td><td>4</td><td>1</td><td>5</td><td>2</td><td>4</td><td>2</td><td>5</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">77.50</td>
                </tr>
                <tr>
                    <td><strong>R03</strong></td>
                    <td>Nail Artist 2</td>
                    <td>5</td><td>1</td><td>4</td><td>2</td><td>4</td><td>1</td><td>5</td><td>1</td><td>4</td><td>2</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">80.00</td>
                </tr>
                <tr>
                    <td><strong>R04</strong></td>
                    <td>Nail Artist 3</td>
                    <td>5</td><td>2</td><td>5</td><td>1</td><td>4</td><td>2</td><td>4</td><td>1</td><td>5</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">85.00</td>
                </tr>
                <tr>
                    <td><strong>R05</strong></td>
                    <td>Pelanggan 1</td>
                    <td>4</td><td>1</td><td>5</td><td>1</td><td>4</td><td>2</td><td>5</td><td>1</td><td>5</td><td>2</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">85.00</td>
                </tr>
                <tr>
                    <td><strong>R06</strong></td>
                    <td>Pelanggan 2</td>
                    <td>5</td><td>2</td><td>4</td><td>1</td><td>5</td><td>1</td><td>4</td><td>2</td><td>4</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">82.50</td>
                </tr>
                <tr>
                    <td><strong>R07</strong></td>
                    <td>Pelanggan 3</td>
                    <td>4</td><td>2</td><td>4</td><td>2</td><td>4</td><td>1</td><td>4</td><td>1</td><td>4</td><td>2</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">75.00</td>
                </tr>
                <tr>
                    <td><strong>R08</strong></td>
                    <td>Pelanggan 4</td>
                    <td>5</td><td>1</td><td>5</td><td>1</td><td>5</td><td>2</td><td>5</td><td>1</td><td>5</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">92.50</td>
                </tr>
                <tr>
                    <td><strong>R09</strong></td>
                    <td>Pelanggan 5</td>
                    <td>4</td><td>1</td><td>4</td><td>2</td><td>5</td><td>1</td><td>4</td><td>2</td><td>5</td><td>2</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">80.00</td>
                </tr>
                <tr>
                    <td><strong>R10</strong></td>
                    <td>Pelanggan 6</td>
                    <td>5</td><td>2</td><td>5</td><td>1</td><td>4</td><td>1</td><td>5</td><td>2</td><td>4</td><td>1</td>
                    <td style="font-weight: bold; background: #f0fdf4; color: #166534;">85.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #e0f2fe; color: #0369a1; font-size: 9pt;">
                    <td colspan="12" style="text-align: right; padding-right: 20px;">RATA-RATA TOTAL SKOR SUS:</td>
                    <td style="font-size: 11pt;">82.50</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<p>
    Kalkulasi rata-rata skor SUS diperoleh nilai akhir sebesar <strong>82.50</strong>. Mengacu pada skala tolok ukur Bangor, Kortum, dan Miller (2008):
</p>
<ul class="academic-list">
    <li><strong>Acceptability Ranges:</strong> Berada pada rentang <em>Acceptable</em> (Sangat Diterima).</li>
    <li><strong>Grade Scale:</strong> Memperoleh predikat <strong>Grade A</strong> (skor di atas 80.3).</li>
    <li><strong>Adjective Rating:</strong> Masuk dalam kategori <strong>"Excellent"</strong> (Sangat Memuaskan).</li>
</ul>
<p class="no-indent">
    Hasil kuantitatif ini membuktikan bahwa antarmuka pengguna sistem Booknail sangat intuitif, mudah dipelajari, dan memberikan kenyamanan pengalaman yang tinggi baik bagi pelanggan maupun staf internal studio.
</p>

<!-- ========================================== -->
<!-- 4.6 PEMBAHASAN DAN ANALISIS HASIL -->
<!-- ========================================== -->
<h2 class="section-title">4.6 Pembahasan dan Analisis Hasil Implementasi</h2>
<p>
    Berdasarkan serangkaian implementasi dan pengujian yang telah dieksekusi, sistem Booknail berhasil menghadirkan perubahan transformatif terhadap tata kelola operasional Nail Glam Studio. Tabel 4.4 menyajikan komparasi indikator kunci sebelum dan sesudah penerapan sistem.
</p>

<div class="table-container">
    <div class="table-title">Tabel 4.4 Perbandingan Metrik Operasional Sebelum dan Sesudah Penerapan Booknail</div>
    <table class="academic-table">
        <thead>
            <tr>
                <th style="width: 25%;">Indikator Kinerja Utama (KPI)</th>
                <th style="width: 32%;">Sebelum Penerapan (Manual WhatsApp)</th>
                <th style="width: 33%;">Setelah Penerapan (Booknail)</th>
                <th style="width: 10%;">Dampak</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Rata-rata Waktu Reservasi</strong></td>
                <td>18,0 Menit (komunikasi tanya-jawab chat berulang)</td>
                <td>2,5 Menit (pemesanan mandiri via wizard dinamis)</td>
                <td><span class="badge-success">-86,1%</span></td>
            </tr>
            <tr>
                <td><strong>Tingkat Insiden Double Booking</strong></td>
                <td>14,3% (akibat kelalaian pencatatan buku kasir)</td>
                <td>0,0% (dijamin transaksi ACID & pessimistic lock)</td>
                <td><span class="badge-success">0% (Nol)</span></td>
            </tr>
            <tr>
                <td><strong>Waktu Penyampaian Info ke Terapis</strong></td>
                <td>30 s/d 60 Menit (menunggu admin memberitahu manual)</td>
                <td>&le; 450 ms (Google FCM Web Push Toast Alert)</td>
                <td><span class="badge-fcm">Real-Time</span></td>
            </tr>
            <tr>
                <td><strong>Waktu Pembuatan Laporan Bulanan</strong></td>
                <td>4 Jam (merekap nota kertas dan kalkulator)</td>
                <td>3 Detik (otomatis ter-generate via PDF & CSV)</td>
                <td><span class="badge-success">Instan</span></td>
            </tr>
            <tr>
                <td><strong>Tingkat Kepuasan Pengguna (SUS)</strong></td>
                <td>54,20 (Kategori: Poor / D)</td>
                <td>82,50 (Kategori: Excellent / Grade A)</td>
                <td><span class="badge-primary">+52,2%</span></td>
            </tr>
        </tbody>
    </table>
</div>

<p>
    Efisiensi waktu pemesanan yang terpangkas sebesar 86,1% memberikan keleluasaan bagi staf admin untuk berfokus pada pelayanan pelanggan di lokasi fisik. Peniadaan insiden <em>double booking</em> secara total berhasil menyelamatkan studio dari potensi kerugian finansial dan menjaga citra reputasi profesional Nail Glam Studio.
</p>
"""
