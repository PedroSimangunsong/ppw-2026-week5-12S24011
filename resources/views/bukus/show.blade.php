<x-layout title="Detail Buku | SIPUS-Del">
    <div class="mb-4">
        <a href="{{ route('bukus.index') }}" class="text-decoration-none">
            &larr; Kembali ke Daftar Buku
        </a>

        <h2 class="page-header fw-bold mt-3">Detail Buku</h2>
        <p class="text-secondary">
            Informasi lengkap buku dalam koleksi perpustakaan.
        </p>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-2">{{ $buku->judul }}</h4>
                    <span class="badge text-bg-light border">
                        {{ $buku->kategori->nama_kategori }}
                    </span>
                </div>

                <a href="{{ route('bukus.edit', $buku) }}"
                   class="btn btn-del align-self-start">
                    Edit Buku
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <tbody>
                        <tr>
                            <th style="width: 200px">ISBN</th>
                            <td>{{ $buku->isbn }}</td>
                        </tr>
                        <tr>
                            <th>Judul Buku</th>
                            <td>{{ $buku->judul }}</td>
                        </tr>
                        <tr>
                            <th>Penulis</th>
                            <td>{{ $buku->penulis }}</td>
                        </tr>
                        <tr>
                            <th>Penerbit</th>
                            <td>{{ $buku->penerbit }}</td>
                        </tr>
                        <tr>
                            <th>Tahun Terbit</th>
                            <td>{{ $buku->tahun_terbit }}</td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>{{ $buku->kategori->nama_kategori }}</td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td>{{ $buku->stok }} buku</td>
                        </tr>
                        <tr>
                            <th>Sinopsis</th>
                            <td>
                                {{ $buku->sinopsis ?: 'Belum ada sinopsis.' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <a href="{{ route('bukus.index') }}"
               class="btn btn-outline-secondary mt-3">
                Kembali
            </a>
        </div>
    </div>
</x-layout>
