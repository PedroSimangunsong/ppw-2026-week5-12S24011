<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BukuApiController extends Controller
{
    /**
     * Menampilkan daftar buku dengan pencarian,
     * filter, sorting, dan pagination.
     */
    public function index(Request $request)
    {
        $query = Buku::with('kategori');

        // Pencarian buku.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('isbn', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%");
            });
        }

        // Filter kategori.
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter ketersediaan buku.
        if ($request->filled('tersedia')) {
            if (filter_var($request->tersedia, FILTER_VALIDATE_BOOLEAN)) {
                $query->where('stok', '>', 0);
            } else {
                $query->where('stok', '<=', 0);
            }
        }

        // Sorting.
        $allowedSorts = [
            'judul',
            'tahun_terbit',
            'stok',
            'created_at',
        ];

        $sort = $request->get('sort', 'judul');
        $direction = strtolower($request->get('direction', 'asc'));

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'judul';
        }

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query->orderBy($sort, $direction);

        // Pagination.
        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $bukus = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Daftar buku berhasil diambil.',
            'data' => $bukus->items(),
            'meta' => [
                'current_page' => $bukus->currentPage(),
                'last_page' => $bukus->lastPage(),
                'per_page' => $bukus->perPage(),
                'total' => $bukus->total(),
            ],
            'links' => [
                'first' => $bukus->url(1),
                'last' => $bukus->url($bukus->lastPage()),
                'prev' => $bukus->previousPageUrl(),
                'next' => $bukus->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menampilkan detail buku.
     */
    public function show($id)
    {
        $buku = Buku::with('kategori')->find($id);

        if (!$buku) {
            return $this->problemResponse(
                404,
                'Not Found',
                'Buku tidak ditemukan.'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail buku berhasil diambil.',
            'data' => $buku,
        ]);
    }

    /**
     * Menambahkan buku baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'isbn' => 'required|string|max:20|unique:bukus,isbn',
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1000|max:2100',
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationProblem(
                $validator->errors()->toArray()
            );
        }

        $buku = Buku::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan.',
            'data' => $buku->load('kategori'),
        ], 201)->header(
            'Location',
            url("/api/v1/bukus/{$buku->id}")
        );
    }

    /**
     * Mengubah seluruh data buku (PUT).
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return $this->problemResponse(
                404,
                'Not Found',
                'Buku tidak ditemukan.'
            );
        }

        $validator = Validator::make($request->all(), [
            'isbn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('bukus', 'isbn')->ignore($buku->id),
            ],
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1000|max:2100',
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationProblem(
                $validator->errors()->toArray()
            );
        }

        $buku->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil diperbarui.',
            'data' => $buku->fresh()->load('kategori'),
        ]);
    }

    /**
     * Mengubah sebagian data buku (PATCH).
     */
    public function patch(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return $this->problemResponse(
                404,
                'Not Found',
                'Buku tidak ditemukan.'
            );
        }

        $rules = [
            'isbn' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('bukus', 'isbn')->ignore($buku->id),
            ],
            'judul' => 'sometimes|required|string|max:255',
            'penulis' => 'sometimes|required|string|max:255',
            'penerbit' => 'sometimes|required|string|max:255',
            'tahun_terbit' => 'sometimes|required|integer|min:1000|max:2100',
            'kategori_id' => 'sometimes|required|exists:kategoris,id',
            'stok' => 'sometimes|required|integer|min:0',
            'sinopsis' => 'sometimes|nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationProblem(
                $validator->errors()->toArray()
            );
        }

        $buku->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Sebagian data buku berhasil diperbarui.',
            'data' => $buku->fresh()->load('kategori'),
        ]);
    }

    /**
     * Menghapus buku.
     */
    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return $this->problemResponse(
                404,
                'Not Found',
                'Buku tidak ditemukan.'
            );
        }

        $peminjamanAktif = $buku->peminjamans()
            ->whereNull('tanggal_kembali')
            ->exists();

        if ($peminjamanAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak dapat dihapus karena masih memiliki peminjaman aktif.',
            ], 409);
        }

        $buku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus.',
        ]);
    }

    /**
     * Mengambil referensi eksternal dari OpenLibrary.
     */
    public function referensiEksternal($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return $this->problemResponse(
                404,
                'Not Found',
                'Buku tidak ditemukan.'
            );
        }

        try {
            $options = [
                'curl' => [
                    CURLOPT_RESOLVE => [
                        'openlibrary.org:443:207.241.239.14',
                    ],
                ],
                'allow_redirects' => false,
            ];

            $url = "https://openlibrary.org/isbn/{$buku->isbn}.json";

            $response = Http::acceptJson()
                ->withOptions($options)
                ->connectTimeout(15)
                ->timeout(30)
                ->get($url);

            // Menangani redirect secara manual.
            if ($response->status() >= 300 && $response->status() < 400) {
                $location = $response->header('Location');

                if (
                    !$location ||
                    parse_url($location, PHP_URL_HOST) !== 'openlibrary.org'
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Alamat redirect OpenLibrary tidak valid.',
                    ], 502);
                }

                $response = Http::acceptJson()
                    ->withOptions($options)
                    ->connectTimeout(15)
                    ->timeout(30)
                    ->get($location);
            }

            if ($response->status() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Referensi buku tidak ditemukan di OpenLibrary.',
                ], 404);
            }

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan OpenLibrary sedang mengalami gangguan.',
                ], 502);
            }

            return response()->json([
                'success' => true,
                'message' => 'Referensi eksternal berhasil ditemukan.',
                'data' => $response->json(),
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke OpenLibrary gagal atau mengalami timeout.',
            ], 504);
        }
    }

    /**
     * Respons error RFC 7807.
     */
    private function problemResponse($status, $title, $detail)
    {
        return response()->json([
            'type' => url(
                '/docs/errors/' . strtolower(str_replace(' ', '-', $title))
            ),
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'instance' => request()->getPathInfo(),
        ], $status, [
            'Content-Type' => 'application/problem+json',
        ]);
    }

    /**
     * Respons validasi RFC 7807.
     */
    private function validationProblem($errors)
    {
        return response()->json([
            'type' => url('/docs/errors/validation-failed'),
            'title' => 'Unprocessable Content',
            'status' => 422,
            'detail' => 'Satu atau lebih atribut input tidak memenuhi aturan validasi.',
            'instance' => request()->getPathInfo(),
            'errors' => $errors,
        ], 422, [
            'Content-Type' => 'application/problem+json',
        ]);
    }
}
