<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;
        $query = Banner::query();

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

    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Banner::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $req = $request->validate([
            'image' => 'required|file|mimes:jpg,png,jpeg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $req['image'] = $request->file('image')->store('media', 'public');
        }

        $banner = Banner::create([
            'image' => "/storage/".$req['image']
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Banner berhasil disimpan',
            'data' => $banner
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($uuid)
    {
        $banner = Banner::findByUuid($uuid);
        return response()->json([
            'status' => true,
            'data' => $banner,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $banner = Banner::findByUuid($uuid);
        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $banner = Banner::findByUuid($uuid);
        if ($banner) {
            $banner->update($request->all());
            return response()->json([
                'status' => 'true',
                'message' => 'Banner berhasil diubah'
            ]);
        } else {
            return response([
                'message' => 'gagal mengubah'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $banner = Banner::findByUuid($uuid);
        if ($banner) {
            unlink("storage/" . $banner->image);
            $banner->delete();
            return response()->json([   
                'message' => "Banner successfully deleted",
                'code' => 200
            ]);
        } else {
            return response([
                'message' => "Failed delete data $uuid / data doesn't exists"
            ]);
        }
    }
}
