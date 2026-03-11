<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiControllers extends Controller
{

    public function bukaAbsen()
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $hari = strtolower(now()->locale('id')->dayName);
        $jam  = now()->format('H:i:s');

        $jadwals = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwals->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada jadwal hari ini'
            ], 404);
        }

        $jadwalAktif = $jadwals->first(function ($j) use ($jam) {
            return $j->jam_mulai <= $jam && $j->jam_selesai >= $jam;
        });

        if (!$jadwalAktif) {
            return response()->json([
                'message' => 'Tidak ada jadwal saat ini'
            ], 404);
        }

        $first = $jadwals->first();
        $last  = $jadwals->last();

        if ($jadwalAktif->id === $first->id) {
            $tipe = 'masuk';
        } elseif ($jadwalAktif->id === $last->id) {
            $tipe = 'pulang';
        } else {
            $tipe = 'mapel';
        }

        $tokenQr = Str::random(8);

        $sesi = SesiAbsen::create([
            'jadwal_id'   => $jadwalAktif->id,
            'tanggal'     => now()->toDateString(),
            'token_qr'    => $tokenQr,
            'tipe'        => $tipe,
            'dibuka_oleh' => $guru->id,
            'dibuka_pada' => now()
        ]);

        $qrImage = QrCode::generate($tokenQr);

        return response()->json([
            'message'  => 'Sesi dibuka',
            'tipe'     => $tipe,
            'sesi_id'  => $sesi->id,
            'qr_token' => $tokenQr,
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode($qrImage)
        ]);
    }


    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'murid') {
            return response()->json(['message' => 'Hanya murid yang bisa scan'], 403);
        }

        $sesi = SesiAbsen::with('jadwal')
            ->where('token_qr', $request->token)
            ->first();

        if (!$sesi) {
            return response()->json(['message' => 'QR tidak valid'], 400);
        }

        $jadwal = $sesi->jadwal;

        $batasAbsen = Carbon::parse(
            $sesi->tanggal . ' ' . $jadwal->jam_selesai
        )->addMinutes(10);

        if (now()->gt($batasAbsen)) {
            return response()->json([
                'message' => 'Waktu absen sudah habis'
            ], 400);
        }

        $isAnggota = AnggotaKelas::where('kelas_id', $jadwal->kelas_id)
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

        $sesi = SesiAbsen::with('jadwal')->findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        $batasAbsen = Carbon::parse(
            $sesi->tanggal . ' ' . $sesi->jadwal->jam_selesai
        )->addMinutes(10);

        if (now()->gt($batasAbsen)) {
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

        $sesi = SesiAbsen::with('jadwal')->findOrFail($request->sesi_id);

        if ($guru->id !== $sesi->dibuka_oleh) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        $batasAbsen = Carbon::parse(
            $sesi->tanggal . ' ' . $sesi->jadwal->jam_selesai
        )->addMinutes(10);

        if (now()->gt($batasAbsen)) {
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


    public function getMuridSesi($Id)
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::with('jadwal.kelas')->findOrFail($Id);

        if ($sesi->dibuka_oleh !== $guru->id) {
            return response()->json(['message' => 'Bukan sesi Anda'], 403);
        }

        $murid = AnggotaKelas::with('murid')
            ->where('kelas_id', $sesi->jadwal->kelas_id)
            ->get()
            ->map(function ($a) use ($sesi) {

                $absen = Absensi::where([
                    'sesi_absen_id' => $sesi->id,
                    'murid_id' => $a->murid->id
                ])->first();

                return [
                    'id' => $a->murid->id,
                    'name' => $a->murid->name,
                    'status' => $absen->status ?? null
                ];
            });

        return response()->json([
            'data' => $murid
        ]);
    }


    public function tahunAjar()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = TahunAjar::orderBy('nama', 'desc')->get([
            'id',
            'nama',
            'aktif'
        ]);

        return response()->json([
            'data' => $data
        ]);
    }


    public function kelas(Request $request)
    {
        $request->validate([
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if (!$guru || $guru->role !== 'guru') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $kelas = Jadwal::with('kelas')
            ->where('guru_id', $guru->id)
            ->whereHas('kelas', function ($q) use ($request) {
                $q->where('tahun_ajar_id', $request->tahun_ajar_id);
            })
            ->get()
            ->pluck('kelas')
            ->unique('id')
            ->values();

        return response()->json([
            'data' => $kelas->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama_kelas' => $k->nama_kelas
                ];
            })
        ]);
    }
    public function assessmentCategories()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = AssessmentCategory::where('is_active', true)
            ->get(['id', 'name', 'description']);

        return response()->json([
            'data' => $data
        ]);
    }

    public function muridKelas($kelasId)
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $murid = AnggotaKelas::with('murid')
            ->where('kelas_id', $kelasId)
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->murid->id,
                    'name' => $a->murid->name
                ];
            });

        return response()->json([
            'data' => $murid
        ]);
    }

    public function simpanAssessment(Request $request)
    {
        $request->validate([
            'murid_id' => 'required|exists:users,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'scores'   => 'required|array',
            'scores.*.category_id' => 'required|exists:assessment_categories,id',
            'scores.*.score' => 'required|integer|min:1|max:5'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {

            $assessment = Assessment::create([
                'guru_id'  => $guru->id,
                'murid_id' => $request->murid_id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'tanggal'  => now(),
                'catatan'  => $request->catatan
            ]);

            foreach ($request->scores as $score) {

                AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'category_id'   => $score['category_id'],
                    'score'         => $score['score']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Penilaian berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function nilaiMurid(Request $request, $muridId)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Assessment::with('details.category')
            ->where('murid_id', $muridId)
            ->where('guru_id', $guru->id);

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('tanggal', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        /*
    ======================
    RATA RATA PER KATEGORI
    ======================
    */

        $avgPerKategori = DB::table('assessment_details')
            ->join('assessments', 'assessment_details.assessment_id', '=', 'assessments.id')
            ->join('assessment_categories', 'assessment_details.category_id', '=', 'assessment_categories.id')
            ->where('assessments.murid_id', $muridId)
            ->where('assessments.guru_id', $guru->id)
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('assessments.tanggal', [
                    $request->start_date,
                    $request->end_date
                ]);
            })
            ->select(
                'assessment_categories.id',
                'assessment_categories.name',
                DB::raw('ROUND(AVG(assessment_details.score),2) as rata_rata')

            )
            ->groupBy('assessment_categories.id', 'assessment_categories.name')
            ->get();

        /*
    ======================
    RATA RATA TOTAL
    ======================
    */

        $avgTotal = DB::table('assessment_details')
            ->join('assessments', 'assessment_details.assessment_id', '=', 'assessments.id')
            ->where('assessments.murid_id', $muridId)
            ->where('assessments.guru_id', $guru->id)
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('assessments.tanggal', [
                    $request->start_date,
                    $request->end_date
                ]);
            })
            ->avg('assessment_details.score');

        return response()->json([
            'data' => $data,
            'rata_per_kategori' => $avgPerKategori,
            'rata_total' => round($avgTotal, 2)
        ]);
    }
}
