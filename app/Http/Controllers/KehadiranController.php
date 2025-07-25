<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Siswa;
use App\Models\Staff;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    public function index()
    {
        // Hitung total siswa dan staff yang memiliki id_template
        $totalStudents = Siswa::whereNotNull('id_template')->count();
        $totalStaff = Staff::whereNotNull('id_template')->count();
        $totalPeople = $totalStudents + $totalStaff;

        // Get selected date or default to today
        $selectedDate = request('date') ? Carbon::parse(request('date')) : Carbon::today();

        // Query untuk mendapatkan log kehadiran siswa dan staff
        $logs = LogKehadiran::with(['siswa', 'staff'])
            ->whereDate('check_in', $selectedDate)
            ->where(function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->whereNotNull('id_template');
                })
                    ->orWhereHas('staff', function ($q) {
                        $q->whereNotNull('id_template');
                    });
            })
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        // Hitung jumlah check-in dan check-out
        $checkInCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '<', '09:00:00')
            ->where(function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->whereNotNull('id_template');
                })
                    ->orWhereHas('staff', function ($q) {
                        $q->whereNotNull('id_template');
                    });
            })
            ->count();

        $checkOutCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '>=', '09:00:00')
            ->where(function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->whereNotNull('id_template');
                })
                    ->orWhereHas('staff', function ($q) {
                        $q->whereNotNull('id_template');
                    });
            })
            ->count();

        return view('kehadiran', compact(
            'logs',
            'totalPeople',
            'checkInCount',
            'checkOutCount',
            'selectedDate'
        ));
    }
}
