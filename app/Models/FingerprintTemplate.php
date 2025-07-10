<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintTemplate extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hex_data'];
    
    // Tambahkan relasi jika diperlukan
    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'id_template', 'id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'id_template', 'id');
    }
}