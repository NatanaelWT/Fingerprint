<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FingerprintTemplate extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'hex_data'];
}
