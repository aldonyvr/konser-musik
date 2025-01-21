<?php

namespace App\Http\Controllers;

use App\Http\Requests\KonserRequest;
use App\Models\Konser;
use App\Models\Lokasi;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;  

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
        $query = Konser::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('deskripsi', 'like', "%{$request->search}%")
                  ->orWhere('lokasi', 'like', "%{$request->search}%");
            });
        }
        if ($request->kota) {
            $query->where('lokasi', $request->kota);
        }
        DB::statement('set @no=0+' . $page * $per);
        $data = $query->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);

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
        'tiket_tersedia' => 'required|integer|min:0',
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
            'tiket_tersedia' => $req['tiket_tersedia'],
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
        $konser = Konser::findByUuid($uuid);
        if ($konser) {
            $konser->update($request->all());
            return response()->json([
                'status' => 'true',
                'message' => 'data berhasil diubah'
            ]);
        } else {
            return response([
                'message' => 'gagal mengubah'
            ]);
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