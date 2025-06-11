<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Siswa;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    public function index()
    {
        // Hanya hitung siswa yang memiliki id_template
        $totalStudents = Siswa::whereNotNull('id_template')->count();
        
        // Get selected date or default to today
        $selectedDate = request('date') ? Carbon::parse(request('date')) : Carbon::today();
        
        // Initialize query dengan kondisi siswa memiliki template
        $query = LogKehadiran::with(['siswa'])
            ->whereDate('check_in', $selectedDate)
            ->whereHas('siswa', function($query) {
                $query->whereNotNull('id_template'); // Hanya siswa dengan template
            });
        
        // Get logs for table
        $logs = $query->orderBy('check_in', 'desc')->paginate(20);
        
        // Count check-ins hanya untuk siswa dengan template
        $checkInCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '<', '12:00:00')
            ->whereHas('siswa', function($query) {
                $query->whereNotNull('id_template');
            })
            ->distinct('id')
            ->count('id');
        
        // Count check-outs hanya untuk siswa dengan template
        $checkOutCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '>=', '12:00:00')
            ->whereHas('siswa', function($query) {
                $query->whereNotNull('id_template');
            })
            ->count();

        return view('kehadiran', compact(
            'logs',
            'totalStudents',
            'checkInCount',
            'checkOutCount',
            'selectedDate'
        ));
    }
}