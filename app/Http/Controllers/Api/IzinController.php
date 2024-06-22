<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IzinResource;
use App\Models\Izin;
use App\Http\Requests\StoreIzinRequest;
use App\Http\Requests\UpdateIzinRequest;
use App\Models\Komentar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    public function index()
    {
        $izins = Izin::with("komentar")->get();

        return IzinResource::make(true, "Data Izin", $izins);
    }

    public function store(StoreIzinRequest $request)
    {
        try {
            $user = Auth::user();

            $izin = Izin::create([
                ...$request->validated(),
                'user_id' => $user->id,
            ]);

            return IzinResource::make(true, "berhasil membuat izin", $izin);
        } catch (\Throwable $th) {
            return IzinResource::make(false, $th->getMessage(), null);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $izin = Izin::find($id);

            $validator = Validator::make($request->all(), [
                "judul" => "required",
                "isi" => "required",
                "detail" => "required",
                "jenis" => "required|in:cuti,liburan,sakit,lainnya"
            ]);

            if ($validator->fails()) {
                return (IzinResource::make(false, "Validation Error", null))->response()->setStatusCode(400);
            }

            if (!$izin) {
                return (IzinResource::make(false, "Data tidak ditemukan", null))->response()->setStatusCode(404);
            }

            if ($izin->user_id != auth()->user()->id) {
                return (IzinResource::make(false, "Akses ditolak.", null))->response()->setStatusCode(401);
            }

            $izin->update([
                "judul" => $request->judul,
                "isi" => $request->isi,
                "detail" => $request->detail,
                "jenis" => $request->jenis
            ]);

            return IzinResource::make(true, "Izin berhasil di update", $izin);
        } catch (\Throwable $th) {
            return (IzinResource::make(false, $th->getMessage(), null))->response()->setStatusCode(500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {

            $izin = Izin::find($id);

            if (!$izin) {
                return (IzinResource::make(true, "Data tidak ditemukan", null))->response()->setStatusCode(404);
            }

            if ($izin->user_id != auth()->user()->id) {
                return (IzinResource::make(true, "Akses ditolak", null))->response()->setStatusCode(400);
            }

            $izin->delete();

            return IzinResource::make(true, "izin berhasil dihapus", null);
        } catch (\Throwable $th) {
            return IzinResource::make(false, $th->getMessage(), null);
        }
    }

    public function showDataByLoginUser()
    {
        try {
            $user = Auth::user();

            $data = Izin::where("user_id", $user->id)->with("komentar")->get();

            if ($data->isEmpty()) {
                return IzinResource::make(false, "Belum pernah mengajukan izin", $data);
            }

            return IzinResource::make(true, "Data yang pernah diajukan", $data);
        } catch (\Throwable $th) {
            return IzinResource::make(false, $th->getMessage(), null);
        }
    }

    public function cancelIzin($id)
    {
        try {
            $izin = Izin::find($id);

            if (!$izin) {
                return response()->json([
                    "status" => false,
                    "message" => "data not found"
                ], 404);
            }

            if ($izin->user_id != auth()->user()->id) {
                return response()->json([
                    "status" => false,
                    "message" => "Akses ditolak!"
                ], 400);
            }

            $izin->update([
                "status" => "cancel"
            ]);

            return IzinResource::make(true, "Pengajuan izin dibatalkan.", $izin);
        } catch (\Throwable $th) {
            return IzinResource::make(false, $th->getMessage(), null);
        }
    }

    public function izinStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                "status" => "required|in:pending,approved,rejected,cancel",
                "komentar" => "nullable"
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Validation Error",
                    "note" => "Harus berupa pending, approved atau rejected,",
                    "error" => $validator->errors()
                ], 400);
            }

            $izin = Izin::find($id);

            if (!$izin) {
                return IzinResource::make(false, "Data tidak di temukan", null);
            }

            if ($request->status === "cancel") {
                return IzinResource::make(false, "Akses ditolak. hanya user tersebut yang bisa membatalkan izin", null);
            }

            $izin->update([
                "status" => $request->status
            ]);

            if ($request->has("komentar")) {
                $komentar = new Komentar();
                $komentar->izin_id = $izin->id;
                $komentar->user_id = auth()->user()->id;
                $komentar->komentar = $request->input("komentar");
                $komentar->save();
            }

            return IzinResource::make(true, "Status berhasil di ubah", $izin);

        } catch (\Throwable $th) {
            return IzinResource::make(true, $th->getMessage(), null);
        }
    }
}
