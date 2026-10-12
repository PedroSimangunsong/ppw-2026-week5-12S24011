<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            ['kode_kategori' => 'KAT001', 'nama_kategori' => 'Teknologi'],
            ['kode_kategori' => 'KAT002', 'nama_kategori' => 'Novel'],
            ['kode_kategori' => 'KAT003', 'nama_kategori' => 'Sejarah'],
            ['kode_kategori' => 'KAT004', 'nama_kategori' => 'Pendidikan'],
            ['kode_kategori' => 'KAT005', 'nama_kategori' => 'Bisnis'],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::updateOrCreate(
                ['kode_kategori' => $kategori['kode_kategori']],
                ['nama_kategori' => $kategori['nama_kategori']]
            );
        }
    }
}
