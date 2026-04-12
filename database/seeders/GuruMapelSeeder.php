<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuruMapel;

class GuruMapelSeeder extends Seeder
{
    public function run(): void
    {
        GuruMapel::create([
            'guru_id' => 3,
            'mapel_id' => 1,
        ]);
    }
}
