<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Absen;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;

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

    public function scan(Request $request)
    {
        $now = Carbon::now(); // Asia/Jakarta jika app timezone sudah benar

        // 1️⃣ Cari siswa
        $siswa = Siswa::where('nis', $request->nis)->first();
        if (!$siswa) {
            return response()->json([
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        // 2️⃣ Cek apakah sudah absen
        $find = Absen::where('siswa_id', $siswa->id)
            ->where('jadwal_pelajaran_id', $request->jadwal_pelajaran_id)
            ->first();

        if ($find) {
            return response()->json([
                'message' => 'Anda sudah melakukan scan untuk siswa ini'
            ], 409);
        }

        // 3️⃣ Ambil jadwal
        $jadwal = JadwalPelajaran::find($request->jadwal_pelajaran_id);
        if (!$jadwal) {
            return response()->json([
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        // 4️⃣ Tentukan status (TERLAMBAT / HADIR)
        $jamMulai = Carbon::createFromFormat('H:i:s', $jadwal->jam_mulai);

        $status = $now->greaterThan($jamMulai)
            ? 'terlambat'
            : 'hadir';

        // 5️⃣ Simpan absen
        $absen = Absen::create([
            'tanggal' => $now->toDateString(),
            'jam' => $now->toTimeString(),
            'siswa_id' => $siswa->id,
            'jadwal_pelajaran_id' => $jadwal->id,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Absen berhasil',
            'data' => $absen
        ], 201);
    }

    public function listJadwal(Request $request)
    {
        $user = $request->user();

        $jadwals = JadwalPelajaran::where('guru')->get();

        return response()->json([
            'jadwals' => $jadwals
        ]);
    }
}
