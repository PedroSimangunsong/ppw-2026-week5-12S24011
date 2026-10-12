<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Menampilkan daftar buku.
     */
    
public function index(Request $request)
{
    $query = Buku::with('kategori');

    // Pencarian berdasarkan judul, ISBN, atau penulis
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%");
        });
    }

    // Filter berdasarkan kategori
    if ($request->filled('kategori_id')) {
        $query->where('kategori_id', $request->kategori_id);
    }

    $bukus = $query->latest()->paginate(10)->withQueryString();

    $kategoris = Kategori::orderBy('nama_kategori')->get();

    return view('bukus.index', compact('bukus', 'kategoris'));
}

    /**
     * Menampilkan formulir tambah buku.
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('bukus.create', compact('kategoris'));
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:bukus,isbn',
            'judul' => 'required|string|min:5|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1000|max:'.date('Y'),
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        Buku::create($validated);

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Buku $buku)
    {
        $buku->load('kategori');

        return view('bukus.show', compact('buku'));
    }

    /**
     * Menampilkan formulir edit buku.
     */
    public function edit(Buku $buku)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('bukus.edit', compact('buku', 'kategoris'));
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:bukus,isbn,'.$buku->id,
            'judul' => 'required|string|min:5|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|integer|min:1000|max:'.date('Y'),
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'sinopsis' => 'nullable|string',
        ]);

        $buku->update($validated);

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Menghapus buku.
     */
    public function destroy(Buku $buku)
    {
        $buku->delete();

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
