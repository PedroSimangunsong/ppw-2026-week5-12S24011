<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'buku_id' => $this->buku_id,
            'nim' => $this->nim,
            'tanggal_pinjam' => $this->tanggal_pinjam,
            'tanggal_kembali' => $this->tanggal_kembali,
            'sudah_kembali' => $this->sudah_kembali,

            'buku' => $this->whenLoaded('buku', function () {
                return [
                    'id' => $this->buku->id,
                    'judul' => $this->buku->judul,
                    'isbn' => $this->buku->isbn,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            '_links' => [
                'collection' => [
                    'href' => url('/api/v1/peminjamans'),
                    'method' => 'GET',
                ],

                'kembalikan_buku' => $this->when(
                    !$this->sudah_kembali,
                    [
                        'href' => url(
                            '/api/v1/peminjamans/' . $this->id . '/kembalikan'
                        ),
                        'method' => 'PATCH',
                    ]
                ),
            ],
        ];
    }
}
