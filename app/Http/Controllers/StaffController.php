<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\LogKehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();

        // Tanggal default: hari ini
        $tanggal = $request->filled('tanggal') ? $request->tanggal : now()->toDateString();

        // Filter pencarian: nama atau NIP
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Eager load logs untuk tanggal yang dipilih
        $query->with(['logs' => function($q) use ($tanggal) {
            $q->whereDate('check_in', $tanggal);
        }]);

        $staff = $query->get();

        // Proses setiap staff untuk menambahkan atribut masuk dan pulang
        foreach ($staff as $s) {
            // Cari log masuk (06:00 - 09:59)
            $masukLog = $s->logs->filter(function($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '03:00:00' && $time <= '09:59:59';
            })->first();

            // Cari log pulang (15:00 - 17:59)
            $pulangLog = $s->logs->filter(function($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '15:00:00' && $time <= '23:59:59';
            })->first();

            $s->masuk = $masukLog ? $masukLog->check_in->format('H:i') : '-';
            $s->pulang = $pulangLog ? $pulangLog->check_in->format('H:i') : '-';
        }
        $logs = LogKehadiran::with('staff')
            ->whereDate('check_in', $tanggal)
            ->get();

        return view('staff', compact('staff', 'tanggal', 'logs'));
    }

    public function create()
    {
        return view('create_staff');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:staffs,nip',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'id_template' => 'required|string|max:255',
        ]);

        Staff::create($request->all());

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }
}
