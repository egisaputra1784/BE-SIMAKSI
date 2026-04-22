<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjar;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TahunAjarExport;

class TahunAjarController extends Controller
{
    public function index()
    {
        return view('tahun-ajar.index');
    }

    public function data()
    {
        return TahunAjar::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required'
        ]);

        // kalau dicentang aktif → matiin yang lain
        if ($request->aktif) {
            TahunAjar::where('aktif', true)->update(['aktif' => false]);
        }

        TahunAjar::create([
            'nama' => $request->nama,
            'aktif' => $request->aktif ? true : false
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return TahunAjar::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = TahunAjar::findOrFail($id);

        // kalau diupdate jadi aktif → matiin yang lain
        if ($request->aktif) {
            TahunAjar::where('aktif', true)
                ->where('id', '!=', $id)
                ->update(['aktif' => false]);
        }

        $data->update([
            'nama' => $request->nama,
            'aktif' => $request->aktif ? true : false
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        TahunAjar::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new TahunAjarExport, 'data-tahun-ajar-' . date('Ymd') . '.xlsx');
    }
}
