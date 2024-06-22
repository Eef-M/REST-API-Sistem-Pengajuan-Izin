<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "nama_lengkap" => "required",
                "email" => "required|email|unique:users,email",
                "password" => "required"
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "errors" => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                "nama_lengkap" => $request->nama_lengkap,
                "email" => $request->email,
                "password" => $request->password
            ]);

            return response()->json([
                "status" => true,
                "message" => "Registrasi berhasil",
                "token" => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                "email" => "required|email",
                "password" => "required"
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "errors" => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(["email", "password"]))) {
                return response()->json([
                    "status" => false,
                    "message" => "Email atau Password Salah"
                ], 401);
            }

            $user = User::where("email", $request->email)->first();

            $userStatus = $user->verif;

            if ($userStatus === 0) {
                return response()->json([
                    "status" => false,
                    "message" => "Belum terverifikasi!"
                ], 401);
            }

            return response()->json([
                "status" => true,
                "message" => "Login berhasil.",
                "token" => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        $userData = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Profile Information",
            "data" => $userData,
            "id" => auth()->user()->id
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            "status" => true,
            "message" => "User Logged Out",
            "data" => [],
        ], 200);
    }
}
