<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Support\Str;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class ApiControllers extends Controller
{
    public function bukaAbsen(Request $request, Jadwal $jadwal)
    {
        $sesi = SesiAbsen::create([
            'jadwal_id' => $jadwal->id,
            'tanggal' => now()->toDateString(),
            'dibuka_oleh' => auth()->id(),
            'dibuka_pada' => now(),
        ]);

        return response()->json([
            'message' => 'Sesi dibuka',
            'sesi_id' => $sesi->id
        ]);
    }

    public function generateQr($sesiId)
    {
        $block = floor(now()->timestamp / 10);

        $token = hash('sha256', $sesiId . $block . config('app.key'));

        return response()->json([
            'token' => $token
        ]);
    }

    public function scan(Request $request)
    {
        $sesiId = $request->sesi_id;
        $tokenClient = $request->token;

        $block = floor(now()->timestamp / 10);
        $tokenServer = hash('sha256', $sesiId . $block . config('app.key'));

        if ($tokenClient !== $tokenServer) {
            return response()->json(['message' => 'QR expired'], 400);
        }

        Absensi::create([
            'sesi_absen_id' => $sesiId,
            'murid_id' => auth()->id(),
            'status' => 'hadir',
            'waktu_scan' => now()
        ]);

        return response()->json(['message' => 'Absen berhasil']);
    }

}
