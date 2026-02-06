<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Jadwal, Kelas, Mapel, User};

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = Kelas::all();
        $mapels = Mapel::all();
        $gurus = User::where('role', 'guru')->get();

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

        foreach ($kelas as $k) {
            foreach ($mapels as $m) {

                Jadwal::create([
                    'kelas_id' => $k->id,
                    'mapel_id' => $m->id,
                    'guru_id' => $gurus->random()->id,
                    'hari' => $hari[array_rand($hari)],
                    'jam_mulai' => '07:00:00',
                    'jam_selesai' => '08:30:00'
                ]);
            }
        }
    }
}
