<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_type',
        'proof_file',
        'amount',
        'status',
        'rejection_reason',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'verified_at' => 'datetime',
    ];

    public const TYPE_DP = 'dp';

    public const TYPE_FULL = 'full';

    public const TYPE_PELUNASAN = 'pelunasan';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_VERIFIED => 'Terverifikasi',
            self::STATUS_REJECTED => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::STATUS_VERIFIED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::STATUS_REJECTED => 'bg-rose-100 text-rose-800 border-rose-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    public function getProofUrlAttribute(): ?string
    {
        if (empty($this->proof_file)) {
            return null;
        }

        if (str_starts_with($this->proof_file, 'data:')) {
            return $this->proof_file;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->proof_file);
    }

    public function getIsBase64ProofAttribute(): bool
    {
        return ! empty($this->proof_file) && str_starts_with($this->proof_file, 'data:');
    }

    public function getProofDisplayNameAttribute(): string
    {
        if (empty($this->proof_file)) {
            return '-';
        }

        if ($this->is_base64_proof) {
            $bookingCode = $this->booking ? $this->booking->booking_code : 'DP';

            return "bukti_{$bookingCode}";
        }

        return basename($this->proof_file);
    }
}
