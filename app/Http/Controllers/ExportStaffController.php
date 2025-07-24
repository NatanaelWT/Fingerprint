<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportStaffController extends Controller
{
    public function export(Request $request)
    {
        $query = Staff::query();
        $tanggal = $request->filled('tanggal') ? $request->tanggal : now()->toDateString();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $query->with(['logs' => function ($q) use ($tanggal) {
            $q->whereDate('check_in', $tanggal);
        }]);

        $staff = $query->get();

        foreach ($staff as $s) {
            $masukLog = $s->logs->filter(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '00:00:00' && $time <= '08:59:59';
            })->first();

            $pulangLog = $s->logs->filter(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '09:00:00' && $time <= '23:59:59';
            })->first();

            $s->masuk = $masukLog ? $masukLog->check_in->format('H:i') : '-';
            $s->pulang = $pulangLog ? $pulangLog->check_in->format('H:i') : '-';
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Nama', 'Jabatan', 'Nomor Telepon', 'Jenis Kelamin', 'Masuk', 'Pulang']
        ], null, 'A1');

        $headerRange = "A1:ZZ1";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row = 2;
        foreach ($staff as $s) {
            $sheet->setCellValue("A$row", $s->nama);
            $sheet->setCellValue("B$row", $s->jabatan);
            $sheet->setCellValue("C$row", $s->nomor_telepon);
            $sheet->setCellValue("D$row", $s->jenis_kelamin);
            $sheet->setCellValue("E$row", $s->masuk);
            $sheet->setCellValue("F$row", $s->pulang);

            // Warna Masuk
            $masukCell = "E$row";
            if ($s->masuk === '-') {
                $color = 'FFFF0000'; // Merah
            } elseif ($s->masuk >= '07:10') {
                $color = 'FFFFA500'; // Oranye
            } else {
                $color = 'FF00FF00'; // Hijau
            }

            $sheet->getStyle($masukCell)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB($color);

            $row++;
        }

        // ✅ Auto-size semua kolom
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'staff_export_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportMonth(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $search = $request->input('search');

        $start = Carbon::parse($bulan)->startOfMonth();
        $end = Carbon::parse($bulan)->endOfMonth();

        $query = Staff::query();
        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $query->with(['logs' => function ($q) use ($start, $end) {
            $q->whereBetween('check_in', [$start, $end]);
        }]);

        $staffs = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Jabatan');
        $sheet->setCellValue('C1', 'Nomor Telepon');
        $sheet->setCellValue('D1', 'Jenis Kelamin');
        $sheet->setCellValue('E1', 'Hadir');
        $sheet->setCellValue('F1', 'Telat');
        $sheet->setCellValue('G1', 'Tidak Absen');

        // Header tanggal
        $col = 'H';
        $current = $start->copy();
        while ($current <= $end) {
            $sheet->setCellValue($col . '1', $current->format('d M'));
            $sheet->getStyle($col . '1')->getAlignment()->setHorizontal('center');
            $sheet->getColumnDimension($col)->setWidth(15);
            $current->addDay();
            $col++;
        }

        // Isi data staf
        $row = 2;
        foreach ($staffs as $staff) {
            $sheet->setCellValue('A' . $row, $staff->nama);
            $sheet->setCellValue('B' . $row, $staff->jabatan);
            $sheet->setCellValue('C' . $row, $staff->nomor_telepon);
            $sheet->setCellValue('D' . $row, $staff->jenis_kelamin);

            $hadir = 0;
            $telat = 0;
            $tidakAbsen = 0;

            $col = 'H';
            $currentDate = $start->copy();
            while ($currentDate <= $end) {
                $logs = $staff->logs->filter(function ($log) use ($currentDate) {
                    return $log->check_in->isSameDay($currentDate);
                });

                $masuk = $logs->filter(function ($log) {
                    $time = $log->check_in->format('H:i');
                    return $time >= '00:00' && $time <= '08:59';
                })->first();

                $pulang = $logs->filter(function ($log) {
                    $time = $log->check_in->format('H:i');
                    return $time >= '09:00' && $time <= '23:59';
                })->first();

                $masukTime = $masuk ? $masuk->check_in->format('H:i') : '-';
                $pulangTime = $pulang ? $pulang->check_in->format('H:i') : '-';
                $value = "$masukTime / $pulangTime";

                $cell = $col . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
                $sheet->getStyle($cell)->getAlignment()->setWrapText(true);

                if ($masukTime === '-') {
                    $tidakAbsen++;
                    $color = 'FFFF0000'; // Merah
                } elseif ($masukTime >= '07:10') {
                    $telat++;
                    $color = 'FFFFA500'; // Oranye
                } else {
                    $hadir++;
                    $color = 'FF00FF00'; // Hijau
                }

                $sheet->getStyle($cell)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($color);

                $col++;
                $currentDate->addDay();
            }

            // Isi jumlah hadir/telat/tidak absen
            $sheet->setCellValue('E' . $row, $hadir);
            $sheet->setCellValue('F' . $row, $telat);
            $sheet->setCellValue('G' . $row, $tidakAbsen);

            $row++;
        }

        // Format header (A1 sampai akhir tanggal): bold + center
        $headerRange = "A1:ZZ1";
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // WrapText + Horizontal Center untuk semua kolom
        foreach (range('A', 'ZZ') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Auto height baris
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $fileName = 'rekap_staff_' . $start->format('F_Y') . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
