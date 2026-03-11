<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('assessment_categories')->insert([
            [
                'name' => 'Disiplin',
                'description' => 'Kedisiplinan siswa dalam mengikuti kegiatan belajar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Kerja Sama',
                'description' => 'Kemampuan bekerja sama dengan teman',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Tanggung Jawab',
                'description' => 'Tanggung jawab terhadap tugas yang diberikan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Komunikasi',
                'description' => 'Kemampuan menyampaikan pendapat dan berdiskusi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Partisipasi',
                'description' => 'Keaktifan dalam kegiatan pembelajaran',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
