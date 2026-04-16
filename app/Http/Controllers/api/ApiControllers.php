<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AnggotaKelas;
use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\FlexibilityItem;
use App\Models\SesiAbsen;
use App\Models\Jadwal;
use App\Models\TahunAjar;
use Illuminate\Http\Request;
use App\Models\PointLedger;
use App\Models\PointRule;
use App\Models\User;
use App\Models\UserToken;
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
        $jam = now()->format('H:i:s');

        $jadwal = Jadwal::where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        $aktif = $jadwal->first(fn($j) => $j->jam_mulai <= $jam && $j->jam_selesai >= $jam);

        if (!$aktif) {
            return response()->json(['message' => 'Tidak ada jadwal aktif'], 404);
        }

        $tipe = $aktif->id === $jadwal->first()->id
            ? 'masuk'
            : ($aktif->id === $jadwal->last()->id ? 'pulang' : 'mapel');

        $token = Str::random(8);

        $sesi = SesiAbsen::create([
            'jadwal_id' => $aktif->id,
            'tanggal' => now()->toDateString(),
            'token_qr' => $token,
            'tipe' => $tipe,
            'dibuka_oleh' => $guru->id,
            'dibuka_pada' => now()
        ]);

        return response()->json([
            'message' => 'Sesi dibuka',
            'sesi_id' => $sesi->id,
            'qr_token' => $token,
            'qr_image' => 'data:image/svg+xml;base64,' . base64_encode(QrCode::generate($token))
        ]);
    }


    public function scan(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'murid') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $sesi = SesiAbsen::with('jadwal')->where('token_qr', $request->token)->firstOrFail();

        $jadwal = $sesi->jadwal;

        // batas waktu
        $batas = Carbon::parse($sesi->tanggal . ' ' . $jadwal->jam_selesai)->addMinutes(10);

        if (now()->gt($batas)) {
            return response()->json(['message' => 'Waktu habis'], 400);
        }

        // cek anggota
        $isAnggota = AnggotaKelas::where('kelas_id', $jadwal->kelas_id)
            ->where('murid_id', $user->id)->exists();

        if (!$isAnggota) {
            return response()->json(['message' => 'Bukan anggota kelas'], 403);
        }

        // cek sudah absen
        if (
            Absensi::where([
                'sesi_absen_id' => $sesi->id,
                'murid_id' => $user->id
            ])->exists()
        ) {
            return response()->json(['message' => 'Sudah absen'], 400);
        }



        DB::beginTransaction();

        try {

            $absen = Absensi::create([
                'sesi_absen_id' => $sesi->id,
                'murid_id' => $user->id,
                'status' => 'hadir',
                'waktu_scan' => now()
            ]);

            $late = $this->formatLateTime($sesi->dibuka_pada, $absen->waktu_scan);

            $lateMinutes = $late['minutes'];
            $lateText = $late['text'];

            $point = $this->applyPoint($user->id, $absen, $lateMinutes);

            DB::commit();

            return response()->json([
                'message' => 'Absen berhasil',
                'late_minutes' => $lateMinutes . ' menit (' . $lateText . ') ',
                'point' => $point
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function absenManual(Request $request)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_absen,id',
            'data' => 'required|array',
            'data.*.murid_id' => 'required|exists:users,id',
            'data.*.status' => 'required|in:hadir,izin,sakit,alpha'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sesi = SesiAbsen::with('jadwal')->findOrFail($request->sesi_id);

        DB::beginTransaction();

        try {

            foreach ($request->data as $item) {

                // ambil data lama dulu (kalau ada)
                $existing = Absensi::where([
                    'sesi_absen_id' => $sesi->id,
                    'murid_id' => $item['murid_id']
                ])->first();

                $oldStatus = $existing->status ?? null;

                // update / create absensi
                $absen = Absensi::updateOrCreate(
                    [
                        'sesi_absen_id' => $sesi->id,
                        'murid_id' => $item['murid_id']
                    ],
                    [
                        'status' => $item['status'],
                        'waktu_scan' => now()
                    ]
                );

                /*
                ======================================
                🔥 LOGIC POINT (ANTI DOUBLE + ROLLBACK)
                ======================================
                */

                // ✅ kalau sebelumnya alpha, sekarang bukan → hapus penalty
                if ($oldStatus === 'alpha' && $item['status'] !== 'alpha') {

                    $lastPenalty = PointLedger::where('absensi_id', $absen->id)
                        ->where('transaction_type', 'PENALTY')
                        ->latest('id')
                        ->first();

                    if ($lastPenalty) {
                        $lastPenalty->delete();
                    }
                }

                // ✅ kalau sekarang alpha → kasih penalty (kalau belum ada)
                if ($item['status'] === 'alpha') {

                    $alreadyPointed = PointLedger::where('absensi_id', $absen->id)->exists();

                    if (!$alreadyPointed) {
                        $this->applyPoint($item['murid_id'], $absen, null, 'alpha');
                    }
                }

                // ✅ APPLY POINT (TELAT / RULE TIME)
                $lateMinutes = Carbon::parse($sesi->dibuka_pada)
                    ->diffInMinutes($absen->waktu_scan);

                $this->applyPoint($item['murid_id'], $absen, $lateMinutes, $item['status']);
            }

            DB::commit();

            return response()->json(['message' => 'Berhasil update absensi']);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'jadwal_id' => 'required|exists:jadwal,id',
            'murid_id' => 'required|exists:users,id',
            'scores' => 'required|array',
            'scores.*.category_id' => 'required|exists:assessment_categories,id',
            'scores.*.score' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {

            $jadwal = Jadwal::findOrFail($request->jadwal_id);

            /*
        ======================
        VALIDASI GURU
        ======================
        */

            if ($jadwal->guru_id !== $guru->id) {
                return response()->json([
                    'message' => 'Bukan jadwal Anda'
                ], 403);
            }

            /*
        ======================
        VALIDASI SISWA
        ======================
        */

            $isAnggota = AnggotaKelas::where('kelas_id', $jadwal->kelas_id)
                ->where('murid_id', $request->murid_id)
                ->exists();

            if (!$isAnggota) {
                return response()->json([
                    'message' => 'Murid bukan anggota kelas'
                ], 403);
            }

            /*
        ======================
        SIMPAN ASSESSMENT
        ======================
        */

            $assessment = Assessment::create([
                'guru_id' => $guru->id,
                'murid_id' => $request->murid_id,
                'kelas_id' => $jadwal->kelas_id,
                'mapel_id' => $jadwal->mapel_id,
                'tanggal' => now(),
                'catatan' => $request->catatan
            ]);

            /*
        ======================
        SIMPAN DETAIL NILAI
        ======================
        */

            foreach ($request->scores as $score) {

                AssessmentDetail::create([
                    'assessment_id' => $assessment->id,
                    'category_id' => $score['category_id'],
                    'score' => $score['score']
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
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function nilaiMurid(Request $request, $muridId)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Assessment::with('details.category')
            ->where('murid_id', $muridId)
            ->where('guru_id', $guru->id);

        if ($request->filled('start_date') && $request->filled('end_date')) {
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

    public function jadwalHariIni()
    {
        $guru = JWTAuth::parseToken()->authenticate();

        if ($guru->role !== 'guru') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $hari = strtolower(now()->locale('id')->dayName);

        $jadwal = Jadwal::with(['kelas', 'mapel'])
            ->where('guru_id', $guru->id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwal->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada jadwal hari ini'
            ]);
        }

        return response()->json([
            'hari' => $hari,
            'data' => $jadwal->map(function ($j) {

                $sesi = SesiAbsen::where('jadwal_id', $j->id)
                    ->where('tanggal', now()->toDateString())
                    ->first();

                return [
                    'jadwal_id' => $j->id,
                    'kelas_id' => $j->kelas_id,
                    'kelas' => $j->kelas->nama_kelas,
                    'mapel' => $j->mapel->nama_mapel,
                    'jam_mulai' => $j->jam_mulai,
                    'jam_selesai' => $j->jam_selesai,

                    'sesi_dibuka' => $sesi ? true : false,
                    'sesi_id' => $sesi->id ?? null,
                    'tipe_sesi' => $sesi->tipe ?? null,
                    'token_qr' => $sesi->token_qr ?? null
                ];
            })
        ]);
    }

    public function jadwalMuridHariIni()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'murid') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $hari = strtolower(now()->locale('id')->dayName);

        /*
    ======================
    AMBIL KELAS MURID
    ======================
    */

        $kelas = AnggotaKelas::where('murid_id', $user->id)->first();

        if (!$kelas) {
            return response()->json([
                'message' => 'Murid belum memiliki kelas'
            ], 404);
        }

        /*
    ======================
    AMBIL JADWAL HARI INI
    ======================
    */

        $jadwal = Jadwal::with(['kelas', 'mapel', 'guru'])
            ->where('kelas_id', $kelas->kelas_id)
            ->where('hari', $hari)
            ->orderBy('jam_mulai')
            ->get();

        if ($jadwal->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada jadwal hari ini'
            ]);
        }

        return response()->json([
            'hari' => $hari,
            'data' => $jadwal->map(function ($j) use ($user) {

                /*
            ======================
            CEK SESI ABSEN
            ======================
            */

                $sesi = SesiAbsen::where('jadwal_id', $j->id)
                    ->where('tanggal', now()->toDateString())
                    ->first();

                /*
            ======================
            CEK STATUS ABSEN
            ======================
            */

                $absen = null;

                if ($sesi) {
                    $absen = Absensi::where([
                        'sesi_absen_id' => $sesi->id,
                        'murid_id' => $user->id
                    ])->first();
                }

                return [
                    'jadwal_id' => $j->id,
                    'kelas' => $j->kelas->nama_kelas,
                    'mapel' => $j->mapel->nama_mapel,
                    'guru' => $j->guru->name,


                    'jam_mulai' => $j->jam_mulai,
                    'jam_selesai' => $j->jam_selesai,

                    'sesi_dibuka' => $sesi ? true : false,
                    'sesi_id' => $sesi->id ?? null,

                    'sudah_absen' => $absen ? true : false,
                    'status_absen' => $absen->status ?? null
                ];
            })
        ]);
    }

    public function jadwalMingguMurid()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user->role !== 'murid') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Ambil kelas murid
        $anggota = AnggotaKelas::where('murid_id', $user->id)->first();

        if (!$anggota) {
            return response()->json([
                'message' => 'Murid belum memiliki kelas'
            ], 404);
        }

        $kelasId = $anggota->kelas_id;

        // Hari ini sampai 6 hari ke depan
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(6);

        $daysOfWeek = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        // Ambil semua jadwal kelas
        $jadwals = Jadwal::with(['kelas', 'mapel', 'guru'])
            ->where('kelas_id', $kelasId)
            ->get();

        $result = [];

        for ($i = 0; $i <= 6; $i++) {
            $date = Carbon::now()->addDays($i);
            $hari = strtolower($date->locale('id')->dayName);

            $jadwalHari = $jadwals->filter(fn($j) => $j->hari === $hari)
                ->map(function ($j) use ($date, $user) {
                    $sesi = SesiAbsen::where('jadwal_id', $j->id)
                        ->where('tanggal', $date->toDateString())
                        ->first();

                    $absen = null;
                    if ($sesi) {
                        $absen = Absensi::where([
                            'sesi_absen_id' => $sesi->id,
                            'murid_id' => $user->id
                        ])->first();
                    }

                    return [
                        'jadwal_id' => $j->id,
                        'kelas' => $j->kelas->nama_kelas,
                        'mapel' => $j->mapel->nama_mapel,
                        'guru' => $j->guru->name,
                        'jam_mulai' => $j->jam_mulai,
                        'jam_selesai' => $j->jam_selesai,
                        'sesi_dibuka' => $sesi ? true : false,
                        'sesi_id' => $sesi->id ?? null,
                        'sudah_absen' => $absen ? true : false,
                        'status_absen' => $absen->status ?? null
                    ];
                })->values();

            $result[] = [
                'tanggal' => $date->toDateString(),
                'hari' => $hari,
                'jadwal' => $jadwalHari
            ];
        }

        return response()->json([
            'data' => $result
        ]);
    }


    public function buyToken(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:flexibility_items,id'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        DB::beginTransaction();

        try {

            // ambil item
            $item = FlexibilityItem::lockForUpdate()->findOrFail($request->item_id);

            // =========================
            // CEK SALDO
            // =========================
            $lastBalance = PointLedger::where('user_id', $user->id)
                ->latest('id')
                ->value('current_balance') ?? 0;

            if ($lastBalance < $item->point_cost) {
                return response()->json(['message' => 'Poin tidak cukup'], 400);
            }

            // =========================
            // CEK STOCK LIMIT (PER BULAN)
            // =========================
            if ($item->stock_limit !== null) {

                $totalBought = UserToken::where('user_id', $user->id)
                    ->where('item_id', $item->id)
                    ->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])
                    ->count();

                if ($totalBought >= $item->stock_limit) {
                    return response()->json([
                        'message' => 'Limit pembelian bulan ini sudah habis'
                    ], 400);
                }
            }

            // =========================
            // HITUNG SALDO BARU
            // =========================
            $newBalance = $lastBalance - $item->point_cost;

            // =========================
            // LEDGER (SPEND)
            // =========================
            PointLedger::create([
                'user_id' => $user->id,
                'transaction_type' => 'SPEND',
                'amount' => -$item->point_cost,
                'current_balance' => $newBalance,
                'description' => 'Beli token: ' . $item->item_name
            ]);

            // =========================
            // SIMPAN TOKEN
            // =========================
            UserToken::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'status' => 'AVAILABLE'
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Berhasil beli token',
                'sisa_poin' => $newBalance
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal membeli token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getItems()
    {
        return response()->json([
            'data' => FlexibilityItem::all()
        ]);
    }

    public function myTokens()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $tokens = UserToken::with('item')
            ->where('user_id', $user->id)
            ->where('status', 'AVAILABLE')
            ->get();

        return response()->json([
            'data' => $tokens
        ]);
    }

    public function myPoint()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $lastBalance = PointLedger::where('user_id', $user->id)
            ->latest('id')
            ->value('current_balance') ?? 0;

        return response()->json([
            'point' => $lastBalance
        ]);
    }

    public function pointHistory()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $history = PointLedger::where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->transaction_type,
                    'amount' => $item->amount,
                    'balance' => $item->current_balance,
                    'description' => $item->description,
                    'date' => $item->created_at
                ];
            });

        return response()->json([
            'data' => $history
        ]);
    }


    public function leaderboard()
    {
        $users = User::where('role', 'murid')
            ->with([
                'pointLedgers' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->get()
            ->map(function ($u) {
                $last = $u->pointLedgers->first();

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'point' => $last->current_balance ?? 0
                ];
            })
            ->sortByDesc('point')
            ->values()
            ->map(function ($u, $i) {
                $u['rank'] = $i + 1;
                return $u;
            });

        return response()->json([
            'data' => $users
        ]);
    }




    public function applyPoint($userId, $absen, $lateMinutes, $status = 'hadir')
    {
        $rules = PointRule::where('target_role', 'murid')->get();

        $currentBalance = PointLedger::where('user_id', $userId)
            ->latest('id')
            ->value('current_balance') ?? 0;

        $applied = false;

        foreach ($rules as $rule) {

            $match = false;

            // =========================
            // ALPHA
            // =========================
            if ($rule->condition_type === 'ALPHA' && $status === 'alpha') {
                $match = true;
            }

            // =========================
            // TIME BASED
            // =========================
            if ($rule->condition_type === 'TIME' && $lateMinutes !== null) {

                $min = $rule->min_value ?? 0;
                $max = $rule->max_value;

                if (is_null($max)) {
                    if ($lateMinutes >= $min) {
                        $match = true;
                    }
                } else {
                    if ($lateMinutes >= $min && $lateMinutes <= $max) {
                        $match = true;
                    }
                }
            }

            if (!$match)
                continue;

            $exists = PointLedger::where('absensi_id', $absen->id)
                ->where('description', 'like', $rule->rule_name . '%')
                ->exists();

            if ($exists)
                continue;

            // =========================
            // APPLY POINT
            // =========================
            $amount = $rule->point_modifier;
            $currentBalance += $amount;

            $type = $amount >= 0 ? 'EARN' : 'PENALTY';

            // =========================
            // 🔥 FIX: FORMAT WAKTU LEBIH MANUSIAWI
            // =========================
            $timeText = '';

            if ($lateMinutes !== null) {
                $seconds = $absen->waktu_scan->diffInSeconds($absen->sesiAbsen->dibuka_pada ?? $absen->waktu_scan);

                $min = floor($seconds / 60);
                $sec = $seconds % 60;

                if ($min > 0) {
                    $timeText .= "{$min} menit ";
                }

                $timeText .= "{$sec} detik";
            }

            PointLedger::create([
                'user_id' => $userId,
                'transaction_type' => $type,
                'amount' => $amount,
                'current_balance' => $currentBalance,
                'description' => $rule->rule_name . ($timeText ? " ({$timeText})" : ""),
                'absensi_id' => $absen->id
            ]);

            $applied = true;
            break;
        }

        return $applied ? $currentBalance : null;
    }


    private function formatLateTime($start, $end)
    {
        $seconds = Carbon::parse($start)->diffInSeconds($end);

        $min = floor($seconds / 60);
        $sec = $seconds % 60;

        return [
            'minutes' => $min,
            'seconds' => $sec,
            'text' => ($min > 0 ? "{$min} menit " : "") . "{$sec} detik"
        ];
    }
}




