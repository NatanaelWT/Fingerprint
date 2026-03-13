<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
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

        $titleRow = 1;
        $metaRow = 2;
        $headerRow = 3;
        $dataStartRow = 4;
        $lastColumn = 'F';

        $reportDate = Carbon::parse($tanggal);
        $sheet->setCellValue("A{$titleRow}", 'Rekap Kehadiran Staff - ' . $reportDate->translatedFormat('d F Y'));
        $sheet->mergeCells("A{$titleRow}:{$lastColumn}{$titleRow}");
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($titleRow)->setRowHeight(26);

        $sheet->setCellValue("A{$metaRow}", 'Tanggal: ' . $reportDate->format('d M Y'));
        $sheet->mergeCells("A{$metaRow}:D{$metaRow}");
        $sheet->setCellValue("E{$metaRow}", 'Dicetak: ' . now()->format('d M Y H:i'));
        $sheet->mergeCells("E{$metaRow}:{$lastColumn}{$metaRow}");
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getFont()->setSize(10)->getColor()->setARGB('FF6B7280');
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($metaRow)->setRowHeight(20);

        $sheet->setCellValue("A{$headerRow}", 'Nama');
        $sheet->setCellValue("B{$headerRow}", 'Jabatan');
        $sheet->setCellValue("C{$headerRow}", 'Nomor Telepon');
        $sheet->setCellValue("D{$headerRow}", 'Jenis Kelamin');
        $sheet->setCellValue("E{$headerRow}", 'Masuk');
        $sheet->setCellValue("F{$headerRow}", 'Pulang');

        $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FF111827');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);

        $row = $dataStartRow;
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

        $lastDataRow = max($dataStartRow, $dataStartRow + $staff->count() - 1);
        $dataRange = "A{$headerRow}:{$lastColumn}{$lastDataRow}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FFE5E7EB');
        $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$dataStartRow}:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E{$dataStartRow}:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->freezePane("A{$dataStartRow}");
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

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

        $titleRow = 1;
        $metaRow = 2;
        $headerRow = 3;
        $dataStartRow = 4;
        $dayColumnStartIndex = 8; // Kolom H
        $daysInMonth = $start->diffInDays($end) + 1;
        $lastColumnIndex = 7 + $daysInMonth;
        $lastColumn = Coordinate::stringFromColumnIndex($lastColumnIndex);

        $title = 'Rekap Kehadiran Staff - ' . $start->translatedFormat('F Y');
        $sheet->setCellValue("A{$titleRow}", $title);
        $sheet->mergeCells("A{$titleRow}:{$lastColumn}{$titleRow}");
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($titleRow)->setRowHeight(26);

        $sheet->setCellValue("A{$metaRow}", 'Periode: ' . $start->format('d M Y') . ' - ' . $end->format('d M Y'));
        $sheet->mergeCells("A{$metaRow}:G{$metaRow}");
        $sheet->setCellValue("{$lastColumn}{$metaRow}", 'Dicetak: ' . now()->format('d M Y H:i'));
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getFont()->setSize(10)->getColor()->setARGB('FF6B7280');
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("{$lastColumn}{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($metaRow)->setRowHeight(20);

        // Header kolom
        $sheet->setCellValue("A{$headerRow}", 'Nama');
        $sheet->setCellValue("B{$headerRow}", 'Jabatan');
        $sheet->setCellValue("C{$headerRow}", 'Nomor Telepon');
        $sheet->setCellValue("D{$headerRow}", 'Jenis Kelamin');
        $sheet->setCellValue("E{$headerRow}", 'Hadir');
        $sheet->setCellValue("F{$headerRow}", 'Telat');
        $sheet->setCellValue("G{$headerRow}", 'Tidak Absen');

        // Header tanggal
        $colIndex = $dayColumnStartIndex;
        $current = $start->copy();
        while ($current <= $end) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($col . $headerRow, $current->format('d M'));
            $sheet->getStyle($col . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getColumnDimension($col)->setWidth(12);
            $current->addDay();
            $colIndex++;
        }

        // Isi data staf
        $row = $dataStartRow;
        foreach ($staffs as $staff) {
            $sheet->setCellValue('A' . $row, $staff->nama);
            $sheet->setCellValue('B' . $row, $staff->jabatan);
            $sheet->setCellValue('C' . $row, $staff->nomor_telepon);
            $sheet->setCellValue('D' . $row, $staff->jenis_kelamin);

            $hadir = 0;
            $telat = 0;
            $tidakAbsen = 0;

            $colIndex = $dayColumnStartIndex;
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

                $col = Coordinate::stringFromColumnIndex($colIndex);
                $cell = $col . $row;
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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

                $colIndex++;
                $currentDate->addDay();
            }

            // Isi jumlah hadir/telat/tidak absen
            $sheet->setCellValue('E' . $row, $hadir);
            $sheet->setCellValue('F' . $row, $telat);
            $sheet->setCellValue('G' . $row, $tidakAbsen);

            $row++;
        }

        // Format header (A3 sampai akhir tanggal): bold + center + background
        $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FF111827');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(12);

        $lastDataRow = max($dataStartRow, $dataStartRow + $staffs->count() - 1);
        $dataRange = "A{$headerRow}:{$lastColumn}{$lastDataRow}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FFE5E7EB');
        $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$dataStartRow}:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E{$dataStartRow}:{$lastColumn}{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->freezePane('H4');

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

    public function exportStaffMonth(Request $request, Staff $staff)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        try {
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        } catch (\Exception $e) {
            $start = now()->startOfMonth();
            $bulan = $start->format('Y-m');
        }

        $end = $start->copy()->endOfMonth();

        $logs = $staff->logs()
            ->whereBetween('check_in', [
                $start->copy()->startOfDay(),
                $end->copy()->endOfDay(),
            ])
            ->orderBy('check_in')
            ->get();

        $logsByDate = $logs->groupBy(function ($log) {
            return $log->check_in->toDateString();
        });

        $scheduleIn = '07:00';
        $scheduleOut = '15:30';
        $lateThreshold = '07:10';

        $formatDuration = function (?string $startTime, ?string $endTime) {
            if (!$startTime || !$endTime) {
                return null;
            }

            $startCarbon = Carbon::createFromFormat('H:i', $startTime);
            $endCarbon = Carbon::createFromFormat('H:i', $endTime);

            if ($endCarbon->lt($startCarbon)) {
                return null;
            }

            $minutes = $startCarbon->diffInMinutes($endCarbon);
            return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
        };

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $titleRow = 1;
        $metaRow = 2;
        $headerRow = 3;
        $dataStartRow = 4;
        $lastColumn = 'H';

        $title = 'Laporan Kehadiran Bulanan - ' . $staff->nama . ' - ' . $start->translatedFormat('F Y');
        $sheet->setCellValue("A{$titleRow}", $title);
        $sheet->mergeCells("A{$titleRow}:{$lastColumn}{$titleRow}");
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$titleRow}:{$lastColumn}{$titleRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($titleRow)->setRowHeight(26);

        $sheet->setCellValue("A{$metaRow}", 'Periode: ' . $start->format('d M Y') . ' - ' . $end->format('d M Y'));
        $sheet->mergeCells("A{$metaRow}:D{$metaRow}");
        $sheet->setCellValue("E{$metaRow}", 'Dicetak: ' . now()->format('d M Y H:i'));
        $sheet->mergeCells("E{$metaRow}:{$lastColumn}{$metaRow}");
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getFont()->setSize(10)->getColor()->setARGB('FF6B7280');
        $sheet->getStyle("A{$metaRow}:{$lastColumn}{$metaRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("E{$metaRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getRowDimension($metaRow)->setRowHeight(20);

        $sheet->setCellValue("A{$headerRow}", 'Tanggal');
        $sheet->setCellValue("B{$headerRow}", 'Jam Masuk');
        $sheet->setCellValue("C{$headerRow}", 'Scan Masuk');
        $sheet->setCellValue("D{$headerRow}", 'Datang Terlambat');
        $sheet->setCellValue("E{$headerRow}", 'Jam Keluar');
        $sheet->setCellValue("F{$headerRow}", 'Scan Keluar');
        $sheet->setCellValue("G{$headerRow}", 'Pulang Awal');
        $sheet->setCellValue("H{$headerRow}", 'Durasi Kerja');

        $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setARGB('FF111827');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');
        $sheet->getRowDimension($headerRow)->setRowHeight(22);

        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);

        $row = $dataStartRow;
        $currentDate = $start->copy();
        while ($currentDate->lte($end)) {
            $dateKey = $currentDate->toDateString();
            $dayLogs = $logsByDate->get($dateKey, collect());

            $masukLog = $dayLogs->first(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '00:00:00' && $time <= '08:59:59';
            });

            $pulangLog = $dayLogs->first(function ($log) {
                $time = $log->check_in->format('H:i:s');
                return $time >= '09:00:00' && $time <= '23:59:59';
            });

            $scanMasuk = $masukLog ? $masukLog->check_in->format('H:i') : null;
            $scanKeluar = $pulangLog ? $pulangLog->check_in->format('H:i') : null;

            $datangTerlambat = null;
            if ($scanMasuk && $scanMasuk >= $lateThreshold) {
                $datangTerlambat = $formatDuration($scheduleIn, $scanMasuk);
            }

            $pulangAwal = null;
            if ($scanKeluar && $scanKeluar < $scheduleOut) {
                $pulangAwal = $formatDuration($scanKeluar, $scheduleOut);
            }

            $durasiKerja = $formatDuration($scanMasuk, $scanKeluar);

            $sheet->setCellValue("A{$row}", $currentDate->translatedFormat('l, d-m-Y'));
            $sheet->setCellValue("B{$row}", $scheduleIn);
            $sheet->setCellValue("C{$row}", $scanMasuk ?? '-');
            $sheet->setCellValue("D{$row}", $datangTerlambat ?? '');
            $sheet->setCellValue("E{$row}", $scheduleOut);
            $sheet->setCellValue("F{$row}", $scanKeluar ?? '-');
            $sheet->setCellValue("G{$row}", $pulangAwal ?? '');
            $sheet->setCellValue("H{$row}", $durasiKerja ?? '');

            $row++;
            $currentDate->addDay();
        }

        $lastDataRow = max($dataStartRow, $row - 1);
        $dataRange = "A{$headerRow}:{$lastColumn}{$lastDataRow}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('FFE5E7EB');
        $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$dataStartRow}:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("B{$dataStartRow}:{$lastColumn}{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->freezePane("A{$dataStartRow}");
        $sheet->getDefaultRowDimension()->setRowHeight(-1);

        $fileName = 'laporan_staff_' . $staff->id . '_' . $start->format('F_Y') . '.xlsx';

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
