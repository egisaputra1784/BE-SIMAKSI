<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\SesiAbsen;
use App\Models\TahunAjar;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMurid = User::where('role', 'murid')->count();
        $totalGuru = User::where('role', 'guru')->count();
        $totalKelas = Kelas::count();

        $today = Carbon::today();

        $totalSesiHariIni = SesiAbsen::whereDate('tanggal', $today)->count();

        $tahunAjar = TahunAjar::where('aktif', true)->first();

        // dummy trend (biar chart ga nangis)
        $trend = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            'murid' => [400, 420, 450, 480, 500, $totalMurid],
            'guru' => [20, 22, 25, 28, 30, $totalGuru]
        ];

        return view('dashboard', compact(
            'totalMurid',
            'totalGuru',
            'totalKelas',
            'totalSesiHariIni',
            'tahunAjar',
            'trend'
        ));
    }
}
