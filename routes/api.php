<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\BukuApiController;
use App\Http\Controllers\Api\V1\PeminjamanApiController;

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'REST API SIPUS-Del berhasil dijalankan!',
    ]);
});

Route::prefix('v1')->group(function () {

    // Autentikasi
    Route::post('/auth/login', [AuthTokenController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthTokenController::class, 'me']);
        Route::post('/auth/logout', [AuthTokenController::class, 'logout']);
    });

   // API Buku - dapat diakses tanpa login untuk GET
   Route::get('/bukus', [BukuApiController::class, 'index']);
   Route::get('/bukus/{id}/referensi-eksternal', [BukuApiController::class, 'referensiEksternal']);
   Route::get('/bukus/{id}', [BukuApiController::class, 'show']);

    // API Buku - memerlukan autentikasi
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/bukus', [BukuApiController::class, 'store']);
        Route::put('/bukus/{id}', [BukuApiController::class, 'update']);
        Route::patch('/bukus/{id}', [BukuApiController::class, 'patch']);
        Route::delete('/bukus/{id}', [BukuApiController::class, 'destroy']);
    });

    // API Peminjaman - memerlukan autentikasi
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/peminjamans', [PeminjamanApiController::class, 'index']);
        Route::post('/peminjamans', [PeminjamanApiController::class, 'store']);
        Route::patch('/peminjamans/{id}/kembalikan', [PeminjamanApiController::class, 'kembalikan']);
    });

});
