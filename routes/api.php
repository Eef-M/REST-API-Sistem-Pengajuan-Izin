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

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("logout", [AuthController::class, "logout"]);

    // Admin
    Route::get("users", [UserController::class, "index"])->middleware("admin");
    Route::post("users", [UserController::class, "store"])->middleware("admin");
    Route::put("users/update-user-status/{id}", [UserController::class, "updateUserStatus"])->middleware("admin");
    Route::put("users/reset-user-password/{id}", [UserController::class, "resetPasswordByAdmin"])->middleware("admin");
    Route::get("izin", [IzinController::class, "index"])->middleware("admin");

    // Verifikator
    Route::put("users/verif-user/{id}", [UserController::class, "verifUser"])->middleware("verifikator");
    Route::put("status-izin/{id}", [IzinController::class, "izinStatus"])->middleware("verifikator");

    // Ordinary User
    Route::post("izin", [IzinController::class, "store"])->middleware("ordinary_user");
    Route::put("izin/{id}", [IzinController::class, "update"])->middleware("ordinary_user");
    Route::delete("izin/{id}", [IzinController::class, "destroy"])->middleware("ordinary_user");
    Route::post("users/update-password", [UserController::class, "updateUserPassword"])->middleware("ordinary_user");
    Route::get("data-izin", [IzinController::class, "showDataByLoginUser"])->middleware("ordinary_user");
    Route::get("cancel-izin/{id}", [IzinController::class, "cancelIzin"])->middleware("ordinary_user");
});
