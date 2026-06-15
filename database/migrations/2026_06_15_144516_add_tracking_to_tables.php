<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambah user_id dan ip_address di tabel audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
        });

        // Tambah created_by di tabel lulusans
        Schema::table('lulusans', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'ip_address']);
        });

        Schema::table('lulusans', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by']);
        });
    }
};