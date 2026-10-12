<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('bukus')
            ->orderBy('nama_kategori')
            ->paginate(10);

        return view('kategoris.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategoris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:255|unique:kategoris,kode_kategori',
            'nama_kategori' => 'required|string|max:255',
        ]);

        Kategori::create($validated);

        return redirect()
            ->route('kategoris.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Kategori $kategori)
    {
        $kategori->load('bukus');

        return view('kategoris.show', compact('kategori'));
    }

    public function edit(Kategori $kategori)
    {
        return view('kategoris.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:255|unique:kategoris,kode_kategori,' . $kategori->id,
            'nama_kategori' => 'required|string|max:255',
        ]);

        $kategori->update($validated);

        return redirect()
            ->route('kategoris.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();

            return redirect()
                ->route('kategoris.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()
                ->route('kategoris.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh buku.');
        }
    }
}
