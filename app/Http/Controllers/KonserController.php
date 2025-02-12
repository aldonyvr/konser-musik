<?php

namespace App\Http\Controllers;

use App\Http\Requests\KonserRequest;
use App\Models\Konser;
use App\Models\Lokasi;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Storage;

class KonserController extends Controller
{
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Konser::all()
        ]);
    }

    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = Tiket::with('konser')->when($request->search, function (Builder $query, string $search) {
        $query = Konser::with(['tiket' => function($query) {
            $query->select('id', 'konsers_id', 'harga_regular', 'harga_vip', 'reguler', 'vip', 
                          'gate_a_capacity', 'gate_b_capacity', 'gate_c_capacity');
        }]);
    })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

        // If perPage is -1 or 0, return all records
        return response()->json($data);
    }

    public function getCities()
    {
        $cities = Konser::select('lokasi')
            ->distinct()
            ->whereNotNull('lokasi')
            ->orderBy('lokasi')
            ->get()
            ->map(function ($item) {
                return [
                    'lokasi' => $item->lokasi
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }

    // Metode lainnya tetap sama seperti yang Anda miliki sebelumnya
    public function destroy($uuid)
    {
        $konser = Konser::findByUuid($uuid);
        if ($konser) {
            $konser->delete();
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

    public function store(Request $request)
{
    // Validasi input
    $req = $request->validate([
        'title' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'jam' => 'required',
        'lokasi' => 'required|string|max:255',
        'kontak' => 'nullable|string|max:255',
        'deskripsi' => 'required|string',
        'nama_sosmed' => 'required|string|max:255',
        'image' => 'required|file|mimes:jpg,png,jpeg|max:2048',
    ]);

    // Jika ada file gambar
    if ($request->hasFile('image')) {
        $req['image'] = $request->file('image')->store('media', 'public');
    }

    // Gunakan transaksi untuk menyimpan data
        // Simpan data ke tabel `konser`
        $konser = Konser::create([
            'title' => $req['title'],
            'tanggal' => $req['tanggal'],
            'jam' => $req['jam'],
            'lokasi' => $req['lokasi'],
            'kontak' => $req['kontak'] ?? null,
            'deskripsi' => $req['deskripsi'],
            'nama_sosmed' => $req['nama_sosmed'],
            'image' => "/storage/".$req['image']
        ]);

        // Simpan data ke tabel `tiket`
        $tiket = Tiket::create([
            'konsers_id' => $konser->id, // Hubungkan dengan konser ID
            'tiket_tersedia' => $req['tiket_tersedia'] ?? 0, // Default jika tidak ada
        ]);

        // $lokasi = Lokasi::create([
        //     'konser_id' => $konser->id,
        //     'lokasi' => $req['lokasi']
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Data konser dan tiket berhasil disimpan',
            'data' => [
                'konser' => $konser,
                'tiket' => $tiket,
            ]
        ]);
}


    public function edit($uuid)
    {
        $konser = Konser::findByUuid($uuid);
        return response()->json([
            'success' => true,
            'data' => $konser
        ]);
    }

    public function update(Request $request, $uuid)
    {
        try {
            $konser = Konser::findByUuid($uuid);
            if (!$konser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konser not found'
                ], 404);
            }
    
            $req = $request->validate([
                'title' => 'required|string|max:255',
                'tanggal' => 'required|date',
                'jam' => 'required',
                'lokasi' => 'required|string|max:255',
                'kontak' => 'nullable|string|max:255',
                'deskripsi' => 'required|string',
                'nama_sosmed' => 'required|string|max:255',
                'image' => 'nullable|file|mimes:jpg,png,jpeg|max:2048',
            ]);
    
            // Handle image update
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($konser->image) {
                    $oldPath = str_replace('/storage/', '', $konser->image);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('media', 'public');
                $req['image'] = "/storage/" . $imagePath;
            } else {
                // If no new image uploaded, keep the existing image
                unset($req['image']);
            }
    
            $konser->update($req);
    
            return response()->json([
                'success' => true,
                'message' => 'Konser updated successfully',
                'data' => $konser
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating konser: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($uuid)
    {
        $konser = Konser::findByUuid($uuid);
        return response()->json([
            'status' => true,
            'data' => $konser,
        ]);
    }
}