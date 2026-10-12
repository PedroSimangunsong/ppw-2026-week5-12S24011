<x-layout title="Edit Kategori | SIPUS-Del">
    <div class="mb-4">
        <h2 class="page-header fw-bold mb-1">Edit Kategori</h2>
        <p class="text-secondary mb-0">
            Perbarui informasi kategori buku.
        </p>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('kategoris.update', $kategori) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="kode_kategori" class="form-label">
                        Kode Kategori
                    </label>

                    <input
                        type="text"
                        name="kode_kategori"
                        id="kode_kategori"
                        class="form-control @error('kode_kategori') is-invalid @enderror"
                        value="{{ old('kode_kategori', $kategori->kode_kategori) }}"
                        required
                    >

                    @error('kode_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="nama_kategori" class="form-label">
                        Nama Kategori
                    </label>

                    <input
                        type="text"
                        name="nama_kategori"
                        id="nama_kategori"
                        class="form-control @error('nama_kategori') is-invalid @enderror"
                        value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                        required
                    >

                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-del">
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('kategoris.index') }}"
                       class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
