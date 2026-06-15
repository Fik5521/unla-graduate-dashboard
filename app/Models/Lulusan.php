<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lulusan extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'lulusans';

    // Izinkan semua kolom diisi secara massal kecuali 'id'
    protected $guarded = ['id'];

    // Relasi untuk mengetahui user/admin siapa yang menambahkan data ini
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}