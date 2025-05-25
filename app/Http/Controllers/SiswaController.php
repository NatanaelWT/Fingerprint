<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();

        // Filter kelas jika ada
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        // Filter tahun, default ke tahun ini
        $tahun = $request->input('tahun', date('Y'));
        $query->where('tahun', $tahun);

        $siswa = $query->get();

        // Ambil daftar kelas unik untuk dropdown
        $kelasList = Siswa::select('kelas')->distinct()->pluck('kelas');

        return view('siswa', compact('siswa', 'kelasList', 'tahun'));
    }

    public function create()
    {
        return view('create_siswa');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|numeric',
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
