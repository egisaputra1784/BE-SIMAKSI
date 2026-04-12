<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function data()
    {
        $admins = User::where('role', 'admin')->latest()->get();
        return response()->json($admins);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'admin'
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $admin = User::findOrFail($id);
        return response()->json($admin);
    }

    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,$id",
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // kalau password diisi → update
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $admin->update($data);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['success' => true]);
    }
}
