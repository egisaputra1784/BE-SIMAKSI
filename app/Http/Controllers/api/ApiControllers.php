<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Support\Str;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApiControllers extends Controller
{
    public function bukaAbsen(Request $request, Jadwal $jadwal)
    {
        $guru = JWTAuth::parseToken()->authenticate();
        $tokenQr = Str::random(8);

        // pakai GD
        $qrImage = QrCode::format('png')->size(200)->generate($tokenQr);
        $qrBase64 = base64_encode($qrImage);

        return response()->json([
            'sesi' => $sesi,
            'qr_token' => $tokenQr,
            'qr_image' => 'data:image/png;base64,' . $qrBase64
        ]);
    }

    /**
     * Scan QR
     */
    public function scan(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id',
            'token' => 'required|string'
        ]);

        $sesi = SesiAbsen::findOrFail($request->sesi_id);

        // cek token QR
        if ($sesi->token_qr !== $request->token) {
            return response()->json(['message' => 'QR salah'], 400);
        }

        // cek expired
        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'QR sudah expired'], 400);
        }

        // cek murid udah absen belum
        if (Absensi::where('sesi_absen_id', $sesi->id)->where('murid_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Sudah absen'], 400);
        }

        $absen = Absensi::create([
            'sesi_absen_id' => $sesi->id,
            'murid_id' => Auth::id(),
            'status' => 'hadir',
            'waktu_scan' => now()
        ]);

        return response()->json([
            'message' => 'Absen berhasil',
            'absensi' => $absen
        ]);
    }

    /**
     * GET semua jadwal
     */
    public function jadwal(Request $request)
    {
        $jadwals = Jadwal::with(['kelas', 'mapel', 'guru'])->get();

        return response()->json([
            'data' => $jadwals
        ]);
    }

    /**
     * GET sesi absen yang aktif / belum expired
     */
    public function sesiAbsenAktif(Request $request)
    {
        $sesi = SesiAbsen::whereDate('tanggal', now()->toDateString())
            ->where('expired_at', '>', now())
            ->with(['jadwal.kelas', 'jadwal.mapel', 'jadwal.guru'])
            ->get();

        return response()->json([
            'data' => $sesi
        ]);
    }

    /**
     * GET absensi murid (berdasarkan auth)
     */
    public function absensiMurid(Request $request)
    {
        $absensi = Absensi::where('murid_id', auth()->id())
            ->with(['sesiAbsensi.jadwal.kelas', 'sesiAbsensi.jadwal.mapel'])
            ->get();

        return response()->json([
            'data' => $absensi
        ]);
    }
}
