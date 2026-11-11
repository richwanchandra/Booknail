<?php

// File: Category.php
// Model Eloquent untuk mengelola kategori layanan nail art.
// Contoh kategori: Manicure, Pedicure, Nail Extension, dll.
// Setiap kategori memiliki banyak layanan (services) di bawahnya.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    /**
     * Boot method: mendaftarkan event model.
     * Otomatis membuat slug dari nama kategori saat record baru dibuat,
     * jika slug belum diisi secara manual.
     */
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($category) {
            // Hanya generate slug otomatis jika kolom slug masih kosong
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Relasi: Satu kategori memiliki banyak layanan (Service).
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Scope: Filter kategori yang aktif saja.
     * Digunakan untuk menampilkan hanya kategori yang tersedia bagi pelanggan.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
