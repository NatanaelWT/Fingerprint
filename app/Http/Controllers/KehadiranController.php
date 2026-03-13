<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Staff;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    public function index()
    {
        // Hitung total staff yang memiliki id_template
        $totalStaff = Staff::whereNotNull('id_template')->count();

        // Get selected date or default to today
        $selectedDate = request('date') ? Carbon::parse(request('date')) : Carbon::today();

        // Query untuk mendapatkan log kehadiran staff
        $logs = LogKehadiran::with('staff')
            ->whereDate('check_in', $selectedDate)
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
            })
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        // Hitung jumlah check-in dan check-out
        $checkInCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '<', '09:00:00')
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
            })
            ->count();

        $checkOutCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '>=', '09:00:00')
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
            })
            ->count();

        return view('kehadiran', compact(
            'logs',
            'totalStaff',
            'checkInCount',
            'checkOutCount',
            'selectedDate'
        ));
    }
}
