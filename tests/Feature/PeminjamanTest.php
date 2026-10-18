<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Login sebagai pengguna untuk setiap pengujian.
        $this->actingAs(User::factory()->create());
    }

    public function test_peminjaman_mengurangi_stok_buku(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'TEST001',
            'nama_kategori' => 'Pengujian',
        ]);

        $buku = Buku::create([
            'isbn' => 'TEST-001',
            'judul' => 'Buku Pengujian',
            'penulis' => 'Penulis Tes',
            'penerbit' => 'Penerbit Tes',
            'tahun_terbit' => 2025,
            'kategori_id' => $kategori->id,
            'stok' => 5,
            'sinopsis' => 'Buku khusus untuk pengujian.',
        ]);

        $response = $this->postJson('/peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
            'sudah_kembali' => 0,
        ]);

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'stok' => 4,
        ]);
    }

    public function test_pengembalian_menambah_stok_buku(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'TEST002',
            'nama_kategori' => 'Pengujian',
        ]);

        $buku = Buku::create([
            'isbn' => 'TEST-002',
            'judul' => 'Buku Pengujian Kembali',
            'penulis' => 'Penulis Tes',
            'penerbit' => 'Penerbit Tes',
            'tahun_terbit' => 2025,
            'kategori_id' => $kategori->id,
            'stok' => 4,
            'sinopsis' => 'Buku untuk pengujian pengembalian.',
        ]);

        $peminjaman = Peminjaman::create([
            'buku_id' => $buku->id,
            'nim' => '12S24011',
            'tanggal_pinjam' => now()->toDateString(),
            'sudah_kembali' => false,
        ]);

        $response = $this->patchJson(
            "/peminjamans/{$peminjaman->id}/kembalikan"
        );

        $response->assertOk();

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'stok' => 5,
        ]);

        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjaman->id,
            'sudah_kembali' => 1,
        ]);
    }

    public function test_mahasiswa_tidak_bisa_meminjam_buku_yang_sama_dua_kali(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'TEST003',
            'nama_kategori' => 'Pengujian',
        ]);

        $buku = Buku::create([
            'isbn' => 'TEST-003',
            'judul' => 'Buku Tes Peminjaman Berulang',
            'penulis' => 'Penulis Tes',
            'penerbit' => 'Penerbit Tes',
            'tahun_terbit' => 2025,
            'kategori_id' => $kategori->id,
            'stok' => 5,
            'sinopsis' => 'Buku untuk menguji peminjaman berulang.',
        ]);

        $responsPertama = $this->postJson('/peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
        ]);

        $responsPertama->assertCreated();

        $responsKedua = $this->postJson('/peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
        ]);

        $responsKedua->assertUnprocessable();

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'stok' => 4,
        ]);

        $this->assertSame(
            1,
            Peminjaman::where('buku_id', $buku->id)
                ->where('nim', '12S24011')
                ->where('sudah_kembali', false)
                ->count()
        );
    }

    public function test_peminjaman_ditolak_jika_stok_buku_habis(): void
    {
        $kategori = Kategori::create([
            'kode_kategori' => 'TEST004',
            'nama_kategori' => 'Pengujian',
        ]);

        $buku = Buku::create([
            'isbn' => 'TEST-004',
            'judul' => 'Buku Tes Stok Habis',
            'penulis' => 'Penulis Tes',
            'penerbit' => 'Penerbit Tes',
            'tahun_terbit' => 2025,
            'kategori_id' => $kategori->id,
            'stok' => 0,
            'sinopsis' => 'Buku untuk menguji kondisi stok habis.',
        ]);

        $response = $this->postJson('/peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseHas('bukus', [
            'id' => $buku->id,
            'stok' => 0,
        ]);

        $this->assertDatabaseMissing('peminjamans', [
            'buku_id' => $buku->id,
            'nim' => '12S24011',
        ]);
    }
}
