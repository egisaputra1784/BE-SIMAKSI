<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GuruExport;

class GuruController extends Controller
{
    public function index()
    {
        return view('guru.index');
    }

    public function data()
    {
        $guru = User::where('role', 'guru')->latest()->get();
        return response()->json($guru);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|unique:users,nip',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => bcrypt($request->password),
            'role' => 'guru'
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $guru = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,$id",
            'nip' => "required|unique:users,nip,$id",
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $guru->update($data);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new GuruExport, 'data-guru-' . date('Ymd') . '.xlsx');
    }
}
