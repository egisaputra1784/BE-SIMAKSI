<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

        $shift = [
            [
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '11:00:00'
            ],
            [
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '16:00:00'
            ]
        ];

        foreach ($hari as $h) {

            foreach ($shift as $s) {

                Jadwal::create([
                    'kelas_id' => 1,
                    'mapel_id' => 1,
                    'guru_id' => 3,
                    'hari' => $h,
                    'jam_mulai' => $s['jam_mulai'],
                    'jam_selesai' => $s['jam_selesai'],
                ]);

            }
        }
    }
}
