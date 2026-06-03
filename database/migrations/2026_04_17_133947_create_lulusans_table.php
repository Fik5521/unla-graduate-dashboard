<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lulusans', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('prodi');
            $table->integer('angkatan')->nullable();
            $table->string('fakultas');
            $table->integer('tahun_lulus');
            $table->string('jenis_daftar')->default('Peserta didik baru')->nullable();
            $table->decimal('ipk', 3, 2);
            $table->integer('lama_studi'); // dalam semester
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lulusans');
    }
};
