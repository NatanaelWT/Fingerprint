<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Siswa;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Siswa::count();
        $today = Carbon::today();
        
        $presentCount = LogKehadiran::whereHas('siswa')
            ->whereDate('check_in', $today)
            ->whereTime('check_in', '<', '10:00:00')
            ->distinct('id')
            ->count('id');
        
        $checkOutCount = LogKehadiran::whereHas('siswa')
            ->whereDate('check_in', $today)
            ->whereTime('check_in', '>=', '15:00:00')
            ->count();

        return view('dashboard', compact(
            'totalStudents',
            'presentCount',
            'checkOutCount'
        ));
    }
}