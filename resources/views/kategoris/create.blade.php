<x-layout title="Tambah Kategori | SIPUS-Del">
    <div class="mb-4">
        <h2 class="page-header fw-bold mb-1">Tambah Kategori</h2>
        <p class="text-secondary mb-0">
            Masukkan informasi kategori buku baru.
        </p>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <form action="{{ route('kategoris.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="kode_kategori" class="form-label">
                        Kode Kategori
                    </label>

                    <input
                        type="text"
                        name="kode_kategori"
                        id="kode_kategori"
                        class="form-control @error('kode_kategori') is-invalid @enderror"
                        value="{{ old('kode_kategori') }}"
                        placeholder="Contoh: KAT006"
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
                        value="{{ old('nama_kategori') }}"
                        placeholder="Contoh: Komputer dan Teknologi"
                        required
                    >

                    @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-del">
                        Simpan Kategori
                    </button>

                    <a href="{{ route('kategoris.index') }}"
                       class="btn btn-outline-secondary">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layout>
