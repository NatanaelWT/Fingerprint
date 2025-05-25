<?php

namespace App\Http\Controllers;

use App\Models\Staff;  
use App\Models\Log;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $jabatanList = ['Admin', 'Guru', 'Karyawan'];

        // Default tahun 2025
        $tahun = $request->input('tahun', 2025);

        // Default tanggal hari ini dalam format Y-m-d
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        // Filter jabatan dari request (nullable)
        $jabatan = $request->input('jabatan');

        // Query staff dengan filter jabatan dan tahun
        $staffQuery = Staff::query();

        if ($jabatan) {
            $staffQuery->where('jabatan', 'like', "%{$jabatan}%");
        }

        if ($tahun) {
            $staffQuery->where('tahun', $tahun);
        }

        $staff = $staffQuery->get();

        // Query log dengan filter tanggal (filter berdasarkan tanggal di kolom waktu)
        $logsQuery = Log::query();

        if ($tanggal) {
            // Asumsi kolom waktu menyimpan datetime, kita filter hanya tanggalnya
            $logsQuery->whereDate('waktu', $tanggal);
        }

        $logs = $logsQuery->get();

        return view('staff', compact('jabatanList', 'tahun', 'tanggal', 'jabatan', 'staff', 'logs'));
    }

    public function create()
    {
        return view('create_staff');
    }
}
