<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Kelas, User, TahunAjar, AnggotaKelas};

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $tahun = TahunAjar::where('aktif', true)->first();
        $gurus = User::where('role', 'guru')->get();
        $murids = User::where('role', 'murid')->get();

        $kelasList = ['X RPL 1', 'X RPL 2', 'XI RPL 1'];

        $index = 0;

        foreach ($kelasList as $nama) {

            $kelas = Kelas::create([
                'nama_kelas' => $nama,
                'tahun_ajar_id' => $tahun->id,
                'wali_guru_id' => $gurus->random()->id
            ]);

            for ($i = 0; $i < 20; $i++) {
                AnggotaKelas::create([
                    'kelas_id' => $kelas->id,
                    'murid_id' => $murids[$index++]->id
                ]);
            }
        }
    }
}
