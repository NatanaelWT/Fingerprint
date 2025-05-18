<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kehadiranstaff;
use App\Models\kehadiransiswa;

class KehadiranController extends Controller
{
    public function kehadiranstaff()
    {
        return view('kehadiran.kehadiranstaff');
    }

    public function kehadiransiswa()
    {
        return view('kehadiran.kehadiransiswa');
    }
}
