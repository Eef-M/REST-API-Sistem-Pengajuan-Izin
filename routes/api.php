<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IzinController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, "register"]);
    Route::post('login', [AuthController::class, "login"]);
});

Route::group([
    "middleware" => ["auth:sanctum"]
], function () {
    Route::get("profile", [AuthController::class, "profile"]);
    Route::get("logout", [AuthController::class, "logout"]);
});

Route::apiResource("users", UserController::class);
Route::put("users/update-user-role/{id}", [UserController::class, "updateUserStatus"]);
Route::put("users/reset-user-password/{id}", [UserController::class, "resetPasswordByAdmin"]);
Route::post("users/update-password", [UserController::class, "updateUserPassword"])->middleware("auth:sanctum");
Route::put("users/verif-user/{id}", [UserController::class, "verifUser"])->middleware("auth:sanctum");

Route::apiResource("izin", IzinController::class)->middleware("auth:sanctum");
Route::get("data-izin", [IzinController::class, "showDataByLoginUser"])->middleware("auth:sanctum");
Route::get("cancel-izin/{id}", [IzinController::class, "cancelIzin"])->middleware("auth:sanctum");
Route::put("status-izin/{id}", [IzinController::class, "izinStatus"])->middleware("auth:sanctum");

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
