<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin CUN',
            'email' => 'admin@kui.com',
            'password' => 'password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        // App user
        User::create([
            'name' => 'Estudiante CUN',
            'email' => 'user@kui.com',
            'password' => 'password',
            'is_admin' => false,
            'is_active' => true,
        ]);
    }
}
