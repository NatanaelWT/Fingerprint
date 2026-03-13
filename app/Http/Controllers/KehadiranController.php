<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogKehadiran;
<<<<<<< HEAD
=======
use App\Models\Siswa;
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562
use App\Models\Staff;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        // Hitung total staff yang memiliki id_template
        $totalStaff = Staff::whereNotNull('id_template')->count();
=======
        // Hitung total siswa dan staff yang memiliki id_template
        $totalStudents = Siswa::whereNotNull('id_template')->count();
        $totalStaff = Staff::whereNotNull('id_template')->count();
        $totalPeople = $totalStudents + $totalStaff;
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562

        // Get selected date or default to today
        $selectedDate = request('date') ? Carbon::parse(request('date')) : Carbon::today();

<<<<<<< HEAD
        // Query untuk mendapatkan log kehadiran staff
        $logs = LogKehadiran::with('staff')
            ->whereDate('check_in', $selectedDate)
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
=======
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
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562
            })
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        // Hitung jumlah check-in dan check-out
        $checkInCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '<', '09:00:00')
<<<<<<< HEAD
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
=======
            ->where(function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->whereNotNull('id_template');
                })
                    ->orWhereHas('staff', function ($q) {
                        $q->whereNotNull('id_template');
                    });
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562
            })
            ->count();

        $checkOutCount = LogKehadiran::whereDate('check_in', $selectedDate)
            ->whereTime('check_in', '>=', '09:00:00')
<<<<<<< HEAD
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
=======
            ->where(function ($query) {
                $query->whereHas('siswa', function ($q) {
                    $q->whereNotNull('id_template');
                })
                    ->orWhereHas('staff', function ($q) {
                        $q->whereNotNull('id_template');
                    });
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562
            })
            ->count();

        return view('kehadiran', compact(
            'logs',
<<<<<<< HEAD
            'totalStaff',
=======
            'totalPeople',
>>>>>>> 88b4e3dbf90c7c3d02312e075ebd6c8f9803d562
            'checkInCount',
            'checkOutCount',
            'selectedDate'
        ));
    }
}
