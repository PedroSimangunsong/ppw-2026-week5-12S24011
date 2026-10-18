<x-layout title="Daftar Buku | SIPUS-Del">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-header fw-bold mb-1">Daftar Buku</h2>
            <p class="text-secondary mb-0">
                Kelola koleksi buku perpustakaan IT Del.
            </p>
        </div>

        <a href="{{ route('bukus.create') }}" class="btn btn-del">
            + Tambah Buku
        </a>
    </div>

    {{-- Form pencarian dan filter kategori --}}
    <form action="{{ route('bukus.index') }}" method="GET" class="card p-3 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label">Cari Buku</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    class="form-control"
                    placeholder="Judul, ISBN, atau penulis..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-md-4">
                <label for="kategori_id" class="form-label">Filter Kategori</label>
                <select
                    name="kategori_id"
                    id="kategori_id"
                    class="form-select"
                >
                    <option value="">Semua Kategori</option>

                    @foreach ($kategoris as $kategori)
                        <option
                            value="{{ $kategori->id }}"
                            @selected(request('kategori_id') == $kategori->id)
                        >
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-del">
                    Cari / Filter
                </button>

                <a href="{{ route('bukus.index') }}"
                   class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Tabel daftar buku --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">No.</th>
                            <th>ISBN</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bukus as $buku)
                            <tr>
                                <td class="px-3">
                                    {{ $bukus->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <small>{{ $buku->isbn }}</small>
                                </td>

                                <td class="fw-semibold">
                                    {{ $buku->judul }}
                                </td>

                                <td>{{ $buku->penulis }}</td>

                                <td>
                                    <span class="badge text-bg-light border">
                                       {{ $buku->kategori?->nama_kategori ?? 'Tanpa Kategori' }}
                                    </span>
                                </td>

                                <td>{{ $buku->tahun_terbit }}</td>

                                <td>
                                    @if ($buku->stok > 0)
                                        <span class="badge text-bg-success">
                                            {{ $buku->stok }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-danger">
                                            Habis
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('bukus.show', $buku) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>

                                        <a href="{{ route('bukus.edit', $buku) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('bukus.destroy', $buku) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <p class="text-secondary mb-1">
                                        Buku tidak ditemukan.
                                    </p>
                                    <small>
                                        Coba kata kunci atau kategori lain.
                                    </small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($bukus->hasPages())
            <div class="card-footer bg-white">
                {{ $bukus->links() }}
            </div>
        @endif
    </div>

    <div class="text-secondary small mt-3">
        Total buku ditemukan: {{ $bukus->total() }} data
    </div>
</x-layout>
