<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\SesiAbsen;
use Carbon\Carbon;
use App\Http\Controllers\Api\ApiControllers;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {


        \Log::info('scheduler fired');
        $schedule->command('absensi:close-sesi')->everyMinute();
    
            // alternatif tanpa command, langsung panggil service
        $schedule->call(function () {

            $sessions = SesiAbsen::with('jadwal')
                ->whereDate('tanggal', now())
                ->where('is_closed', false)
                ->get();

                \Log::info($sessions->count());

            foreach ($sessions as $sesi) {

                $endTime = Carbon::parse(
                    $sesi->tanggal . ' ' . $sesi->jadwal->jam_selesai
                )->addMinutes(10);

                if (now()->lt($endTime)) {
                    continue;
                }

                app(\App\Services\AbsensiService::class)->closeSesi($sesi->id);
            }

            \Log::info('scheduler jalan: '.now());
            \Log::info("closeSesi dipanggil: ".$sesi->id);

        })->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }

    
}