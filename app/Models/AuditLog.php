<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Tabel yang digunakan (opsional, jika nama tabelmu 'audit_logs')
    protected $table = 'audit_logs';

    // WAJIB ADA: Kolom yang diizinkan untuk diisi secara otomatis
    protected $fillable = [
        'aksi',
        'keterangan',
    ];
}
