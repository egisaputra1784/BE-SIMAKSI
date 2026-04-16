<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ApiControllers;
use Illuminate\Console\Command;
use App\Models\SesiAbsen;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Services\AbsensiService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CloseExpiredSesiAbsen extends Command
{
    protected $signature = 'absensi:close-expired';
    protected $description = 'Auto close sesi absen yang sudah lewat dan generate alpha';

    public function handle()
    {
        $this->info("🔍 Checking expired sesi...");

        $now = Carbon::now();

        $sesiList = SesiAbsen::with('jadwal')
            ->where('is_closed', false)
            ->get();

        $count = 0;

        foreach ($sesiList as $sesi) {

            if (!$sesi->jadwal)
                continue;

            $endTime = Carbon::parse($sesi->tanggal . ' ' . $sesi->jadwal->jam_selesai);

            // kalau belum lewat waktu selesai → skip
            if ($now->lt($endTime)) {
                continue;
            }

            DB::beginTransaction();

            try {

                $this->info("⚡ Closing sesi ID: {$sesi->id}");

                $muridList = AnggotaKelas::where('kelas_id', $sesi->jadwal->kelas_id)->get();

                foreach ($muridList as $m) {

                    $absen = Absensi::firstOrCreate(
                        [
                            'sesi_absen_id' => $sesi->id,
                            'murid_id' => $m->murid_id,
                        ],
                        [
                            'status' => 'alpha',
                            'waktu_scan' => now()
                        ]
                    );

                    // hanya yang baru dibuat = belum absen
                    if ($absen->wasRecentlyCreated) {
                        app(ApiControllers::class)
                            ->applyPoint($m->murid_id, $absen, null, 'alpha');
                    }
                }

                SesiAbsen::where('id', $sesi->id)
                    ->update([
                        'is_closed' => true
                    ]);

                DB::commit();

                $count++;

            } catch (\Exception $e) {
                DB::rollBack();

                $this->error("❌ Failed sesi {$sesi->id}: " . $e->getMessage());
            }
        }

        $this->info("✅ Done. Closed {$count} sesi.");

        return Command::SUCCESS;
    }
}