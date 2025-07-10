<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\LogKehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

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

        // Eager load logs untuk tanggal yang dipilih, diurutkan berdasarkan check_in terbaru
        $query->with(['logs' => function ($q) use ($tanggal) {
            $q->whereDate('check_in', $tanggal)
                ->orderBy('check_in', 'desc'); // <- Perbaikan di sini
        }]);

        $staff = $query->get();

        // Proses setiap staff untuk menambahkan atribut masuk dan pulang
        foreach ($staff as $s) {
            // Cari log masuk (06:00 - 09:59)
            $masukLog = $s->logs->filter(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '03:00:00' && $time <= '09:59:59';
            })->first();

            // Cari log pulang (15:00 - 17:59)
            $pulangLog = $s->logs->filter(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '15:00:00' && $time <= '23:59:59';
            })->first();

            $s->masuk = $masukLog ? $masukLog->check_in->format('H:i') : '-';
            $s->pulang = $pulangLog ? $pulangLog->check_in->format('H:i') : '-';
        }

        $logs = LogKehadiran::with('staff')
            ->whereDate('check_in', $tanggal)
            ->whereHas('staff')
            ->orderBy('check_in', 'desc')
            ->limit(5) // <- Tambahkan juga di sini jika kamu ingin menampilkan semua log kehadiran urut terbaru
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
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('staffs', 'id_template'), // Pastikan belum digunakan staff lain
                Rule::unique('siswas', 'id_template'), // Pastikan belum digunakan siswa
            ],
        ]);

        Staff::create($request->all());

        return redirect()->route('staff.index')->with('success', 'Staff created successfully.');
    }
    public function edit(Staff $staff)
    {
        return view('edit_staff', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'nip' => 'required|unique:staffs,nip,' . $staff->id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('staffs', 'id_template')->ignore($staff->id), // Ignore staff saat ini
                Rule::unique('siswas', 'id_template'), // Pastikan belum digunakan siswa
            ],
        ]);

        $staff->update($request->all());

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui.');
    }
}
