<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class FingerprintTemplateController extends Controller
{
    public function getAllHexData(): JsonResponse
    {
        // Ambil semua data dari tabel
        $records = DB::table('fingerprint_templates')->select('id', 'hex_data')->get();

        $output = [];

        foreach ($records as $record) {
            $allPackets = $this->splitHexTo6Packets($record->hex_data);
            $packetsForApi = array_slice($allPackets, 0, 4);

            $output[] = [
                'id' => (int) $record->id,
                'packets' => $packetsForApi
            ];
        }

        return response()->json($output);
    }

    private function splitHexTo6Packets($hexString)
    {
        $hexString = preg_replace('/[^0-9A-Fa-f]/', '', $hexString);
        $length = strlen($hexString);
        $packetLength = ceil($length / 6 / 2) * 2; // Rata-rata per 6 bagian, pastikan genap
        $packets = [];

        for ($i = 0; $i < 6; $i++) {
            $start = $i * $packetLength;
            $packet = substr($hexString, $start, $packetLength);
            $bytes = str_split($packet, 2);
            $bytes = array_map(function($byte) {
                return '0x' . strtoupper($byte);
            }, $bytes);
            $packets[] = $bytes;
        }

        return $packets;
    }
}
