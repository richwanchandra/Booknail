<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'duration_minutes',
        'description',
        'image',
        'service_image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}j {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        }

        return "{$minutes} menit";
    }

    public function getServiceImageAttribute(): ?string
    {
        return $this->attributes['image'] ?? null;
    }

    public function setServiceImageAttribute($value): void
    {
        $this->attributes['image'] = $value;
    }

    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'data:image')) {
                return $this->image;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->image);
            }
        }

        $prompt = 'luxury nail art ' . ($this->category?->name ?? 'nail') . ' ' . $this->name . ', elegant pink rose gold manicure design, beauty salon aesthetic';
        return 'https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=' . urlencode($prompt) . '&image_size=portrait_4_3';
    }
}
