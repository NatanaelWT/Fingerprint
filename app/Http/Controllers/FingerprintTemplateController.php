<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class FingerprintTemplateController extends Controller
{
    public function getAllHexData(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = DB::table('fingerprint_templates')
            ->select('id', 'hex_data');

        // Hitung total data untuk paginasi
        $total = $query->count();
        $totalPages = ceil($total / $perPage);

        // Ambil data paginasi
        $records = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

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
        $packetLength = ceil($length / 6 / 2) * 2;
        $packets = [];

        for ($i = 0; $i < 6; $i++) {
            $start = $i * $packetLength;
            $packet = substr($hexString, $start, $packetLength);
            $bytes = str_split($packet, 2);
            $bytes = array_map(function ($byte) {
                return '0x' . strtoupper($byte);
            }, $bytes);
            $packets[] = $bytes;
        }

        return $packets;
    }
}
