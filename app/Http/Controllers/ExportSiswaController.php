<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportSiswaController extends Controller
{
    public function export(Request $request)
{
    $query = Siswa::query();

    // Terapkan filter
    if ($request->filled('kelas')) {
        $query->where('kelas', $request->kelas);
    }

    if ($request->filled('tahun')) {
        $query->where('tahun', $request->tahun);
    }

    // Gunakan tanggal default jika tidak diisi
    $tanggal = $request->filled('tanggal') 
        ? $request->tanggal 
        : now()->toDateString();

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('nama', 'like', '%' . $request->search . '%')
              ->orWhere('nis', 'like', '%' . $request->search . '%');
        });
    }

    // Eager loading logs dengan filter tanggal
    $query->with(['logs' => function($q) use ($tanggal) {
        $q->whereDate('check_in', $tanggal);
    }]);

    $siswa = $query->get();

    // Tambahkan atribut masuk/pulang
    $siswa->each(function ($s) {
        // Cari log masuk (03:00 - 09:59)
        $masukLog = $s->logs->filter(function($log) {
            $time = $log->check_in->format('H:i:s');
            return $time >= '03:00:00' && $time <= '09:59:59';
        })->first();

        // Cari log pulang (15:00 - 23:59)
        $pulangLog = $s->logs->filter(function($log) {
            $time = $log->check_in->format('H:i:s');
            return $time >= '15:00:00' && $time <= '23:59:59';
        })->first();

        $s->masuk = $masukLog ? $masukLog->check_in->format('H:i') : '-';
        $s->pulang = $pulangLog ? $pulangLog->check_in->format('H:i') : '-';
    });

    // Buat file Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header kolom
    $sheet->fromArray([
        ['NIS', 'Nama', 'Kelas', 'Alamat', 'Nomor Ortu', 'Jenis Kelamin', 'Tahun', 'ID Template', 'Masuk', 'Pulang']
    ], null, 'A1');

    // Data siswa
    $sheet->fromArray(
        $siswa->map(function ($s) {
            return [
                $s->nis,
                $s->nama,
                $s->kelas,
                $s->alamat,
                $s->nomor_ortu,
                $s->jenis_kelamin,
                $s->tahun,
                $s->id_template,
                $s->masuk, // Gunakan nilai yang dihitung
                $s->pulang // Gunakan nilai yang dihitung
            ];
        })->toArray(),
        null,
        'A2'
    );

    // Stream file Excel ke browser
    $writer = new Xlsx($spreadsheet);
    $fileName = 'siswa_export_' . now()->format('Ymd_His') . '.xlsx';

    return new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
        'Cache-Control' => 'max-age=0',
    ]);
}
}

