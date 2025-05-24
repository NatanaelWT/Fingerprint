<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FingerprintTemplate;
use Illuminate\Support\Facades\DB;

class FingerprintController extends Controller
{
    public function store(Request $request)
    {
        $hex = $request->input('template_hex');

        if (empty($hex)) {
            return response()->json(['message' => 'Data kosong'], 400);
        }

        // Cari ID terkecil yang belum dipakai dari 1-300
        $usedIds = FingerprintTemplate::pluck('id')->toArray();
        $missingId = null;
        for ($i = 1; $i <= 300; $i++) {
            if (!in_array($i, $usedIds)) {
                $missingId = $i;
                break;
            }
        }

        if ($missingId === null) {
            return response()->json(['message' => 'Semua ID telah terpakai hingga 300'], 400);
        }

        try {
            FingerprintTemplate::create([
                'id' => $missingId,
                'hex_data' => $hex
            ]);
            return response()->json(['message' => "Data berhasil disimpan dengan ID $missingId"], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }
}

