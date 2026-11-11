<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'artist_id',
        'rating',
        'comment',
        'photo',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(NailArtist::class, 'artist_id');
    }

    public function getStarDisplayAttribute(): string
    {
        return str_repeat('★', $this->rating).str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Accessor: URL foto hasil karya review (mendukung Base64 Data URI maupun storage path).
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo)) {
            return null;
        }

        if (str_starts_with($this->photo, 'data:')) {
            return $this->photo;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->photo);
    }
}
