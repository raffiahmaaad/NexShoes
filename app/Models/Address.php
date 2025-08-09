<?php
// File: app/Models/Address.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    /**
     * Mass assignment protection.
     * Menggunakan $guarded agar semua field bisa diisi.
     */
    protected $guarded = [];

    /**
     * Accessor untuk mendapatkan full_name.
     * INI ADALAH KUNCI UTAMA agar 'full_name' muncul dengan benar.
     * Fungsi ini akan menggabungkan 'first_name' dan 'last_name'.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Relasi bahwa alamat ini dimiliki oleh satu Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
