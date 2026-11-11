<?php

// File: Booking.php
// Model Eloquent utama untuk mengelola data booking/pemesanan layanan nail art.
// Menangani seluruh siklus hidup booking: dari pembuatan, konfirmasi DP, pengerjaan,
// hingga penyelesaian atau pembatalan. Mendukung booking online maupun walk-in.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'booking_code',               // Kode unik booking (format: BNyymmddXXX)
        'customer_id',                // ID pelanggan terdaftar (nullable untuk walk-in)
        'artist_id',                  // ID nail artist yang ditugaskan
        'booking_date',               // Tanggal layanan dijadwalkan
        'start_time',                 // Jam mulai layanan
        'end_time',                   // Jam selesai layanan (estimasi)
        'custom_design_photo',        // Path foto desain kustom dari pelanggan
        'design_notes',               // Catatan desain/referensi dari pelanggan
        'total_price',                // Total harga layanan (dalam Rupiah)
        'dp_amount',                  // Jumlah DP (Down Payment) yang harus dibayar
        'loyalty_points_used',        // Jumlah poin loyalitas yang digunakan
        'loyalty_discount',           // Potongan harga dari poin loyalitas (dalam Rupiah)
        'status',                     // Status booking saat ini (pending/confirmed/in_progress/completed/cancelled/rescheduled)
        'is_walk_in',                 // Penanda apakah booking berasal dari walk-in
        'customer_name',              // Nama pelanggan walk-in (jika tidak login)
        'customer_phone',             // Nomor telepon pelanggan walk-in
        'branch',                     // Cabang salon tempat layanan
        'booking_source',             // Sumber booking (WEB_ONLINE / WALK_IN_ADMIN)
        'cashier_notes',              // Catatan dari kasir/admin
        'cancel_reason',              // Alasan pembatalan booking
        'cancelled_at',               // Waktu pembatalan
        'completed_at',               // Waktu penyelesaian layanan
        'is_delayed',                 // Penanda apakah booking mengalami keterlambatan
        'delay_minutes',              // Durasi keterlambatan dalam menit
        'original_start_time',        // Jam mulai asli sebelum pergeseran jadwal
        'original_end_time',          // Jam selesai asli sebelum pergeseran jadwal
        'delay_reason',               // Alasan keterlambatan
        'delay_notified_at',          // Waktu notifikasi delay dikirim ke pelanggan
        'customer_delay_ack',         // Konfirmasi pelanggan terhadap delay
        'service_upgrade_notes',      // Catatan upgrade layanan tambahan
        'artist_confirmation_status', // Status konfirmasi dari artist (pending/confirmed/rejected)
        'artist_confirmed_at',        // Waktu artist mengonfirmasi booking
        'artist_notes',               // Catatan dari artist terkait booking
    ];

    // Casting tipe data otomatis untuk kolom tertentu
    protected $casts = [
        'booking_date' => 'date',            // Objek Carbon untuk manipulasi tanggal
        'cancelled_at' => 'datetime',        // Objek Carbon untuk waktu pembatalan
        'completed_at' => 'datetime',        // Objek Carbon untuk waktu penyelesaian
        'delay_notified_at' => 'datetime',   // Objek Carbon untuk waktu notifikasi delay
        'artist_confirmed_at' => 'datetime', // Objek Carbon untuk waktu konfirmasi artist
        'total_price' => 'integer',          // Harga total sebagai integer (Rupiah, tanpa desimal)
        'dp_amount' => 'integer',            // Jumlah DP sebagai integer
        'loyalty_discount' => 'integer',     // Diskon loyalitas sebagai integer
        'loyalty_points_used' => 'integer',  // Poin loyalitas terpakai sebagai integer
        'is_walk_in' => 'boolean',           // Boolean untuk cek walk-in
        'is_delayed' => 'boolean',           // Boolean untuk cek keterlambatan
        'delay_minutes' => 'integer',        // Menit keterlambatan sebagai integer
    ];

    // Konstanta sumber booking: pemesanan melalui website
    public const SOURCE_ONLINE = 'WEB_ONLINE';

    // Konstanta sumber booking: walk-in melalui admin/kasir
    public const SOURCE_WALK_IN = 'WALK_IN_ADMIN';

    // Konstanta status: menunggu pembayaran DP
    public const STATUS_PENDING = 'pending';

    // Konstanta status: DP sudah dikonfirmasi, booking terkonfirmasi
    public const STATUS_CONFIRMED = 'confirmed';

    // Konstanta status: layanan sedang dikerjakan oleh artist
    public const STATUS_IN_PROGRESS = 'in_progress';

    // Konstanta status: layanan selesai
    public const STATUS_COMPLETED = 'completed';

    // Konstanta status: booking dibatalkan
    public const STATUS_CANCELLED = 'cancelled';

    // Konstanta status: booking dijadwalkan ulang
    public const STATUS_RESCHEDULED = 'rescheduled';

    /**
     * Relasi: Booking ini dimiliki oleh seorang pelanggan (User).
     * Nullable untuk kasus walk-in tanpa akun.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi alternatif untuk pelanggan (alias dari customer()).
     */
    public function user(): BelongsTo
    {
        return $this->customer();
    }

    /**
     * Relasi: Booking ini ditangani oleh seorang NailArtist.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(NailArtist::class, 'artist_id');
    }

    /**
     * Relasi: Satu booking memiliki banyak detail layanan (BookingDetail).
     */
    public function details(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    /**
     * Relasi: Mengambil satu pembayaran terbaru yang terkait dengan booking ini.
     * Menggunakan latestOfMany() untuk mendapatkan record payment paling akhir.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    /**
     * Relasi: Satu booking bisa memiliki banyak pembayaran (DP, pelunasan, dll).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relasi alternatif untuk detail booking (alias dari details()).
     */
    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    /**
     * Relasi: Satu booking memiliki satu ulasan dari pelanggan.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Relasi: Log pengiriman pesan WhatsApp yang terkait dengan booking ini.
     */
    public function waLogs(): HasMany
    {
        return $this->hasMany(WaLog::class);
    }

    /**
     * Accessor: Format total harga ke format mata uang Rupiah (contoh: Rp 150.000).
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp '.number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Accessor: Format jumlah DP ke format mata uang Rupiah.
     */
    public function getFormattedDpAmountAttribute(): string
    {
        return 'Rp '.number_format($this->dp_amount, 0, ',', '.');
    }

    /**
     * Accessor: Format tanggal booking ke format Indonesia lengkap (contoh: Senin, 01 Januari 2025).
     */
    public function getFormattedBookingDateAttribute(): string
    {
        return $this->booking_date->translatedFormat('l, d F Y');
    }

    /**
     * Accessor: URL atau Data URI foto referensi desain kustom (Base64 atau Storage URL).
     */
    public function getCustomDesignPhotoUrlAttribute(): ?string
    {
        if (empty($this->custom_design_photo)) {
            return null;
        }

        if (str_starts_with($this->custom_design_photo, 'data:')) {
            return $this->custom_design_photo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->custom_design_photo);
    }

    /**
     * Accessor: Cek apakah custom design photo berformat Base64 Data URI.
     */
    public function getIsBase64DesignAttribute(): bool
    {
        return ! empty($this->custom_design_photo) && str_starts_with($this->custom_design_photo, 'data:');
    }

    /**
     * Accessor: Label nama tampilan untuk file referensi desain.
     */
    public function getDesignDisplayNameAttribute(): string
    {
        if (empty($this->custom_design_photo)) {
            return '-';
        }

        if ($this->is_base64_design) {
            return "desain_{$this->booking_code}";
        }

        return basename($this->custom_design_photo);
    }

    /**
     * Accessor: Menampilkan rentang waktu layanan dalam format "HH:MM - HH:MM".
     */
    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }

    /**
     * Accessor: Menampilkan rentang waktu asli sebelum delay (jika ada).
     * Mengembalikan null jika booking tidak pernah mengalami pergeseran jadwal.
     */
    public function getFormattedOriginalTimeAttribute(): ?string
    {
        // Jika tidak ada waktu asli tersimpan, berarti tidak pernah di-delay
        if (! $this->original_start_time) {
            return null;
        }

        return substr($this->original_start_time, 0, 5).' - '.substr($this->original_end_time ?? '', 0, 5);
    }

    /**
     * Accessor: Mengecek apakah booking benar-benar mengalami delay yang signifikan.
     * Memerlukan flag is_delayed aktif DAN delay_minutes lebih dari 0.
     */
    public function getHasDelayAttribute(): bool
    {
        return (bool) ($this->is_delayed && $this->delay_minutes > 0);
    }

    /**
     * Accessor: Mengembalikan kelas CSS Tailwind untuk badge status booking.
     * Setiap status memiliki warna badge yang berbeda untuk kemudahan identifikasi visual.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-200',           // Kuning: menunggu
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800 border-blue-200',             // Biru: terkonfirmasi
            self::STATUS_IN_PROGRESS => 'bg-purple-100 text-purple-800 border-purple-200',     // Ungu: sedang dikerjakan
            self::STATUS_COMPLETED => 'bg-emerald-100 text-emerald-800 border-emerald-200',    // Hijau: selesai
            self::STATUS_CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',             // Merah: dibatalkan
            self::STATUS_RESCHEDULED => 'bg-orange-100 text-orange-800 border-orange-200',     // Oranye: dijadwalkan ulang
            default => 'bg-gray-100 text-gray-800 border-gray-200',                            // Abu: status tidak dikenal
        };
    }

    /**
     * Accessor: Mengembalikan label status booking dalam bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu DP',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_RESCHEDULED => 'Dijadwalkan Ulang',
            default => ucfirst($this->status),
        };
    }

    /**
     * Scope: Filter booking dengan status pending (menunggu DP).
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Filter booking dengan status confirmed (terkonfirmasi).
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope: Filter booking yang dijadwalkan untuk hari ini.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('booking_date', today());
    }

    /**
     * Scope: Filter booking dengan status completed (selesai).
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope: Filter booking dengan status in_progress (sedang dikerjakan).
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope: Filter booking dengan status cancelled (dibatalkan).
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Accessor: Menampilkan nama pelanggan dengan fallback untuk walk-in.
     * Prioritas: data user terdaftar > nama walk-in > label default.
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        // Jika pelanggan adalah user terdaftar, gunakan nama dari relasi
        if ($this->customer) {
            return $this->customer->name;
        }

        // Jika walk-in dengan nama tercatat, gunakan nama tersebut; jika tidak, tampilkan label default
        return $this->customer_name ?: 'Pelanggan Walk-In';
    }

    /**
     * Accessor: Menampilkan nomor telepon pelanggan dengan fallback untuk walk-in.
     */
    public function getCustomerDisplayPhoneAttribute(): string
    {
        // Jika pelanggan adalah user terdaftar, ambil nomor telepon dari relasi
        if ($this->customer) {
            return $this->customer->phone ?? '-';
        }

        // Jika walk-in, gunakan nomor telepon yang dicatat saat booking
        return $this->customer_phone ?: '-';
    }

    /**
     * Accessor: Mengecek apakah booking ini berasal dari walk-in.
     * Dicek dari flag is_walk_in ATAU sumber booking SOURCE_WALK_IN.
     */
    public function getIsWalkInOrderAttribute(): bool
    {
        return (bool) ($this->is_walk_in || $this->booking_source === self::SOURCE_WALK_IN);
    }

    /**
     * Accessor: Mengembalikan kelas CSS badge berdasarkan sumber booking.
     * Walk-in mendapat warna ungu, online mendapat warna biru.
     */
    public function getSourceBadgeClassAttribute(): string
    {
        return $this->is_walk_in_order
            ? 'bg-purple-100 text-purple-800 border-purple-200'
            : 'bg-blue-100 text-blue-800 border-blue-200';
    }

    /**
     * Accessor: Mengembalikan label sumber booking dalam bahasa Indonesia.
     */
    public function getSourceLabelAttribute(): string
    {
        return $this->is_walk_in_order ? 'Walk-In Kasir' : 'Web Online';
    }

    /**
     * Scope: Filter booking yang berasal dari walk-in (kasir).
     */
    public function scopeWalkIn($query)
    {
        return $query->where(function ($q) {
            $q->where('booking_source', self::SOURCE_WALK_IN)
                ->orWhere('is_walk_in', true);
        });
    }

    /**
     * Scope: Filter booking yang berasal dari pemesanan online.
     */
    public function scopeOnline($query)
    {
        return $query->where(function ($q) {
            $q->where('booking_source', self::SOURCE_ONLINE)
                ->orWhereNull('booking_source')
                ->where('is_walk_in', false);
        });
    }

    /**
     * Scope: Filter booking berdasarkan cabang salon tertentu.
     */
    public function scopeBranch($query, $branch)
    {
        return $query->where('branch', $branch);
    }

    /**
     * Scope: Filter booking berdasarkan ID artist yang menangani.
     */
    public function scopeForArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    /**
     * Scope: Filter booking yang masih menunggu konfirmasi dari artist.
     */
    public function scopePendingArtistAck($query)
    {
        return $query->where('artist_confirmation_status', 'pending');
    }

    /**
     * Accessor: Mengembalikan kelas CSS badge untuk status konfirmasi artist.
     */
    public function getArtistConfirmationBadgeClassAttribute(): string
    {
        return match ($this->artist_confirmation_status) {
            'confirmed' => 'bg-emerald-50 text-emerald-700 border-emerald-200', // Hijau: dikonfirmasi
            'rejected' => 'bg-red-50 text-red-700 border-red-200',              // Merah: ditolak
            default => 'bg-amber-50 text-amber-700 border-amber-200',           // Kuning: menunggu
        };
    }

    /**
     * Accessor: Mengembalikan label konfirmasi artist dalam bahasa Indonesia.
     */
    public function getArtistConfirmationLabelAttribute(): string
    {
        return match ($this->artist_confirmation_status) {
            'confirmed' => 'Dikonfirmasi Teknisi',
            'rejected' => 'Ditolak / Bentrok',
            default => 'Menunggu Konfirmasi Teknisi',
        };
    }

    /**
     * Membuat kode booking unik dengan format BNyymmddXXX.
     * Contoh: BN251029001 untuk booking pertama tanggal 29 Oktober 2025.
     * Nomor urut dihitung berdasarkan jumlah booking yang sudah ada pada hari tersebut.
     */
    public static function generateBookingCode(): string
    {
        // Prefix berisi "BN" + tanggal dalam format yymmdd
        $prefix = 'BN'.date('ymd');

        // Hitung jumlah booking yang sudah ada dengan prefix yang sama hari ini
        $latest = self::where('booking_code', 'like', "{$prefix}%")->count();

        // Gabungkan prefix dengan nomor urut yang di-pad 3 digit (001, 002, dst.)
        return $prefix.str_pad($latest + 1, 3, '0', STR_PAD_LEFT);
    }
}
