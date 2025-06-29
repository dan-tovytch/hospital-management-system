<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NursesController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('address')->group(function () {
        Route::post("register", [AddressController::class, 'store'])->name('address.register');
        Route::put("update", [AddressController::class, 'update'])->name('address.update');
    });
    Route::prefix("patients")->group(function () {
        Route::post("register", [PatientController::class, 'store'])->name("patients.register");
        Route::put("update", [PatientController::class, 'update'])->name("patients.update");
    });
    Route::prefix("nurses")->group(function () {
        Route::post("register", [NursesController::class, 'store'])->name("nurse.register");
        Route::put("update", [NursesController::class, 'update'])->name("nurse.update");
    });
});
