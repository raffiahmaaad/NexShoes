<?php

namespace App\Models;

use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

// Pastikan Anda menambahkan "implements FilamentUser"
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar', // <-- Tambahkan 'avatar' di sini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, // <-- Lakukan casting ke Enum
        ];
    }

    /**
     * Metode ini adalah KUNCI UTAMA untuk keamanan panel Anda.
     * Filament akan memanggil ini untuk memeriksa izin akses.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya izinkan akses jika rolenya adalah Admin.
        return $this->role === UserRole::Admin;
    }

    /**
     * Get all orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Return default avatar or generate one
        return $this->getDefaultAvatarUrl();
    }

    /**
     * Get default avatar URL (you can use a service like UI Avatars)
     */
    public function getDefaultAvatarUrl(): string
    {
        $name = urlencode($this->name);
        $initials = urlencode($this->initials());

        // Using UI Avatars service for default avatars
        return "https://ui-avatars.com/api/?name={$name}&size=200&background=6366f1&color=ffffff&bold=true";
    }

    /**
     * Delete old avatar when updating
     */
    public function deleteOldAvatar(): void
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }
    }
}
