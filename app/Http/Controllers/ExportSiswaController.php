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

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $query->get();

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
                    $s->masuk,
                    $s->pulang,
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

