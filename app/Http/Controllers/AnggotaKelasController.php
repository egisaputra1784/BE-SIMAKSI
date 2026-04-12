<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnggotaKelas;

class AnggotaKelasController extends Controller
{
    public function index()
    {
        return view('anggota-kelas.index');
    }

    public function data()
    {
        return AnggotaKelas::with(['kelas', 'murid'])->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'murid_id' => 'required|exists:users,id',
        ]);

        // cegah duplicate (biar gak kena unique error)
        $exists = AnggotaKelas::where('kelas_id', $request->kelas_id)
            ->where('murid_id', $request->murid_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Murid sudah ada di kelas ini'
            ]);
        }

        AnggotaKelas::create([
            'kelas_id' => $request->kelas_id,
            'murid_id' => $request->murid_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        AnggotaKelas::destroy($id);
        return response()->json(['success' => true]);
    }
}
