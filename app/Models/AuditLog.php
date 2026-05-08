<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'audit_logs';

    // Izinkan kolom aksi dan keterangan diisi otomatis saat import
    protected $fillable = [
        'aksi',
        'keterangan'
    ];
}