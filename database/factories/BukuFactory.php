<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

class BukuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'isbn' => fake()->unique()->numerify('978##########'),
            'judul' => fake()->sentence(4),
            'penulis' => fake()->name(),
            'penerbit' => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(2000, 2026),
            'kategori_id' => Kategori::query()->inRandomOrder()->value('id')
                ?? Kategori::factory(),
            'stok' => fake()->numberBetween(0, 50),
            'sinopsis' => fake()->paragraph(),
        ];
    }
}
