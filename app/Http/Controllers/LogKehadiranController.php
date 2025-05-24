<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LogKehadiran;

class LogKehadiranController extends Controller
{
    public function logKehadiran(Request $request)
    {
        $validated = $request->validate([
            'fingerprint_id' => 'required|integer|exists:fingerprint_templates,id',
        ]);

        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        $currentDate = $currentTime->toDateString();

        // Cek apakah waktu saat ini dalam rentang absensi yang diizinkan
        $isMasukTime = $currentHour >= 6 && $currentHour < 10;  // 06:00 - 09:59
        $isPulangTime = $currentHour >= 15 && $currentHour < 18; // 15:00 - 17:59

        if (!$isMasukTime && !$isPulangTime) {
            return response()->json(['message' => 'Bukan waktu absensi yang valid'], 400);
        }

        // Tentukan tipe absensi dan batas waktu
        if ($isMasukTime) {
            $start = '06:00:00';
            $end = '09:59:59';
            $type = 'masuk';
        } else {
            $start = '15:00:00';
            $end = '17:59:59';
            $type = 'pulang';
        }

        // Cek apakah sudah ada absensi dengan tipe yang sama hari ini
        $existingLog = LogKehadiran::where('fingerprint_id', $validated['fingerprint_id'])
            ->whereDate('check_in', $currentDate)
            ->whereTime('check_in', '>=', $start)
            ->whereTime('check_in', '<=', $end)
            ->exists();

        if ($existingLog) {
            return response()->json([
                'message' => "Anda sudah absensi $type hari ini"
            ], 400);
        }

        // Simpan log kehadiran
        $log = LogKehadiran::create([
            'fingerprint_id' => $validated['fingerprint_id'],
            'check_in' => $currentTime,
        ]);

        return response()->json([
            'message' => "Absensi $type berhasil dicatat",
            'data' => $log
        ], 201);
    }
}