<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'penerbit' => $this->penerbit,
            'tahun_terbit' => $this->tahun_terbit,
            'kategori_id' => $this->kategori_id,

            'kategori' => $this->whenLoaded('kategori', function () {
                return [
                    'id' => $this->kategori->id,
                    'nama_kategori' => $this->kategori->nama_kategori,
                ];
            }),

            'stok' => $this->stok,
            'sinopsis' => $this->sinopsis,

            'jumlah_peminjaman' => $this->whenCounted('peminjamans'),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            '_links' => [
                'self' => [
                    'href' => url('/api/v1/bukus/' . $this->id),
                ],
                'collection' => [
                    'href' => url('/api/v1/bukus'),
                ],
                'pinjam_buku' => $this->when($this->stok > 0, [
                    'href' => url('/api/v1/peminjamans'),
                    'method' => 'POST',
                ]),
            ],
        ];
    }
}
