<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah'
            ]);
        }

        if($user->role !== 'guru') {
            return response()->json([
                'message' => 'Hanya guru yang bisa memakai aplikasi ini'
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
    }

    public function logoutFromDevice(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $request->user()->currentAccessToken())->delete();
    }

    public function refreshToken(Request $request)
    {

        $user = $request->user();
        $user->tokens()->delete();
    
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }
}
