<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApiControllers extends Controller
{
    /**
     * Guru buka sesi absen + generate QR
     */
    public function bukaAbsen(Request $request, $jadwalId)
    {
        $guru = JWTAuth::parseToken()->authenticate();

        $jadwal = Jadwal::findOrFail($jadwalId);

        $tokenQr = Str::random(8);

        // bikin sesi dulu (ini yang tadi ilang)
        $sesi = SesiAbsen::create([
            'jadwal_id' => $jadwal->id,
            'tanggal' => now()->toDateString(),
            'token_qr' => $tokenQr,
            'expired_at' => now()->addMinutes(30),
            'tipe' => 'mapel',
            'dibuka_oleh' => $guru->id,
            'dibuka_pada' => now()
        ]);

        $qrImage = QrCode::generate($tokenQr);


        return response()->json([
            'message' => 'Sesi dibuka',
            'sesi_id' => $sesi->id,
            'qr_token' => $tokenQr,
            'expired_at' => $sesi->expired_at->setTimezone('Asia/Jakarta')->toDateTimeString(),
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode($qrImage)
        ]);
    }


    /**
     * Murid scan QR
     */
    public function scan(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id',
            'token' => 'required|string'
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $sesi = SesiAbsen::findOrFail($request->sesi_id);

        if ($sesi->token_qr !== $request->token) {
            return response()->json(['message' => 'QR salah'], 400);
        }

        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'QR expired'], 400);
        }

        $exists = Absensi::where([
            'sesi_absen_id' => $sesi->id,
            'murid_id' => $user->id
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'Sudah absen'], 400);
        }

        $absen = Absensi::create([
            'sesi_absen_id' => $sesi->id,
            'murid_id' => $user->id,
            'status' => 'hadir',
            'waktu_scan' => now()
        ]);

        return response()->json([
            'message' => 'Absen berhasil',
            'data' => $absen
        ]);
    }


    /**
     * Semua jadwal
     */
    public function jadwal()
    {
        return response()->json([
            'data' => Jadwal::with(['kelas', 'mapel', 'guru'])->get()
        ]);
    }


    /**
     * Sesi aktif
     */
    public function sesiAbsenAktif()
    {
        $sesi = SesiAbsen::whereDate('tanggal', today())
            ->where('expired_at', '>', now())
            ->with(['jadwal.kelas', 'jadwal.mapel', 'jadwal.guru'])
            ->get();

        return response()->json(['data' => $sesi]);
    }


    /**
     * Riwayat absensi murid
     */
    public function absensiMurid()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'data' => Absensi::where('murid_id', $user->id)
                ->with(['sesiAbsensi.jadwal.kelas', 'sesiAbsensi.jadwal.mapel'])
                ->get()
        ]);
    }
}
