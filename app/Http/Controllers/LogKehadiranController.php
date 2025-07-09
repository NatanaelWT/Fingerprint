<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LogKehadiran;
use App\Models\Siswa;
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

        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;
        $currentDate = $currentTime->toDateString();

        $isMasukTime = $currentHour >= 3 && $currentHour < 9;
        $isPulangTime = $currentHour >= 15 && $currentHour < 23;

        if (!$isMasukTime && !$isPulangTime) {
            return response()->json(['message' => 'Diluar Waktu Absensi'], 400);
        }

        // Tentukan tipe absensi dan rentang waktunya
        if ($isMasukTime) {
            $start = '03:00:00';
            $end = '09:59:59';
            $type = 'masuk';
        } else {
            $start = '15:00:00';
            $end = '23:59:59';
            $type = 'pulang';
        }

        // Cek apakah sudah ada log dengan tipe yang sama di hari ini
        $existingLogSameType = LogKehadiran::where('fingerprint_id', $validated['fingerprint_id'])
            ->whereDate('check_in', $currentDate)
            ->whereTime('check_in', '>=', $start)
            ->whereTime('check_in', '<=', $end)
            ->exists();

        if ($existingLogSameType) {
            return response()->json([
                'message' => "Sudah $type hari ini"
            ], 400);
        }

        // Cek khusus untuk absensi pulang: harus sudah ada absensi masuk di hari yang sama
        if ($type === 'pulang') {
            $masukToday = LogKehadiran::where('fingerprint_id', $validated['fingerprint_id'])
                ->whereDate('check_in', $currentDate)
                ->whereTime('check_in', '>=', '03:00:00')
                ->whereTime('check_in', '<=', '09:59:59')
                ->exists();

            if (!$masukToday) {
                return response()->json([
                    'message' => 'Tidak hadir hari ini'
                ], 400);
            }
        }

        // Simpan log
        $log = LogKehadiran::create([
            'fingerprint_id' => $validated['fingerprint_id'],
            'check_in' => $currentTime,
        ]);

        // ===== Identifikasi pemilik fingerprint =====
        $targetNumber = null;
        $name = null;

        $siswa = Siswa::where('id_template', $validated['fingerprint_id'])->first();
        if ($siswa) {
            $targetNumber = $this->formatPhoneNumber($siswa->nomor_ortu);
            $name = $siswa->nama;
        } else {
            $staff = Staff::where('id_template', $validated['fingerprint_id'])->first();
            if ($staff) {
                $targetNumber = '6282158114721'; // Nomor statis untuk staff
                $name = $staff->nama;
            }
        }

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
            'message' => "Absensi $type",
            'data' => $log
        ], 201);
    }

    /**
     * Format nomor telepon ke format internasional (62)
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        }
        
        if (substr($phone, 0, 2) !== '62') {
            return '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Tentukan status absensi berdasarkan waktu
     */
    private function determineStatus($time)
    {
        $hour = $time->hour;

        if ($hour >= 6 && $hour < 10) return 'Masuk';
        if ($hour >= 15 && $hour < 18) return 'Pulang';
        
        return 'Lainnya';
    }
}