<?php

namespace App\Http\Controllers;

use App\Models\DataPemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScannerController extends Controller
{
    public function scanTicket(Request $request)
    {
        try {
            $request->validate([
                'ticket_uuid' => 'required|string'
            ]);

            $ticket = DataPemesanan::where('uuid', $request->ticket_uuid)
                ->with(['tiket.konser'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid',
                    'type' => 'invalid'
                ], 404);
            }

            // Check if ticket has been scanned
            if ($ticket->is_scanned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah digunakan!',
                    'type' => 'already_scanned',
                    'scan_info' => [
                        'scanned_at' => $ticket->scanned_at->format('d M Y H:i:s'),
                        'ticket_holder' => $ticket->nama_pemesan,
                        'event_name' => $ticket->tiket->konser->title
                    ]
                ], 400);
            }

            if ($ticket->status_pembayaran !== 'Successfully') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran tiket belum selesai'
                ], 400);
            }

            // Update ticket to mark as scanned
            $ticket->update([
                'is_scanned' => true,
                'scanned_at' => now(),
                'scan_status' => 'success',
                'scanned_by' => Auth::id()
            ]);

            // Return success with ticket details
            return response()->json([
                'success' => true,
                'message' => 'Tiket valid - Silakan masuk',
                'scan_time' => now()->format('d M Y H:i:s'),
                'data' => [
                    'ticket_holder' => $ticket->nama_pemesan,
                    'event_name' => $ticket->tiket->konser->title,
                    'ticket_type' => $ticket->gate_type,
                    'event_date' => $ticket->tiket->konser->tanggal,
                    'gate' => $ticket->gate_type
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error scanning ticket: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
