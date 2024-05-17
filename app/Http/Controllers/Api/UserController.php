<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Relawan;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'nik' => 'required',
            'tgl_lahir' => 'required',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::error('Validasi gagal.', $validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_type' => 'RELAWAN'
        ]);

        $relawan = Relawan::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'nik' => $request->nik,
            'tgl_lahir' => $request->tgl_lahir,
        ]);

    
        return ResponseHelper::success($relawan, 'Data berhasil disimpan.');
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::error('Validasi gagal.', $validator->errors(), 400);
        }

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);

        if($token == null) {
            return ResponseHelper::error('Email atau Password salah', null, 400);
        }else {
            
            $user = User::where('email', $request->email)->first();

            return ResponseHelper::success([
                'token' => $token,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->role_type
            ], 'User berhasil login');
        }
    }

    public function refreshToken() {
        $newToken = auth()->refresh();

        return ResponseHelper::success([
            'token' => $newToken,
        ], 'Refresh token berhasil');

    }

    public function logout() {
        
        auth()->logout();

        return ResponseHelper::success(message:"Logout Berhasil");
    }
}
