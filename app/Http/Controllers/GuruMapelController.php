<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuruMapel;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruMapelExport;

class GuruMapelController extends Controller
{
    public function index()
    {
        return view('guru-mapel.index');
    }

    public function data()
    {
        return GuruMapel::with(['guru', 'mapel'])->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'mapel_id' => 'required|exists:mapel,id',
        ]);

        GuruMapel::create([
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return GuruMapel::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = GuruMapel::findOrFail($id);

        $data->update([
            'guru_id' => $request->guru_id,
            'mapel_id' => $request->mapel_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        GuruMapel::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new GuruMapelExport, 'data-guru-mapel-' . date('Ymd') . '.xlsx');
    }
}
