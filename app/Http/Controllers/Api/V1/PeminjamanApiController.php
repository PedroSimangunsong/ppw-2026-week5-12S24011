<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PeminjamanApiController extends Controller
{
    // Menampilkan seluruh data peminjaman
    public function index()
    {
        $peminjamans = Peminjaman::with('buku')
            ->latest()
            ->paginate(10);

        return PeminjamanResource::collection($peminjamans)
            ->additional([
                'success' => true,
            ]);
    }

    // Menambahkan peminjaman buku
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'buku_id' => 'required|exists:bukus,id',
            'nim' => 'required|string|max:20',
            'tanggal_pinjam' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($request) {
            $buku = Buku::whereKey($request->buku_id)
                ->lockForUpdate()
                ->first();

            if (!$buku || $buku->stok <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok buku habis, peminjaman ditolak.',
                ], 409);
            }

            $peminjaman = Peminjaman::create([
                'buku_id' => $buku->id,
                'nim' => $request->nim,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'sudah_kembali' => false,
            ]);

            $buku->decrement('stok');

            $peminjaman->load('buku');

            return (new PeminjamanResource($peminjaman))
                ->additional([
                    'success' => true,
                    'message' => 'Peminjaman buku berhasil.',
                ])
                ->response()
                ->setStatusCode(201);
        });
    }

    // Mengembalikan buku
    public function kembalikan($id)
    {
        return DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::whereKey($id)
                ->lockForUpdate()
                ->first();

            if (!$peminjaman) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data peminjaman tidak ditemukan.',
                ], 404);
            }

            if ($peminjaman->sudah_kembali) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku sudah dikembalikan sebelumnya.',
                ], 409);
            }

            $buku = Buku::whereKey($peminjaman->buku_id)
                ->lockForUpdate()
                ->first();

            $peminjaman->update([
                'sudah_kembali' => true,
                'tanggal_kembali' => now()->toDateString(),
            ]);

            $buku->increment('stok');

            $peminjaman->load('buku');

            return (new PeminjamanResource($peminjaman))
                ->additional([
                    'success' => true,
                    'message' => 'Buku berhasil dikembalikan.',
                ]);
        });
    }
}
