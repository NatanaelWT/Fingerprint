<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Staff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogKehadiranController extends Controller
{
    public function logKehadiran(Request $request)
    {
        $validated = $request->validate([
            'fingerprint_id' => 'required|integer|exists:fingerprint_templates,id',
        ]);

        $staff = Staff::where('id_template', $validated['fingerprint_id'])->first();
        if (!$staff) {
            return response()->json([
                'message' => 'Staff tidak ditemukan'
            ], 404);
        }

        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        $currentDate = $currentTime->toDateString();

        $isMasukTime = $currentHour >= 0 && $currentHour < 9;
        $isPulangTime = $currentHour >= 9 && $currentHour < 24;

        // Tentukan tipe absensi dan rentang waktunya
        if ($isMasukTime) {
            $start = '00:00:00';
            $end = '08:59:59';
            $type = 'Masuk';
        } else {
            $start = '09:00:00';
            $end = '23:59:59';
            $type = 'Pulang';
        }

        // Cek apakah sudah ada log dengan tipe yang sama di hari ini
        $existingLogSameType = LogKehadiran::where('fingerprint_id', $validated['fingerprint_id'])
            ->whereDate('check_in', $currentDate)
            ->whereTime('check_in', '>=', $start)
            ->whereTime('check_in', '<=', $end)
            ->exists();

        // Jika sudah absen
        if ($existingLogSameType) {
            return response()->json([
                'message' => "$type"
            ], 400);
        }

        // Simpan log
        $log = LogKehadiran::create([
            'fingerprint_id' => $validated['fingerprint_id'],
            'check_in' => $currentTime,
        ]);

        // ===== Identifikasi pemilik fingerprint =====
        $targetNumber = '6281217739010'; // Nomor statis untuk staff
        $name = $staff->nama;

        // ===== Kirim notifikasi =====
        if ($targetNumber && $name) {
            try {
                $status = $this->determineStatus($currentTime);
                $message = "{$name} telah melakukan absensi {$status} pada " . $currentTime->format('d-m-Y H:i:s');

                Http::withHeaders([
                    'Authorization' => '8w5a4Yv8RQ24dh7LJU8m' // API key Fonnte
                ])->post('https://api.fonnte.com/send', [
                    'target' => $targetNumber,
                    'message' => $message,
                ]);
                
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => "$type",
            'data' => $log
        ], 201);
    }

    /**
     * Tentukan status absensi berdasarkan waktu
     */
    private function determineStatus($time)
    {
        $hour = $time->hour;

        if ($hour >= 0 && $hour < 9) return 'Masuk';
        if ($hour >= 9 && $hour < 24) return 'Pulang';
        
        return 'Lainnya';
    }
}
