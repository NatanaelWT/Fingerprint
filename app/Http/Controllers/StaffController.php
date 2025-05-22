<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $jabatanList = ['Admin', 'Guru', 'Karyawan'];
        $tahun = 2025;

        // Ambil semua data staff dari tabel siswa (atau sesuaikan nama model)
        $staff = Siswa::all(); // atau bisa pakai filter, contoh: Siswa::where('jabatan', '!=', null)->get();

        return view('staff', compact('jabatanList', 'tahun', 'staff'));
    }
}
