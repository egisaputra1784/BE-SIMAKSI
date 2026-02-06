<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mapel;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            ['Matematika','MTK'],
            ['Bahasa Indonesia','BIN'],
            ['Bahasa Inggris','ENG'],
            ['Produktif RPL','RPL'],
            ['PKN','PKN'],
            ['IPA','IPA'],
        ];

        foreach ($mapels as $m) {
            Mapel::create([
                'nama_mapel' => $m[0],
                'kode_mapel' => $m[1]
            ]);
        }
    }
}
