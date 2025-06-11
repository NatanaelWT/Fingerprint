<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'tahun',
        'nama',
        'kelas',
        'alamat',
        'nomor_ortu',
        'jenis_kelamin',
        'id_template'
    ];
    public function logs()
    {
        return $this->hasMany(LogKehadiran::class, 'fingerprint_id', 'id_template');
    }

}
