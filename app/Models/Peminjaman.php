<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Peminjaman extends Model

{   protected $table = 'peminjamans';
    
    protected $fillable = [
        'buku_id',
        'nim',
        'tanggal_pinjam',
        'tanggal_kembali',
        'sudah_kembali',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
            'sudah_kembali' => 'boolean',
        ];
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }
}
