<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function index()
    {
        $users = User::where('status', '!=', 'admin')->get();
        return UserResource::make(true, "User Data", $users);
    }

    public function store(Request $request)
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
                ], 400);
            }

            $user = User::create([
                "nama_lengkap" => $request->nama_lengkap,
                "email" => $request->email,
                "password" => $request->password,
                "status" => "verifikator",
                "verif" => 1
            ]);

            return (UserResource::make(true, "Berhasil menambahkan verifikator", $user))->response()->setStatusCode(201);

        } catch (\Throwable $th) {
            return (UserResource::make(false, $th->getMessage(), null))->response()->setStatusCode(500);
        }
    }

    public function updateUserStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "status" => "required|in:admin,verifikator,ordinary_user",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "note" => "Harus berupa admin, verifikator atau ordinary_user",
                    "errors" => $validator->errors()
                ], 400);
            }

            $user = User::find($id);

            if (!$user) {
                return (UserResource::make(false, "user tidak ditemukan", null))->response()->setStatusCode(404);
            }

            if ($user->verif === 0) {
                return (UserResource::make(false, "user belum terverifikasi", null))->response()->setStatusCode(400);
            }

            $prohibitedFields = ['nama_lengkap', 'email', 'password', 'verif'];

            foreach ($prohibitedFields as $field) {
                if ($request->has($field)) {
                    return (UserResource::make(false, $field . " tidak diperbolehkan", null))->response()->setStatusCode(400);
                }
            }

            $user->update([
                "status" => $request->input("status")
            ]);

            return UserResource::make(true, "status berhasil diupdate!", $user);

        } catch (\Throwable $th) {
            return (UserResource::make(false, $th->getMessage(), null))->response()->setStatusCode(500);
        }
    }

    public function updateUserPassword(Request $request)
    {
        try {
            $request->validate([
                "password_lama" => "required",
                "password_baru" => "required",
                "konfirmasi_password_baru" => "required"
            ]);

            $user = Auth::user();

            if (!Hash::check($request->password_lama, $user->password)) {
                return response()->json([
                    "status" => false,
                    "message" => "password lama salah."
                ], 401);
            }

            if ($request->password_baru != $request->konfirmasi_password_baru) {
                return response()->json([
                    "status" => false,
                    "message" => "password baru tidak cocok dengan konfirmasi password baru."
                ], 401);
            }

            $user->password = Hash::make($request->password_baru);
            $user->save();

            return response()->json([
                "status" => true,
                "message" => "Password berhasil di update."
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "error" => $th->getMessage()
            ], 500);
        }
    }

    public function resetPasswordByAdmin(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "new_password" => "required"
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error.",
                    "errors" => $validator->errors()
                ], 400);
            }

            $user = User::find($id);

            if (!$user) {
                return (UserResource::make(false, "user tidak ditemukan", null))->response()->setStatusCode(404);
            }

            if ($user->role === "admin") {
                return (UserResource::make(false, "Password tidak bisa di reset. " . $user->nama_lengkap . " adalah Admin.", null))->response()->setStatusCode(400);
            }

            $user->update([
                "password" => $request->new_password
            ]);

            return UserResource::make(true, "Password berhasil diubah.", $user);

        } catch (\Throwable $th) {
            return (UserResource::make(false, $th->getMessage(), null))->response()->setStatusCode(500);
        }
    }

    public function verifUser(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return (UserResource::make(false, "User tidak di temukan.", null))->response()->setStatusCode(404);
            }

            if ($user->status === "admin") {
                return (UserResource::make(false, "Akses di tolak. Admin tidak bisa di ubah verifikasi akun", null))->response()->setStatusCode(400);
            }

            if ($user->verif === 1) {
                return (UserResource::make(false, "User sudah terverifikasi", null))->response()->setStatusCode(400);
            }

            $user->update([
                "verif" => 1
            ]);

            return UserResource::make(true, "User terverifikasi", $user);
        } catch (\Throwable $th) {
            return (UserResource::make(true, $th->getMessage(), null))->response()->setStatusCode(500);
        }
    }
}
