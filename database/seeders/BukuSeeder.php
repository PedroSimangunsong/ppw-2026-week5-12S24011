<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriIds = Kategori::query()->pluck('id')->all();

        if (count($kategoriIds) < 5) {
            $this->call(KategoriSeeder::class);
            $kategoriIds = Kategori::query()->pluck('id')->all();
        }

        Buku::factory()->count(20)->create([
            'kategori_id' => fake()->randomElement($kategoriIds),
        ]);
    }
}
