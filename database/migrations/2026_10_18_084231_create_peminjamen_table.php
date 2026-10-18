<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('buku_id')
                ->constrained('bukus')
                ->cascadeOnDelete();

            $table->string('nim');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->boolean('sudah_kembali')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};
