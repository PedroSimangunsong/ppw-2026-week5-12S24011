<x-layout title="Detail Kategori | SIPUS-Del">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-header fw-bold mb-1">Detail Kategori</h2>
            <p class="text-secondary mb-0">
                Informasi kategori dan koleksi buku terkait.
            </p>
        </div>

        <a href="{{ route('kategoris.index') }}"
           class="btn btn-outline-secondary">
            Kembali ke Kategori
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="text-secondary mb-1">Kode Kategori</p>
                    <h5 class="fw-semibold">{{ $kategori->kode_kategori }}</h5>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Nama Kategori</p>
                    <h5 class="fw-semibold">{{ $kategori->nama_kategori }}</h5>
                </div>

                <div class="col-md-6">
                    <p class="text-secondary mb-1">Jumlah Buku</p>
                    <h5 class="fw-semibold">{{ $kategori->bukus->count() }} buku</h5>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('kategoris.edit', $kategori) }}"
                   class="btn btn-del">
                    Edit Kategori
                </a>
            </div>
        </div>
    </div>

    <h4 class="page-header fw-bold mb-3">Daftar Buku dalam Kategori</h4>

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
                            <th>Tahun Terbit</th>
                            <th>Stok</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kategori->bukus as $buku)
                            <tr>
                                <td class="px-3">{{ $loop->iteration }}</td>
                                <td>{{ $buku->isbn }}</td>
                                <td class="fw-semibold">{{ $buku->judul }}</td>
                                <td>{{ $buku->penulis }}</td>
                                <td>{{ $buku->tahun_terbit }}</td>
                                <td>{{ $buku->stok }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">
                                    Belum ada buku dalam kategori ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
