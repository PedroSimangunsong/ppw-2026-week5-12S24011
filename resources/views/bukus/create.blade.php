<x-layout title="Tambah Buku | SIPUS-Del">
    <div class="mb-4">
        <a href="{{ route('bukus.index') }}"
           class="text-decoration-none">
            &larr; Kembali ke Daftar Buku
        </a>

        <h2 class="page-header fw-bold mt-3">Tambah Buku</h2>
        <p class="text-secondary">
            Masukkan informasi buku yang akan ditambahkan ke perpustakaan.
        </p>
    </div>

    <div class="card">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Data belum valid.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bukus.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="isbn" class="form-label">ISBN</label>
                        <input type="text" id="isbn" name="isbn"
                               class="form-control"
                               value="{{ old('isbn') }}"
                               maxlength="20" required>
                    </div>

                    <div class="col-md-6">
                        <label for="judul" class="form-label">Judul Buku</label>
                        <input type="text" id="judul" name="judul"
                               class="form-control"
                               value="{{ old('judul') }}"
                               minlength="5" required>
                    </div>

                    <div class="col-md-6">
                        <label for="penulis" class="form-label">Penulis</label>
                        <input type="text" id="penulis" name="penulis"
                               class="form-control"
                               value="{{ old('penulis') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit"
                               class="form-control"
                               value="{{ old('penerbit') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="tahun_terbit" class="form-label">
                            Tahun Terbit
                        </label>
                        <input type="number" id="tahun_terbit"
                               name="tahun_terbit" class="form-control"
                               value="{{ old('tahun_terbit') }}"
                               min="1000" max="{{ date('Y') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="kategori_id" class="form-label">
                            Kategori
                        </label>
                        <select id="kategori_id" name="kategori_id"
                                class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}"
                                    @selected(old('kategori_id') == $kategori->id)>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" id="stok" name="stok"
                               class="form-control"
                               value="{{ old('stok', 0) }}"
                               min="0" required>
                    </div>

                    <div class="col-12">
                        <label for="sinopsis" class="form-label">
                            Sinopsis
                        </label>
                        <textarea id="sinopsis" name="sinopsis"
                                  class="form-control" rows="4"
                                  placeholder="Tuliskan ringkasan isi buku...">{{ old('sinopsis') }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-del">
                        Simpan Buku
                    </button>

                    <a href="{{ route('bukus.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
