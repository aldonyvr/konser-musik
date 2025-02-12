<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin',
            'role_id' => 1,
            'email' => 'admkonser2025@gmail.com',
            'password' => bcrypt('12345678'),
            'phone' => '08123456789',
        ])->assignRole('admin');

        // Regular user
        User::create([
            'name' => 'User',
            'photo' => '/media/musik/profileuser.jpg',
            'email' => 'user@gmail.com',
            'role_id' => 2,
            'password' => bcrypt('12345678'),
            'phone' => '08123162631',
        ])->assignRole('user');

        // Mitra user
        User::create([
            'name' => 'Mitra Test',
            'email' => 'mitra@test.com',
            'password' => bcrypt('12345678'),
            'phone' => '081234567890',
            'company_name' => 'PT Mitra Test',
            'role_id' => 3,
        ])->assignRole('mitra');
    }
}
