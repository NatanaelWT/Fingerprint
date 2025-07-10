<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();

        // Tahun default: tahun sekarang
        $tahun = $request->filled('tahun') ? $request->tahun : now()->year;

        // Tanggal default: hari ini
        $tanggal = $request->filled('tanggal') ? $request->tanggal : now()->toDateString();

        // Filter kelas jika ada
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter tahun
        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        // Filter pencarian nama atau NIS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Eager load logs kehadiran untuk tanggal yang dipilih
        $query->with(['logs' => function ($q) use ($tanggal) {
            $q->whereDate('check_in', $tanggal);
        }]);

        // Ambil hasil query dengan paginasi
        $siswa = $query->paginate(500)->withQueryString();

        // Tambahkan atribut masuk dan pulang untuk setiap siswa
        foreach ($siswa->items() as $s) {
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

            // Tambahkan atribut virtual
            $s->masuk = $masukLog ? $masukLog->check_in->format('H:i') : '-';
            $s->pulang = $pulangLog ? $pulangLog->check_in->format('H:i') : '-';
        }

        // Daftar kelas unik
        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');

        return view('siswa', compact('siswa', 'kelasList', 'tahun', 'tanggal'));
    }
    public function create()
    {
        return view('create_siswa');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|unique:siswas,nis',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:20',
            'tahun' => 'required|numeric',
            'alamat' => 'required|string|max:255',
            'nomor_ortu' => 'required|string|max:13',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('siswas', 'id_template'), // Pastikan belum digunakan siswa lain
                Rule::unique('staffs', 'id_template'), // Pastikan belum digunakan staff
            ],
        ]);

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }
    public function edit(Siswa $siswa)
    {
        return view('edit_siswa', compact('siswa'));
    }

     public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric|unique:siswas,nis,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:20',
            'tahun' => 'required|numeric',
            'alamat' => 'required|string|max:255',
            'nomor_ortu' => 'required|string|max:13',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('siswas', 'id_template')->ignore($siswa->id), // Ignore siswa saat ini
                Rule::unique('staffs', 'id_template'), // Pastikan belum digunakan staff
            ],
        ]);

        $siswa->update($validated);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }
}
