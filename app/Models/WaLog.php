<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaLog extends Model
{
    protected $fillable = [
        'phone_number',
        'message_type',
        'message_body',
        'status',
        'provider_message_id',
        'error_message',
        'sent_at',
        'booking_id',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public const TYPE_BOOKING_CREATED = 'booking_created';

    public const TYPE_DP_CONFIRMED = 'dp_confirmed';

    public const TYPE_REMINDER_H1 = 'reminder_h1';

    public const TYPE_REMINDER_2H = 'reminder_2h';

    public const TYPE_BOOKING_CANCELLED = 'booking_cancelled';

    public const TYPE_BOOKING_COMPLETED = 'booking_completed';

    public const TYPE_SCHEDULE_DELAYED = 'schedule_delayed';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->message_type) {
            self::TYPE_BOOKING_CREATED => 'Booking Dibuat',
            self::TYPE_DP_CONFIRMED => 'DP Dikonfirmasi',
            self::TYPE_REMINDER_H1 => 'Pengingat H-1',
            self::TYPE_REMINDER_2H => 'Pengingat 2 Jam',
            self::TYPE_BOOKING_CANCELLED => 'Booking Dibatalkan',
            self::TYPE_BOOKING_COMPLETED => 'Booking Selesai',
            self::TYPE_SCHEDULE_DELAYED => 'Penyesuaian Jadwal (Delay)',
            default => ucfirst(str_replace('_', ' ', $this->message_type)),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_QUEUED => 'Dalam Antrian',
            self::STATUS_SENT => 'Terkirim',
            self::STATUS_FAILED => 'Gagal',
            default => ucfirst($this->status),
        };
    }
}
