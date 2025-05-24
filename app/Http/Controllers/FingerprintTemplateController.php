<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class FingerprintTemplateController extends Controller
{
    public function getHexData($id): JsonResponse
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json(['error' => 'Invalid or missing ID parameter'], 400);
        }

        $record = DB::table('fingerprint_templates')->where('id', $id)->first();

        if (!$record) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $hexString = preg_replace('/[^0-9A-Fa-f]/', '', $record->hex_data);
        $bytes = str_split($hexString, 2);
        $totalBytes = count($bytes);

        // Bagi jadi 6 bagian, ambil 4 saja
        $fullParts = 6;
        $useParts = 4;
        $packetSize = ceil($totalBytes / $fullParts);

        $packets = [];
        for ($i = 0; $i < $useParts; $i++) {
            $packet = array_slice($bytes, $i * $packetSize, $packetSize);
            $packets[] = array_map(function ($b) {
                return '0x' . strtoupper($b);
            }, $packet);
        }

        return response()->json([
            'id' => (int) $id,
            'packets' => $packets,
        ]);
    }
}
