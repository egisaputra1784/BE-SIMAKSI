<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjar;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\SesiAbsen;
use App\Models\Absensi;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapAbsenExport;

class RekapAbsenController extends Controller
{
    public function index()
    {
        $tahunAjars = TahunAjar::orderByDesc('aktif')->orderByDesc('id')->get();
        $kelasList  = Kelas::with('tahunAjar')->orderBy('nama_kelas')->get();
        $mapelList  = Mapel::orderBy('nama_mapel')->get();

        return view('rekap-absen.index', compact('tahunAjars', 'kelasList', 'mapelList'));
    }

    public function data(Request $request)
    {
        $query = Absensi::with([
            'murid',
            'sesiAbsen.jadwal.kelas.tahunAjar',
            'sesiAbsen.jadwal.mapel',
            'sesiAbsen.jadwal.guru',
        ])->whereHas('sesiAbsen.jadwal');

        // Filter: tahun ajar
        if ($request->tahun_ajar_id) {
            $query->whereHas('sesiAbsen.jadwal.kelas', function ($q) use ($request) {
                $q->where('tahun_ajar_id', $request->tahun_ajar_id);
            });
        }

        // Filter: kelas
        if ($request->kelas_id) {
            $query->whereHas('sesiAbsen.jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter: mapel
        if ($request->mapel_id) {
            $query->whereHas('sesiAbsen.jadwal', function ($q) use ($request) {
                $q->where('mapel_id', $request->mapel_id);
            });
        }

        // Filter: tanggal mulai
        if ($request->dari) {
            $query->whereHas('sesiAbsen', function ($q) use ($request) {
                $q->where('tanggal', '>=', $request->dari);
            });
        }

        // Filter: tanggal akhir
        if ($request->sampai) {
            $query->whereHas('sesiAbsen', function ($q) use ($request) {
                $q->where('tanggal', '<=', $request->sampai);
            });
        }

        $data = $query->orderByDesc('created_at')->get();

        return response()->json($data->map(function ($a) {
            $sesi   = $a->sesiAbsen;
            $jadwal = $sesi?->jadwal;
            return [
                'id'         => $a->id,
                'murid_id'   => $a->murid_id,
                'nama_murid' => $a->murid?->name ?? '-',
                'nisn'       => $a->murid?->nisn ?? '-',
                'kelas'      => $jadwal?->kelas?->nama_kelas ?? '-',
                'tahun_ajar' => $jadwal?->kelas?->tahunAjar?->nama ?? '-',
                'mapel'      => $jadwal?->mapel?->nama_mapel ?? '-',
                'guru'       => $jadwal?->guru?->name ?? '-',
                'tanggal'    => $sesi?->tanggal ?? '-',
                'status'     => $a->status,
                'waktu_scan' => $a->waktu_scan,
            ];
        }));
    }

    public function summary(Request $request)
    {
        // Ringkasan statistik per murid
        $query = Absensi::with(['sesiAbsen.jadwal'])
            ->whereHas('sesiAbsen.jadwal');

        if ($request->tahun_ajar_id) {
            $query->whereHas('sesiAbsen.jadwal.kelas', fn($q) => $q->where('tahun_ajar_id', $request->tahun_ajar_id));
        }
        if ($request->kelas_id) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('kelas_id', $request->kelas_id));
        }
        if ($request->mapel_id) {
            $query->whereHas('sesiAbsen.jadwal', fn($q) => $q->where('mapel_id', $request->mapel_id));
        }
        if ($request->dari) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '>=', $request->dari));
        }
        if ($request->sampai) {
            $query->whereHas('sesiAbsen', fn($q) => $q->where('tanggal', '<=', $request->sampai));
        }

        $grouped = $query->get()->groupBy('murid_id');

        $result = $grouped->map(function ($records, $muridId) {
            $murid = User::find($muridId);
            $counts = $records->groupBy('status')->map->count();
            $total  = $records->count();
            return [
                'nama_murid' => $murid?->name ?? '-',
                'nisn'       => $murid?->nisn ?? '-',
                'hadir'      => $counts['hadir']    ?? 0,
                'izin'       => $counts['izin']     ?? 0,
                'sakit'      => $counts['sakit']    ?? 0,
                'alpha'      => $counts['alpha']    ?? 0,
                'terlambat'  => $counts['terlambat']?? 0,
                'total'      => $total,
                'pct_hadir'  => $total > 0 ? round((($counts['hadir'] ?? 0) / $total) * 100) : 0,
            ];
        })->values();

        return response()->json($result);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new RekapAbsenExport($request->all()),
            'rekap-absen-' . date('Ymd') . '.xlsx'
        );
    }
}
