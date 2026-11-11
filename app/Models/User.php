<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'loyalty_points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'loyalty_points' => 'integer',
        ];
    }

    public function nailArtist(): HasOne
    {
        return $this->hasOne(NailArtist::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /** FCM device tokens associated with the user. */
    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function paymentsVerified(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isArtist(): bool
    {
        return $this->hasRole('artist');
    }

    /**
     * Add loyalty points (pure increment, formula done by caller).
     * 1 point earned per Rp 10.000 spent (calculated in caller).
     */
    public function addLoyaltyPoints(int $points): void
    {
        if ($points > 0) {
            $this->increment('loyalty_points', $points);
        }
    }
}
