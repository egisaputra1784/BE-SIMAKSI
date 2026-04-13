<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mapel;

class MapelController extends Controller
{
    public function index()
    {
        return view('mapel.index');
    }

    public function data()
    {
        return Mapel::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mapel' => 'required',
            'kode_mapel' => 'required'
        ]);

        Mapel::create([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return Mapel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = Mapel::findOrFail($id);

        $data->update([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Mapel::destroy($id);
        return response()->json(['success' => true]);
    }
}
