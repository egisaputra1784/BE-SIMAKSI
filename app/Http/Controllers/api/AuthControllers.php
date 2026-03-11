<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthControllers extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $login = $request->login;
        $password = $request->password;

        $user = User::where('email', $login)
            ->orWhere('nip', $login)
            ->orWhere('nisn', $login)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 401);
        }

        if (!$token = JWTAuth::attempt([
            'email' => $user->email,
            'password' => $password
        ])) {
            return response()->json(['message' => 'Password salah'], 401);
        }

        // =====================
        // TAMBAHAN DATA KELAS
        // =====================

        $kelasData = null;

        if ($user->role === 'murid') {

            $anggota = AnggotaKelas::with([
                'kelas.tahunAjar',
                'kelas.wali'
            ])
                ->where('murid_id', $user->id)
                ->first();

            if ($anggota) {
                $kelasData = $anggota->kelas;
            }
        }

        return response()->json([
            'token' => $token,
            'user' => $user,
            'kelas' => $kelasData
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'User tidak terautentikasi'
            ], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password lama salah'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }


    public function logout()
    {
        $token = JWTAuth::getToken();

        JWTAuth::invalidate($token);

        return response()->json([
            'message' => 'Logout sukses'
        ]);
    }
}
