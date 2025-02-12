<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataPemesanan;

class TicketVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $ticket = DataPemesanan::where('uuid', $request->qr_code)
            ->with(['tiket.konser'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'isValid' => false,
                'message' => 'Tiket tidak ditemukan'
            ]);
        }

        if ($ticket->is_used) {
            return response()->json([
                'isValid' => false,
                'message' => 'Tiket sudah digunakan'
            ]);
        }

        // Mark ticket as used
        $ticket->is_used = true;
        $ticket->used_at = now();
        $ticket->save();

        return response()->json([
            'isValid' => true,
            'name' => $ticket->nama_pemesan,
            'concert' => $ticket->tiket->konser->title,
            'gate' => $ticket->gate_type,
            'date' => $ticket->tiket->konser->tanggal,
            'message' => 'Tiket valid'
        ]);
    }
}
