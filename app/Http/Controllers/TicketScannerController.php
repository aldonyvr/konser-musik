<?php

namespace App\Http\Controllers;

use App\Models\DataPemesanan;
use App\Models\TiketScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketScannerController extends Controller
{
    public function scan(Request $request)
    {
        try {
            $request->validate([
                'ticket_uuid' => 'required|string'
            ]);

            $ticket = DataPemesanan::where('uuid', $request->ticket_uuid)
                ->with(['tiket.konsers'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan'
                ], 404);
            }

            if ($ticket->is_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah digunakan pada ' . $ticket->used_at
                ], 400);
            }

            if ($ticket->status_pembayaran !== 'Successfully') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tiket belum selesai'
                ], 400);
            }

            // Update ticket status
            $ticket->update([
                'is_used' => true,
                'used_at' => now(),
                'scanned_by' => Auth::id()
            ]);

            // Create scan history
            TiketScanner::create([
                'ticket_id' => $ticket->id,
                'scanner_id' => Auth::id(),
                'scanned_at' => now(),
                'status' => 'success'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tiket valid',
                'data' => [
                    'ticket_holder' => $ticket->nama_pemesan,
                    'event_name' => $ticket->tiket->konsers->title,
                    'ticket_type' => $ticket->gate_type ?? 'VIP',
                    'event_date' => $ticket->tiket->konsers->tanggal
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scanning ticket: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getScanHistory()
    {
        try {
            $history = TiketScanner::with(['ticket.tiket.konsers'])
                ->where('scanner_id', Auth::id())
                ->orderBy('scanned_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching scan history: ' . $e->getMessage()
            ], 500);
        }
    }
}
