<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\TahunAjar;
use App\Models\User;

class KelasController extends Controller
{
    public function index()
    {
        return view('kelas.index');
    }

    public function data()
    {
        return Kelas::with(['tahunAjar', 'wali'])->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'tahun_ajar_id' => 'required|exists:tahun_ajar,id',
        ]);

        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'tahun_ajar_id' => $request->tahun_ajar_id,
            'wali_guru_id' => $request->wali_guru_id
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return Kelas::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = Kelas::findOrFail($id);

        $data->update([
            'nama_kelas' => $request->nama_kelas,
            'tahun_ajar_id' => $request->tahun_ajar_id,
            'wali_guru_id' => $request->wali_guru_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Kelas::destroy($id);
        return response()->json(['success' => true]);
    }
}
