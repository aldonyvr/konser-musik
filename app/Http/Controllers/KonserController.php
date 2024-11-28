<?php

namespace App\Http\Controllers;

use App\Http\Requests\KonserRequest;
use App\Models\Konser;
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

        // Pencarian berdasarkan nama atau lokasi
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('full_name', 'like', "%{$request->search}%")
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

    public function store(KonserRequest $request)
{
    // Validasi input
    $req = $request->validated();

    // Jika ada file gambar
    if ($request->hasFile('image')) {
        $req['image'] = $request->file('image')->store('konser', 'public');
    }

    // Gunakan transaksi untuk menyimpan data
    try {
        DB::beginTransaction();

        // Simpan data ke tabel `konser`
        $konser = Konser::create([
            'title' => $req['title'],
            'tanggal' => $req['tanggal'],
            'jam' => $req['jam'],
            'lokasi' => $req['lokasi'],
            'tiket_tersedia' => $req['tiket_tersedia'],
            'kontak' => $req['kontak'] ?? null,
            'deskripsi' => $req['deskripsi'],
            'harga' => $req['harga'],
            'nama_sosmed' => $req['nama_sosmed'],
            'image' => $req['image']
        ]);

        // Simpan data ke tabel `tiket`
        $tiket = Tiket::create([
            'konser_id' => $konser->id, // Hubungkan dengan konser ID
            'user_id' => auth()->id(), // Ambil user yang login
            'vip' => $req['vip'] ?? 0, // Default jika tidak ada
            'reguler' => $req['reguler'] ?? 0,
            'opengate' => $req['opengate'] ?? '00:00',
            'closegate' => $req['closegate'] ?? '23:59',
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Data konser dan tiket berhasil disimpan',
            'data' => [
                'konser' => $konser,
                'tiket' => $tiket,
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data',
            'error' => $e->getMessage()
        ], 500);
    }
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