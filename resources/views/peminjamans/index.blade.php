<x-layout title="Peminjaman Buku">
    <div class="page-header mb-4">
        <h2 class="fw-bold">Peminjaman Buku</h2>
        <p class="text-muted mb-0">
            Kelola transaksi peminjaman dan pengembalian buku.
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-white fw-bold py-3">
            Form Peminjaman Buku
        </div>

        <div class="card-body p-4">
            <form action="{{ route('peminjamans.pinjam') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="nim" class="form-label">NIM Mahasiswa</label>
                    <input
                        type="text"
                        name="nim"
                        id="nim"
                        class="form-control"
                        value="{{ old('nim') }}"
                        placeholder="Masukkan NIM mahasiswa"
                        required
                        maxlength="255"
                    >
                </div>

                <div class="mb-3">
                    <label for="buku_id" class="form-label">Pilih Buku</label>
                    <select name="buku_id" id="buku_id"
                            class="form-select" required>
                        <option value="">-- Pilih Buku --</option>

                        @foreach ($bukus as $buku)
                            <option
                                value="{{ $buku->id }}"
                                @selected(old('buku_id') == $buku->id)
                                @disabled($buku->stok < 1)
                            >
                                {{ $buku->judul }} — Stok: {{ $buku->stok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-del">
                    Pinjam Buku
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white fw-bold py-3">
            Riwayat Peminjaman
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>NIM</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($peminjamans as $peminjaman)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $peminjaman->nim }}</td>
                                <td>{{ $peminjaman->buku->judul ?? '-' }}</td>
                                <td>
                                    {{ $peminjaman->tanggal_pinjam?->format('d-m-Y') ?? '-' }}
                                </td>
                                <td>
                                    {{ $peminjaman->tanggal_kembali?->format('d-m-Y') ?? '-' }}
                                </td>
                                <td>
                                    @if ($peminjaman->sudah_kembali)
                                        <span class="badge bg-success">
                                            Dikembalikan
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            Dipinjam
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @unless ($peminjaman->sudah_kembali)
                                        <form
                                            action="{{ route('peminjamans.kembalikan', $peminjaman) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-success btn-sm"
                                                onclick="return confirm('Yakin ingin mengembalikan buku ini?')"
                                            >
                                                Kembalikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">Selesai</span>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada transaksi peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>
