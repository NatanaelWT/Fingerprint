<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;

class KehadiranController extends Controller
{
    // Di dalam KehadiranController
    public function index()
    {
        $logs = LogKehadiran::with(['siswa'])
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        return view('kehadiran', compact('logs'));
    }
}
