<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogKehadiran extends Model
{
    protected $table = 'log_kehadirans'; // Sesuaikan dengan nama tabel di database
    
    protected $fillable = [
        'fingerprint_id',
        'check_in',
    ];

    protected $casts = [
        'check_in' => 'datetime',
    ];
    // Di dalam model LogKehadiran
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'fingerprint_id', 'id_template');
    }

    // Relasi ke model FingerprintTemplate (jika diperlukan)
    public function fingerprintTemplate()
    {
        return $this->belongsTo(FingerprintTemplate::class, 'fingerprint_id');
    }
}