<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Absensi;
use App\Models\SesiAbsen;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RekapController extends Controller
{
    public function siswaRekap(Request $request)
    {
        /* =========================
           VALIDASI REQUEST
        ========================= */
        $request->validate([
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
            'kelas_id' => 'required|exists:kelas,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        /* =========================
           AUTH GURU
        ========================= */
        $guru = JWTAuth::parseToken()->authenticate();

        if (!$guru || $guru->role !== 'guru') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $tahunAjarId = $request->tahun_ajar_id;
        $kelasId = $request->kelas_id;
        $start = $request->start_date;
        $end = $request->end_date;

        /* =========================
           AMBIL MURID DALAM KELAS
        ========================= */
        $murids = AnggotaKelas::with('murid')
            ->where('kelas_id', $kelasId)
            ->whereHas('kelas', function ($q) use ($tahunAjarId) {
                $q->where('tahun_ajar_id', $tahunAjarId);
            })
            ->get();

        /* =========================
           AMBIL SEMUA SESI GURU DI KELAS INI
        ========================= */
        $sesiGuru = SesiAbsen::whereHas('jadwal', function ($q) use ($guru, $kelasId) {
            $q->where('guru_id', $guru->id)
              ->where('kelas_id', $kelasId);
        })
        ->when($start, fn($q) => $q->whereDate('tanggal', '>=', $start))
        ->when($end, fn($q) => $q->whereDate('tanggal', '<=', $end))
        ->pluck('id'); // ambil id sesi aja

        $data = [];

        /* =========================
           LOOP REKAP PER MURID
        ========================= */
        foreach ($murids as $anggota) {
            $murid = $anggota->murid;

            // cek absensi murid di sesi guru
            $absensi = Absensi::whereIn('sesi_absen_id', $sesiGuru)
                ->where('murid_id', $murid->id)
                ->get();

            $total = count($sesiGuru); // total sesi guru di periode
            $hadir = $absensi->where('status', 'hadir')->count();
            $percent = $total > 0 ? round(($hadir / $total) * 100) : 0;

            /* =========================
               STATUS KEHADIRAN
            ========================= */
            $status = 'Baik';
            if ($percent < 70) {
                $status = 'Perlu perhatian';
            } elseif ($percent < 85) {
                $status = 'Cukup';
            }

            $data[] = [
                'id' => $murid->id,
                'name' => $murid->name,
                'hadir' => $hadir,
                'total' => $total,
                'percent' => $percent,
                'status' => $status
            ];
        }

        /* =========================
           RESPONSE
        ========================= */
        return response()->json([
            'success' => true,
            'students' => $data
        ]);
    }
}
