<?php

namespace App\Http\Controllers;

use App\Http\Requests\TiketRequest;
use App\Models\Konser;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;  
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TiketController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Tiket::all()
        ]);
    }

    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        // Change 'konsers' to 'konser' in the relationship name
        $data = Tiket::with('konser')->when($request->search, function (Builder $query, string $search) {
            $query->whereHas('konser', function($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            });
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);
        
        return response()->json($data);
    }

    public function destroy($uuid)
    {
        $tiket = Tiket::findByUuid($uuid);
        if ($tiket) {
            $tiket->delete();
            return response()->json([
                'message' => "Data successfully deleted",
                'code' => 200
            ]);
        } else {
            return response([
                'message' => "Failed delete data $uuid / data doesn't exists"
            ]);
        }
    }

    public function store (TiketRequest $request)
    {
        $req = $request->validated();

        if($request->hasFile('image')){
            $req['image'] = $request->file('image')->store('Tiket', 'public');
        }
        $add = Tiket::create($req);
        return response()->json([
            'success' => true,
            'data' => $add
        ]);
    }
    public function edit($uuid)
    {
        try {
            $tiket = Tiket::with('konser')
                ->where('uuid', $uuid)
                ->firstOrFail();

            if (!$tiket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan'
                ], 404);
            }

            $responseData = [
                'id' => $tiket->id,
                'uuid' => $tiket->uuid,
                'harga_regular' => $tiket->harga_regular,
                'harga_vip' => $tiket->harga_vip,
                'reguler' => $tiket->reguler,
                'vip' => $tiket->vip,
                'opengate' => $tiket->opengate,
                'closegate' => $tiket->closegate,
                'gate_a_capacity' => $tiket->gate_a_capacity,
                'gate_b_capacity' => $tiket->gate_b_capacity,
                'gate_c_capacity' => $tiket->gate_c_capacity,
                'gate_d_capacity' => $tiket->gate_d_capacity,
                'gate_e_capacity' => $tiket->gate_e_capacity,
                'konser' => $tiket->konser ? [
                    'id' => $tiket->konser->id,
                    'title' => $tiket->konser->title,
                    'tiket_tersedia' => $tiket->konser->tiket_tersedia
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in TiketController@edit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $uuid)
    {
        try {
            DB::beginTransaction();
            
            $tiket = Tiket::where('uuid', $uuid)->first();
            if (!$tiket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan'
                ], 404);
            }

            $validatedData = $request->validate([
                'harga_vip' => 'required|numeric',
                'harga_regular' => 'required|numeric',
                'vip' => 'required|numeric',
                'reguler' => 'required|numeric',
                'opengate' => 'required',
                'closegate' => 'required',
                'gate_a_capacity' => 'nullable|numeric',
                'gate_b_capacity' => 'nullable|numeric',
                'gate_c_capacity' => 'nullable|numeric',
                'gate_d_capacity' => 'nullable|numeric',
                'gate_e_capacity' => 'nullable|numeric',
            ]);

            $tiket->update($validatedData);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tiket berhasil diperbarui',
                'data' => $tiket->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating ticket: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tiket: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function show($uuid)
    {
        try {
            $konser = Konser::findByUuid($uuid);
            if (!$konser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konser not found'
                ], 404);
            }
            $tiket = Tiket::where('konsers_id', $konser->id)->first();
            $tiket->konser = $konser;
        
            return response()->json([
                'success' => true,
                'data' => $tiket
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



}
