<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        // Filter tanggal (created_at atau bisa disesuaikan)
        if ($tanggal) {
            $query->whereDate('created_at', $tanggal);
        }

        // Filter pencarian nama atau NIS
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Ambil hasil query
        $siswa = $query->paginate(10)->withQueryString();

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
            'nis' => 'required|numeric|unique:siswa,nis',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:20',
            'tahun' => 'required|numeric',
            'alamat' => 'required|string|max:255',
            'nomor_ortu' => 'required|string|max:13',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'id_template' => 'required|numeric',
        ]);

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }
}
