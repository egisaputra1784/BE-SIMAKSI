<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@simaksi.id'],
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@simaksi.id',
                'password' => Hash::make('superadmin123'),
                'role'     => 'superadmin',
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@simaksi.id'],
            [
                'name'     => 'Admin Sekolah',
                'email'    => 'admin@simaksi.id',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        $this->command->info('✅ Akun admin berhasil dibuat:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['superadmin', 'superadmin@simaksi.id', 'superadmin123'],
                ['admin',      'admin@simaksi.id',      'admin123'],
            ]
        );
    }
}
