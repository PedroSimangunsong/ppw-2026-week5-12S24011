<x-layout title="Daftar Kategori | SIPUS-Del">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="page-header fw-bold mb-1">Daftar Kategori</h2>
            <p class="text-secondary mb-0">
                Kelola kategori koleksi buku perpustakaan IT Del.
            </p>
        </div>

        <a href="{{ route('kategoris.create') }}" class="btn btn-del">
            + Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">No.</th>
                            <th>Kode Kategori</th>
                            <th>Nama Kategori</th>
                            <th>Jumlah Buku</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kategoris as $kategori)
                            <tr>
                                <td class="px-3">
                                    {{ $kategoris->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $kategori->kode_kategori }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $kategori->nama_kategori }}
                                </td>

                                <td>
                                    <span class="badge text-bg-light border">
                                        {{ $kategori->bukus_count }} buku
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('kategoris.show', $kategori) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>

                                        <a href="{{ route('kategoris.edit', $kategori) }}"
                                           class="btn btn-sm btn-outline-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('kategoris.destroy', $kategori) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                                <td colspan="5" class="text-center py-5">
                                    <p class="text-secondary mb-1">
                                        Belum ada kategori.
                                    </p>
                                    <small>Silakan tambahkan kategori baru.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($kategoris->hasPages())
            <div class="card-footer bg-white">
                {{ $kategoris->links() }}
            </div>
        @endif
    </div>

    <div class="text-secondary small mt-3">
        Total kategori: {{ $kategoris->total() }} data
    </div>
</x-layout>
