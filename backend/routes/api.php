<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GearController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - UTS Konstruksi & Evolusi Perangkat Lunak
| Mahasiswa: Muhammad Abel Abhinaya Riananto (544597)
| Tema: Production Gear & Equipment Management (Hong Films)
|--------------------------------------------------------------------------
*/

// Endpoint login publik (tanpa token)
Route::post('/login', [AuthController::class, 'login']);

// Endpoint terproteksi Sanctum (wajib token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('gears', GearController::class);
});
