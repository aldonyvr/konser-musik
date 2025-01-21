<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Midtrans\Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DataPemesanan;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;

class DataPemesananController extends Controller
{
    public function get()
    {
        try {
            $pemesanan = DataPemesanan::all();
            return response()->json([
                'success' => true,
                'data' => $pemesanan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booking data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
      
    }

    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pemesanan = DataPemesanan::find($request->id);
            
            if (!$pemesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pemesanan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booking data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($uuid)
    {
        try {
            $pemesanan = DataPemesanan::where('tiket_id', $uuid)->first();
            
            if (!$pemesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $pemesanan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booking data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $uuid)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_pemesan' => 'required|string',
                'email_pemesan' => 'required|email',
                'telepon_pemesan' => 'required|string',
                'alamat_pemesan' => 'required|string',
                'total_harga' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pemesanan = DataPemesanan::where('tiket_id', $uuid)->first();
            
            if (!$pemesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $pemesanan->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Booking updated successfully',
                'data' => $pemesanan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating booking data: ' . $e->getMessage()
            ], 500);
        }
    }
}