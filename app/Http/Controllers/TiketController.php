<?php

namespace App\Http\Controllers;

use App\Http\Requests\TiketRequest;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;  

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
        $data = Tiket::with('konsers')->when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('full_name', 'like', "%$search%");
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
    public function edit ($uuid){
        $tiket = Tiket::findByUuid($uuid);
        return response()->json([
            'success' => true,
            'data' => $tiket
        ]);
    }
    public function update(Request $request, $uuid)
    {
        $tiket = Tiket::findByUuid($uuid);
        if ($tiket) {
            $tiket->update($request->all());
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
        $tiket = Tiket::findByUuid($uuid);
        return response()->json([
            'status' => true,
            'pasien' => $tiket,
        ]);
    }



}
