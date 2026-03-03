<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $jadwal = Jadwal::findOrFail($jadwalId);

        $tokenQr = Str::random(8);

        $sesi = SesiAbsen::create([
            'jadwal_id'   => $jadwal->id,
            'tanggal'     => now()->toDateString(),
            'token_qr'    => $tokenQr,
            'expired_at'  => now()->addMinutes(30),
            'tipe'        => 'mapel',
            'dibuka_oleh' => $guru->id,
            'dibuka_pada' => now()
        ]);

        $qrImage = QrCode::generate($tokenQr);

        return response()->json([
            'message'    => 'Sesi dibuka',
            'sesi_id'    => $sesi->id,
            'qr_token'   => $tokenQr,
            'expired_at' => $sesi->expired_at->setTimezone('Asia/Jakarta')->toDateTimeString(),
            'qr_image'   => 'data:image/svg+xml;base64,' . base64_encode($qrImage)
        ]);
    }

    /**
     * Murid scan QR
     */
    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'murid') {
            return response()->json(['message' => 'Hanya murid yang bisa scan'], 403);
        }

        $sesi = SesiAbsen::where('token_qr', $request->token)->first();

        if (!$sesi) {
            return response()->json(['message' => 'QR tidak valid'], 400);
        }

        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'QR expired'], 400);
        }

        // Cek apakah murid memang ada di kelas ini
        $isAnggota = AnggotaKelas::where('kelas_id', $sesi->jadwal->kelas_id)
            ->where('murid_id', $user->id)
            ->exists();

        if (!$isAnggota) {
            return response()->json(['message' => 'Bukan anggota kelas'], 403);
        }

        $exists = Absensi::where([
            'sesi_absen_id' => $sesi->id,
            'murid_id'      => $user->id
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'Sudah absen'], 400);
        }

        $absen = Absensi::create([
            'sesi_absen_id' => $sesi->id,
            'murid_id'      => $user->id,
            'status'        => 'hadir',
            'waktu_scan'    => now()
        ]);

        return response()->json([
            'message' => 'Absen berhasil',
            'data'    => $absen
        ]);
    }

    /**
     * Manual QR (insert only)
     */
    public function absenManualQR(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id',
            'murid_id' => 'required|exists:users,id',
            'status'  => 'required|in:hadir,izin,sakit,alpha'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 422);
        }

        $exists = Absensi::where([
            'sesi_absen_id' => $sesi->id,
            'murid_id'      => $request->murid_id
        ])->exists();

        if ($exists) {
            return response()->json(['message' => 'Sudah diinput'], 400);
        }

        $absen = Absensi::create([
            'sesi_absen_id' => $sesi->id,
            'murid_id'      => $request->murid_id,
            'status'        => $request->status,
            'waktu_scan'    => now()
        ]);

        return response()->json([
            'message' => 'Absensi manual berhasil',
            'data'    => $absen
        ]);
    }

    /**
     * Manual utama (insert / update)
     */
    public function absenManual(Request $request)
    {
        $request->validate([
            'sesi_id'           => 'required|exists:sesi_absen,id',
            'data'              => 'required|array',
            'data.*.murid_id'   => 'required|exists:users,id',
            'data.*.status'     => 'required|in:hadir,izin,sakit,alpha'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        if (now()->greaterThan($sesi->expired_at)) {
            return response()->json(['message' => 'Sesi sudah berakhir'], 422);
        }

        foreach ($request->data as $item) {
            Absensi::updateOrCreate(
                [
                    'sesi_absen_id' => $sesi->id,
                    'murid_id'      => $item['murid_id']
                ],
                [
                    'status'     => $item['status'],
                    'waktu_scan' => now()
                ]
            );
        }

        return response()->json([
            'message' => 'Absensi berhasil disimpan / diperbarui'
        ]);
    }
}
