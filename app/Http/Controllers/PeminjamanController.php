<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    /**
     * Memproses peminjaman buku.
     */
    public function pinjam(Request $request)
    {
        $validated = $request->validate([
            'buku_id' => ['required', 'integer', 'exists:bukus,id'],
            'nim' => ['required', 'string', 'max:255'],
        ]);

        $peminjaman = DB::transaction(function () use ($validated) {
            $buku = Buku::whereKey($validated['buku_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($buku->stok < 1) {
                throw ValidationException::withMessages([
                    'buku_id' => 'Stok buku habis.',
                ]);
            }

            $sudahMeminjam = Peminjaman::where('buku_id', $buku->id)
                ->where('nim', $validated['nim'])
                ->where('sudah_kembali', false)
                ->exists();

            if ($sudahMeminjam) {
                throw ValidationException::withMessages([
                    'buku_id' => 'Mahasiswa ini masih meminjam buku yang sama.',
                ]);
            }

            $buku->decrement('stok');

            return Peminjaman::create([
                'buku_id' => $buku->id,
                'nim' => $validated['nim'],
                'tanggal_pinjam' => now()->toDateString(),
                'sudah_kembali' => false,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Buku berhasil dipinjam.',
                'data' => $peminjaman,
            ], 201);
        }

        return redirect()
            ->route('peminjamans.index')
            ->with('success', 'Buku berhasil dipinjam.');
    }

    /**
     * Memproses pengembalian buku.
     */
    public function kembalikan(Request $request, Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($peminjaman->sudah_kembali) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Buku ini sudah dikembalikan.',
                ]);
            }

            $buku = Buku::whereKey($peminjaman->buku_id)
                ->lockForUpdate()
                ->firstOrFail();

            $buku->increment('stok');

            $peminjaman->update([
                'tanggal_kembali' => now()->toDateString(),
                'sudah_kembali' => true,
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Buku berhasil dikembalikan.',
            ]);
        }

        return redirect()
            ->route('peminjamans.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    /**
     * Statistik peminjaman menggunakan Query Builder.
     */
    public function statistik()
    {
        $totalPeminjaman = DB::table('peminjamans')->count();

        $peminjamanAktif = DB::table('peminjamans')
            ->where('sudah_kembali', false)
            ->count();

        $totalPengembalian = DB::table('peminjamans')
            ->where('sudah_kembali', true)
            ->count();

        $totalBukuTersedia = DB::table('bukus')->sum('stok');

        return response()->json([
            'total_peminjaman' => $totalPeminjaman,
            'peminjaman_aktif' => $peminjamanAktif,
            'total_pengembalian' => $totalPengembalian,
            'total_stok_buku' => $totalBukuTersedia,
        ]);
    }

    /**
     * Statistik jumlah peminjaman setiap buku menggunakan withCount.
     */
    public function statistikBuku()
    {
        $buku = Buku::withCount('peminjamans')
            ->orderBy('judul')
            ->get(['id', 'judul', 'stok']);

        return response()->json($buku);
    }
}
