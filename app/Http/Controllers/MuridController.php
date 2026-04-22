<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MuridExport;

class MuridController extends Controller
{
    public function index()
    {
        return view('murid.index');
    }

    public function data()
    {
        $murid = User::where('role', 'murid')->latest()->get();
        return response()->json($murid);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'nisn' => 'required|unique:users,nisn',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'password' => bcrypt($request->password),
            'role' => 'murid'
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $murid = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,$id",
            'nisn' => "required|unique:users,nisn,$id",
        ]);

        $murid->update([
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new MuridExport, 'data-murid-' . date('Ymd') . '.xlsx');
    }
}
