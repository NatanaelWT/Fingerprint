<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'nip',
        'tahun',
        'nama',
        'jabatan',
        'alamat',
        'nomor_telepon',
        'jenis_kelamin',
    ];
}
