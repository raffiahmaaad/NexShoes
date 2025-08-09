<?php

namespace Database\Seeders;

use App\Models\User; // <-- Gunakan Model User
use Illuminate\Database\Seeder;
use App\Enums\UserRole;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Gunakan User::create, bukan Admins::create
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com', // <-- Pastikan email valid
            'password' => bcrypt('password'), // <-- Tambahkan password (contoh: 'password')
            'role' => UserRole::Admin,
        ]);
    }
}
