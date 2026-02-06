<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Contracts\Auth\Guard;


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

        // cari user berdasarkan email / nip / nisn
        $user = \App\Models\User::where('email', $login)
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

        return response()->json([
            'token' => $token,
            'user' => $user
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
