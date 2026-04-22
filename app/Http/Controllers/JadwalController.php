<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalExport;

class JadwalController extends Controller
{
    public function index()
    {
        return view('jadwal.index');
    }

    public function data()
    {
        return Jadwal::with(['kelas', 'mapel', 'guru'])
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai'
        ]);

        Jadwal::create([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return Jadwal::with(['kelas', 'mapel', 'guru'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapel,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai'
        ]);

        $jadwal->update([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Jadwal::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new JadwalExport, 'data-jadwal-' . date('Ymd') . '.xlsx');
    }
}
