<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('lulusans', function (Blueprint $table) {
            // Tambah kolom status, default-nya kita anggap 'Lulus' kalau kosong
            $table->string('status')->default('Lulus')->after('ipk'); 
        });
    }

    public function down()
    {
        Schema::table('lulusans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
