<?php

namespace App\Services;

use App\Http\Controllers\Api\ApiControllers;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\SesiAbsen;
use Illuminate\Support\Facades\DB;

class AbsensiService
{
    public function closeSesi($id)
    {
        $sesi = SesiAbsen::with('jadwal')->findOrFail($id);

        if ($sesi->is_closed)
            return;

        $muridList = AnggotaKelas::where('kelas_id', $sesi->jadwal->kelas_id)->get();

        DB::beginTransaction();

        try {

            foreach ($muridList as $m) {

                $absen = Absensi::firstOrCreate(
                    [
                        'sesi_absen_id' => $sesi->id,
                        'murid_id' => $m->murid_id
                    ],
                    [
                        'status' => 'alpha',
                        'waktu_scan' => now()
                    ]
                );

                if ($absen->wasRecentlyCreated) {
                    app(ApiControllers::class)
                        ->applyPoint($m->murid_id, $absen, null, 'alpha');
                }
            }

            $sesi->update(['is_closed' => true]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}