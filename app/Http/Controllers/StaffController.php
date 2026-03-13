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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
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

        $logs = LogKehadiran::with('staff')
            ->whereDate('check_in', $tanggal)
            ->whereHas('staff')
            ->orderBy('check_in', 'desc')
            ->limit(5) // <- Tambahkan juga di sini jika kamu ingin menampilkan semua log kehadiran urut terbaru
            ->get();

        return view('staff', compact('staff', 'tanggal', 'logs'));
    }

    public function show(Request $request, Staff $staff)
    {
        $month = $request->input('bulan', now()->format('Y-m'));
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->format('Y-m');
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        $monthEnd = $monthStart->copy()->endOfMonth();

        $logsForMonth = $staff->logs()
            ->whereBetween('check_in', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->orderBy('check_in')
            ->get();

        $attendanceByDate = [];
        $logsForMonth
            ->groupBy(function ($log) {
                return $log->check_in->toDateString();
            })
            ->each(function ($dayLogs, $date) use (&$attendanceByDate) {
                $masukLog = $dayLogs->first(function ($log) {
                    $time = $log->check_in->format('H:i:s');
                    return $time >= '00:00:00' && $time <= '08:59:59';
                });

                $pulangLog = $dayLogs->first(function ($log) {
                    $time = $log->check_in->format('H:i:s');
                    return $time >= '09:00:00' && $time <= '23:59:59';
                });

                $masukTime = $masukLog ? $masukLog->check_in->format('H:i') : null;
                $pulangTime = $pulangLog ? $pulangLog->check_in->format('H:i') : null;

                $status = 'Tidak Absen';
                if ($masukTime) {
                    $status = $masukTime >= '07:10' ? 'Telat' : 'Hadir';
                }

                $attendanceByDate[$date] = [
                    'masuk' => $masukTime,
                    'pulang' => $pulangTime,
                    'status' => $status,
                ];
            });

        $weeks = [];
        $week = [];
        $startOffset = $monthStart->dayOfWeekIso;
        for ($i = 1; $i < $startOffset; $i++) {
            $week[] = null;
        }

        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $week[] = $cursor->copy();
            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
            $cursor->addDay();
        }

        if (count($week) > 0) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        $summary = [
            'hadir' => 0,
            'telat' => 0,
            'tidak_absen' => 0,
        ];

        $summaryEnd = $monthEnd->copy();
        $today = Carbon::today();
        if ($summaryEnd->gt($today)) {
            $summaryEnd = $today->copy();
        }

        if ($summaryEnd->gte($monthStart)) {
            $countCursor = $monthStart->copy();
            while ($countCursor->lte($summaryEnd)) {
                $dateKey = $countCursor->toDateString();
                $entry = $attendanceByDate[$dateKey] ?? null;

                if (!$entry || $entry['status'] === 'Tidak Absen') {
                    $summary['tidak_absen']++;
                } elseif ($entry['status'] === 'Telat') {
                    $summary['telat']++;
                } else {
                    $summary['hadir']++;
                }

                $countCursor->addDay();
            }
        }

        $logs = $staff->logs()
            ->orderBy('check_in', 'desc')
            ->paginate(20);

        $monthLabel = $monthStart->translatedFormat('F Y');

        return view('show_staff', compact(
            'staff',
            'logs',
            'month',
            'monthLabel',
            'weeks',
            'attendanceByDate',
            'summary'
        ));
    }

    public function calendar(Request $request)
    {
        $month = $request->input('bulan', now()->format('Y-m'));
        try {
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            $month = now()->format('Y-m');
            $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        $monthEnd = $monthStart->copy()->endOfMonth();
        $totalStaff = Staff::whereNotNull('id_template')->count();

        $logs = LogKehadiran::with('staff')
            ->whereBetween('check_in', [
                $monthStart->copy()->startOfDay(),
                $monthEnd->copy()->endOfDay(),
            ])
            ->whereHas('staff', function ($q) {
                $q->whereNotNull('id_template');
            })
            ->orderBy('check_in')
            ->get();

        $statsByDate = [];
        $logs->groupBy(function ($log) {
            return $log->check_in->toDateString();
        })->each(function ($dayLogs, $date) use (&$statsByDate, $totalStaff) {
            $perStaffMasuk = [];

            foreach ($dayLogs as $log) {
                if (!$log->staff) {
                    continue;
                }

                $staffId = $log->staff->id;
                $time = $log->check_in->format('H:i:s');
                if ($time >= '00:00:00' && $time <= '08:59:59') {
                    $masuk = $log->check_in->format('H:i');
                    if (!isset($perStaffMasuk[$staffId]) || $masuk < $perStaffMasuk[$staffId]) {
                        $perStaffMasuk[$staffId] = $masuk;
                    }
                }
            }

            $hadir = 0;
            $telat = 0;
            foreach ($perStaffMasuk as $masukTime) {
                if ($masukTime >= '07:10') {
                    $telat++;
                } else {
                    $hadir++;
                }
            }

            $tidakHadir = max($totalStaff - ($hadir + $telat), 0);

            $statsByDate[$date] = [
                'hadir' => $hadir,
                'telat' => $telat,
                'tidak_hadir' => $tidakHadir,
            ];
        });

        $weeks = [];
        $week = [];
        $startOffset = $monthStart->dayOfWeekIso;
        for ($i = 1; $i < $startOffset; $i++) {
            $week[] = null;
        }

        $cursor = $monthStart->copy();
        while ($cursor->lte($monthEnd)) {
            $week[] = $cursor->copy();
            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
            $cursor->addDay();
        }

        if (count($week) > 0) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $weeks[] = $week;
        }

        $monthLabel = $monthStart->translatedFormat('F Y');

        return view('staff_calendar', compact(
            'month',
            'monthLabel',
            'weeks',
            'statsByDate',
            'totalStaff'
        ));
    }

    public function create()
    {
        return view('create_staff');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('staffs', 'id_template'), // Pastikan belum digunakan staff lain
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
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'id_template' => [
                'required',
                'numeric',
                'exists:fingerprint_templates,id', // Pastikan template ada di database
                Rule::unique('staffs', 'id_template')->ignore($staff->id), // Ignore staff saat ini
            ],
        ]);

        $staff->update($request->all());

        return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui.');
    }
}
