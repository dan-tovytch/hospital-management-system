<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NursesController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueriesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register'])->name('user.register');
Route::middleware('throttle:login')->post('/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::put('update/password', [UserController::class, 'updatePassword'])->name("user.updated.password");
    });

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
        Route::put("disable", [NursesController::class, 'disable'])->name("nurse.disable");
        Route::post('records', [NursesController::class, 'medicalRecord'])->name("nurse.records");
    });

    Route::prefix("agenda")->group(function () {
        Route::post("create", [AgendaController::class, 'dailyAgenda'])->name("agenda.create");
        Route::put("update", [AgendaController::class, 'update'])->name("agenda.create");
        Route::get("myagenda", [AgendaController::class, 'myAgenda'])->name("agenda.myagenda");
        Route::get("list", [AgendaController::class, 'listAgenda'])->name("agenda.list");
    });

    Route::prefix("queries")->group(function () {
        Route::post("schedule", [QueriesController::class, 'schedule'])->name("queries.schedule");
        Route::get("list", [QueriesController::class, 'list'])->name("queries.list");
        Route::put("postpone", [QueriesController::class, 'postpone'])->name("queries.postpone");
        Route::delete("cancel", [QueriesController::class, 'cancel'])->name("queries.cancel");
    });

    Route::prefix("admin")->group(function () {
        Route::get("nurses/list", [AdminController::class, "listNurses"])->name("admin.nurses");
    });
});
