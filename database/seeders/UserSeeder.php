<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Akun 1: Admin
        User::updateOrCreate(
            ['email' => 'admin@unla.ac.id'], // Kunci unik (email)
            [
                'name' => 'Fiki Administrator',
                'password' => Hash::make('adminunla2026'), // Ganti dengan password pilihanmu
                'email_verified_at' => now(),
            ]
        );

        // Akun 2: Operator
        User::updateOrCreate(
            ['email' => 'operator@unla.ac.id'], // Kunci unik (email)
            [
                'name' => 'Operator Dashboard',
                'password' => Hash::make('operatorunla2026'), // Ganti dengan password pilihanmu
                'email_verified_at' => now(),
            ]
        );
    }
}