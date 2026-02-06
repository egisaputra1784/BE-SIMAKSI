<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TahunAjar;

class TahunAjarSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjar::insert([
            ['nama' => '2024/2025', 'aktif' => false],
            ['nama' => '2025/2026', 'aktif' => true],
        ]);
    }
}
