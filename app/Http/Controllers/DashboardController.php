<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Staff;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStaff = Staff::count();
        $today = Carbon::today();
        
        $presentCount = LogKehadiran::whereHas('staff')
            ->whereDate('check_in', $today)
            ->whereTime('check_in', '<', '09:00:00')
            ->distinct('id')
            ->count('id');
        
        $checkOutCount = LogKehadiran::whereHas('staff')
            ->whereDate('check_in', $today)
            ->whereTime('check_in', '>=', '09:00:00')
            ->count();

        return view('dashboard', compact(
            'totalStaff',
            'presentCount',
            'checkOutCount'
        ));
    }
}
