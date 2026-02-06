<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin'
        ]);

        // admin
        User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // guru
        for ($i = 1; $i <= 8; $i++) {
            User::create([
                'name' => "Guru $i",
                'email' => "guru$i@mail.com",
                'password' => Hash::make('password'),
                'role' => 'guru',
                'nip' => '1987' . $i . '00' . $i
            ]);
        }

        // murid
        for ($i = 1; $i <= 60; $i++) {
            User::create([
                'name' => "Murid $i",
                'email' => "murid$i@mail.com",
                'password' => Hash::make('password'),
                'role' => 'murid',
                'nisn' => '1000' . $i
            ]);
        }
    }
}
