<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tiket;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataPemesanan;
use App\Models\Konser;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class DataPemesananController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validator = Validator::make($request->all(), [
                'tiketId' => 'required|exists:tikets,uuid',
                'totalPrice' => 'required|numeric|min:0',
                'tickets' => 'required|array|min:1',
                'tickets.*.nama_pemesan' => 'required|string',
                'tickets.*.email_pemesan' => 'required|email',
                'tickets.*.telepon_pemesan' => 'required',
                'tickets.*.alamat_pemesan' => 'required',
                'tickets.*.type' => 'required|in:regular,vip',
                'tickets.*.total_harga' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            // Get tiket
            $tiket = Tiket::where('uuid', $request->tiketId)->firstOrFail();
            
            // Generate order ID
            $orderId = 'ORDER-' . Str::random(10);

            // Create orders
            foreach ($request->tickets as $ticket) {
                DataPemesanan::create([
                    'tiket_id' => $tiket->id,
                    'user_id' => Auth::id(),
                    'order_id' => $orderId,
                    'nama_pemesan' => $ticket['nama_pemesan'],
                    'email_pemesan' => $ticket['email_pemesan'],
                    'telepon_pemesan' => $ticket['telepon_pemesan'],
                    'alamat_pemesan' => $ticket['alamat_pemesan'],
                    'tanggal_pemesan' => now(),
                    'jumlah_tiket' => 1,
                    'status_pembayaran' => 'Pending',
                    'total_harga' => $ticket['total_harga'],
                    'gate' => $ticket['gate'] ?? null
                ]);
            }

            // Generate Midtrans token
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $snapToken = \Midtrans\Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $request->totalPrice,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $orderId,
                    'snap_token' => $snapToken
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Booking error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show()
    {    
        $dataPemesanan = DataPemesanan::where('user_id', Auth::id())->get();
        return response()->json([
            'data' => $dataPemesanan
        ]);
    }

    public function index(Request $request)
    {
        try {
            $per = $request->per ?? 10;
            $page = $request->page ? $request->page - 1 : 0;

            DB::statement('set @no=0+' . $per * $page);
            
            $query = DataPemesanan::with(['tiket.konser']);


            // Filter by konser_id if provided and not 0
            if ($request->input('konser_id') && $request->input('konser_id') != '0') {
                $query->whereHas('tiket', function($q) use ($request) {
                    $q->where('konsers_id', $request->input('konser_id'));
                });
            }

            // Apply search filter if provided
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $search = $request->search;
                    $q->where('nama_pemesan', 'like', "%$search%")
                      ->orWhere('email_pemesan', 'like', "%$search%")
                      ->orWhere('telepon_pemesan', 'like', "%$search%");
                });
            }

            $data = $query->latest()
                         ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error in DataPemesananController@index:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error fetching data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllKonser(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Konser::all()
        ]);
    }

    public function getPurchasedTickets()
    {
        try {
            $tickets = DataPemesanan::with(['tiket.konser'])
                ->where('user_id', Auth::id())
                ->where('status_pembayaran', 'Successfully')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'uuid' => $ticket->uuid,
                        'nama_pemesan' => $ticket->nama_pemesan,
                        'gate_type' => $ticket->gate,
                        'total_harga' => $ticket->total_harga,
                        'status_pembayaran' => $ticket->status_pembayaran,
                        'is_used' => $ticket->is_used ?? false,
                        'tiket' => [
                            'opengate' => $ticket->tiket->opengate,
                            'konser' => [
                                'title' => $ticket->tiket->konser->title,
                                'tanggal' => $ticket->tiket->konser->tanggal,
                                'lokasi' => $ticket->tiket->konser->lokasi,
                                'image' => $ticket->tiket->konser->image,
                            ]
                        ]
                    ];
                });

            \Log::info('Purchased tickets:', ['tickets' => $tickets]);

            return response()->json($tickets);
        } catch (\Exception $e) {
            \Log::error('Error fetching purchased tickets: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handlePaymentCallback(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $orderId = $request->order_id;
            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status;

            \Log::info('Payment callback received:', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'fraud_status' => $fraudStatus
            ]);

            $pemesanan = DataPemesanan::where('order_id', $orderId)->get();
            
            if ($pemesanan->isEmpty()) {
                throw new \Exception('Order not found: ' . $orderId);
            }

            // Handle all types of failed/expired transactions
            $failedStatuses = ['expire', 'cancel', 'deny', 'failure'];
            $isExpired = in_array($transactionStatus, $failedStatuses);

            foreach ($pemesanan as $ticket) {
                if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                    $ticket->status_pembayaran = 'Successfully';
                    $this->updateTicketStock($ticket);
                } else if ($isExpired || $fraudStatus == 'deny') {
                    $ticket->status_pembayaran = 'Failed';
                } else if ($transactionStatus == 'pending' && !$isExpired) {
                    $ticket->status_pembayaran = 'Pending';
                }

                $ticket->save();
                \Log::info('Updated payment status:', [
                    'ticket_id' => $ticket->id,
                    'status' => $ticket->status_pembayaran,
                    'transaction_status' => $transactionStatus
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment status updated',
                'status' => $pemesanan->first()->status_pembayaran
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Add new method to handle expired payments
    public function handleExpiredPayment(Request $request)
    {
        try {
            DB::beginTransaction();

            $orderId = $request->order_id;
            $tickets = DataPemesanan::where('order_id', $orderId)
                                   ->where('status_pembayaran', 'Pending')
                                   ->get();

            foreach ($tickets as $ticket) {
                $ticket->status_pembayaran = 'Failed';
                $ticket->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment marked as failed due to expiration'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error handling expired payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function updateTicketStock($ticket)
    {
        if (!$ticket->tiket) return;

        if ($ticket->gate) {
            $gateField = 'gate_' . strtolower(substr($ticket->gate, -1)) . '_capacity';
            if (isset($ticket->tiket->$gateField)) {
                $ticket->tiket->$gateField = max(0, $ticket->tiket->$gateField - 1);
                $ticket->tiket->reguler = max(0, $ticket->tiket->reguler - 1);
            }
        } else {
            $ticket->tiket->vip = max(0, $ticket->tiket->vip - 1);
        }
        
        $ticket->tiket->save();
    }

    private function releaseReservedTickets($ticket)
    {
        // Optionally implement logic to release reserved tickets
        // This could be useful if you're reserving tickets during checkout
    }
}
