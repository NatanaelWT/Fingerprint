<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'alamat',
        'nomor_telepon',
        'jenis_kelamin',
        'id_template',
    ];
    public function logs()
    {
        return $this->hasMany(LogKehadiran::class, 'fingerprint_id', 'id_template');
    }
}
