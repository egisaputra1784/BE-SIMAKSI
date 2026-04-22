<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ApiControllers;
use Illuminate\Console\Command;
use App\Models\SesiAbsen;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
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
            ->whereDate('tanggal', '<=', now())
            ->get();

        $count = 0;

        foreach ($sesiList as $sesi) {

            if (!$sesi->jadwal) continue;

            $endTime = Carbon::parse($sesi->tanggal . ' ' . $sesi->jadwal->jam_selesai);

            // skip kalau belum lewat
            if ($now->lt($endTime)) continue;

            DB::beginTransaction();

            try {

                $this->info("⚡ Closing sesi ID: {$sesi->id}");

                $muridList = AnggotaKelas::where('kelas_id', $sesi->jadwal->kelas_id)->get();

                foreach ($muridList as $m) {

                    // cek apakah sudah ada absensi
                    $absen = Absensi::where([
                        'sesi_absen_id' => $sesi->id,
                        'murid_id' => $m->murid_id,
                    ])->first();

                    // kalau sudah ada → skip
                    if ($absen) continue;

                    // 🔍 cek voucher dulu
                    $voucher = \App\Models\UserToken::where('user_id', $m->murid_id)
                        ->where('status', 'AVAILABLE')
                        ->whereHas('item', fn($q) => $q->where('type', 'ALPHA'))
                        ->lockForUpdate()
                        ->with('item')
                        ->first();

                    $usedVoucher = false;

                    if ($voucher) {

                        // ✅ pakai voucher → tidak alpha
                        $absen = Absensi::create([
                            'sesi_absen_id' => $sesi->id,
                            'murid_id' => $m->murid_id,
                            'status' => 'hadir', // bisa diganti 'bebas_alpha' kalau mau lebih jelas
                            'waktu_scan' => now()
                        ]);

                        $voucher->update([
                            'status' => 'USED',
                            'used_at_attendance_id' => $absen->id
                        ]);

                        \App\Models\PointLedger::create([
                            'user_id' => $m->murid_id,
                            'transaction_type' => 'SPEND',
                            'amount' => 0,
                            'current_balance' => \App\Models\PointLedger::where('user_id', $m->murid_id)
                                ->latest('id')
                                ->value('current_balance') ?? 0,
                            'event_type' => 'VOUCHER_USED',
                            'item_id' => $voucher->item_id,
                            'used_token_id' => $voucher->id,
                            'absensi_id' => $absen->id,
                            'description' => 'Pakai voucher bebas alpha'
                        ]);

                        $usedVoucher = true;
                    } else {

                        // ❌ tidak ada voucher → alpha
                        $absen = Absensi::create([
                            'sesi_absen_id' => $sesi->id,
                            'murid_id' => $m->murid_id,
                            'status' => 'alpha',
                            'waktu_scan' => now()
                        ]);
                    }

                    // 🔥 apply point
                    $statusForPoint = $usedVoucher ? 'hadir' : 'alpha';

                    app(ApiControllers::class)
                        ->applyPoint($m->murid_id, $absen, null, $statusForPoint, $usedVoucher);
                }

                // tutup sesi
                $sesi->update([
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
