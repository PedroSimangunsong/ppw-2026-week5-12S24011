<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;

// Halaman utama
Route::get('/', function () {
    return redirect()->route('bukus.index');
});

// Login hanya untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

// Halaman yang membutuhkan login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Pengujian cookie
    Route::get('/cookie', function () {
        return response('Cookie SIPUS-Del berhasil diuji.')
            ->cookie(
                'sipus_preferensi',
                'tampilan_ungu',
                60
            );
    })->name('cookie.test');

    // Statistik buku
    Route::get('/bukus/statistik-peminjaman', [
        PeminjamanController::class,
        'statistikBuku'
    ])->name('bukus.statistik-peminjaman');

    // CRUD Buku dan Kategori
    Route::resource('bukus', BukuController::class);
    Route::resource('kategoris', KategoriController::class);

    // Statistik peminjaman
    Route::get('/peminjamans/statistik', [
        PeminjamanController::class,
        'statistik'
    ])->name('peminjamans.statistik');

    
    // Halaman daftar dan form peminjaman
    Route::get('/peminjamans', function () {
        $bukus = \App\Models\Buku::orderBy('judul')->get();

        $peminjamans = \App\Models\Peminjaman::with('buku')
            ->orderByDesc('tanggal_pinjam')
            ->get();

        return view('peminjamans.index', compact('bukus', 'peminjamans'));
    })->name('peminjamans.index');


    // Peminjaman buku
    Route::post('/peminjamans', [
        PeminjamanController::class,
        'pinjam'
    ])->name('peminjamans.pinjam');

    // Pengembalian buku
    Route::patch('/peminjamans/{peminjaman}/kembalikan', [
        PeminjamanController::class,
        'kembalikan'
    ])->name('peminjamans.kembalikan');
});
