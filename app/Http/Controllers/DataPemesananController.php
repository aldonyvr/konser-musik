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

class DataPemesananController extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'tiketId' => 'required|exists:tikets,uuid',
                'reguler' => 'integer|min:0|required_without_all:vip',
                'vip' => 'integer|min:0|required_without_all:reguler',
                'totalPrice' => 'required|numeric|min:0',
                'tickets' => 'required|array|min:1',
                'tickets.*.nama_pemesan' => 'required|string',
                'tickets.*.email_pemesan' => 'required|email',
                'tickets.*.telepon_pemesan' => 'required|string',
                'tickets.*.alamat_pemesan' => 'required|string',
                'tickets.*.type' => 'required|in:regular,vip',
            ]);

            $tiket = Tiket::where('uuid', $request->tiketId)->firstOrFail();

            if ($request->reguler > $tiket->reguler || $request->vip > $tiket->vip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough tickets available'
                ], 400);
            }

            DB::beginTransaction();

            try {
                $tiket->reguler -= $request->reguler;
                $tiket->vip -= $request->vip;

                // Update gate capacity
                foreach ($request->tickets as $ticketData) {
                    if ($ticketData['type'] === 'regular') {
                        $gateField = $ticketData['gate'] . '_capacity';
                        $tiket->$gateField -= 1;
                    }
                }
                $tiket->save();

                foreach ($request->tickets as $ticketData) {
                    DataPemesanan::create([
                        'tiket_id' => $tiket->id,
                        'user_id' => Auth::id(),
                        'nama_pemesan' => $ticketData['nama_pemesan'],
                        'email_pemesan' => $ticketData['email_pemesan'],
                        'telepon_pemesan' => $ticketData['telepon_pemesan'],
                        'alamat_pemesan' => $ticketData['alamat_pemesan'],
                        'tanggal_pemesan' => Carbon::now()->format('Y-m-d'),
                        'jumlah_tiket' => 1,
                        'status_pembayaran' => 'Successfully',
                        'total_harga' => $ticketData['type'] === 'vip' ? $tiket->harga_vip : $tiket->harga_regular,
                        'gate_type' => $ticketData['type'] === 'regular' ? $ticketData['gate'] : null,
                    ]);
                }

                $orderId = 'ORDER-' . Str::random(10);  
                $snapToken = $this->generateSnapToken([
                    'order_id' => $orderId,
                    'gross_amount' => $request->totalPrice,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully',
                    'snap_token' => $snapToken,
                    'order_id' => $orderId
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Booking failed: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateSnapToken($params)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        return \Midtrans\Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $params['order_id'],
                'gross_amount' => $params['gross_amount'],
            ],
        ]);
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
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;
        $query = DataPemesanan::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pemesan', 'like', "%{$request->search}%")
                  ->orWhere('email_pemesan', 'like', "%{$request->search}%")
                  ->orWhere('telepon_pemesan', 'like', "%{$request->search}%");
            });
        }
        if ($request->kota) {
            $query->where('lokasi', $request->kota);
        }
        DB::statement('set @no=0+' . $page * $per);
        $data = $query->with('tiket.konsers')->latest()->get();

        return response()->json($data);
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
            $tickets = DataPemesanan::where('user_id', Auth::id())
                ->with(['tiket.konsers'])
                ->where('status_pembayaran', 'Successfully')
                ->get();

            return response()->json($tickets);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tickets: ' . $e->getMessage()
            ], 500);
        }
    }
}
