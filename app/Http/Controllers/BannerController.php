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
                $q->where('title', 'like', "%{$request->search}%");
            });
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
            'image' => 'required|file|mimes:jpg,png,jpeg',
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

    public function show($uuid)
    {
        $banner = Banner::findByUuid($uuid);
        return response()->json([
            'status' => true,
            'data' => $banner,
        ]);
    }

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
        try {
            $banner = Banner::findByUuid($uuid);
            $req = $request->validate([
                'image' => 'nullable|file|mimes:jpg,png,jpeg',
            ]);

            if ($request->hasFile('image')) {
                $req['image'] = $request->file('image')->store('media', 'public');
            }

            $banner->update([
                'image' => "/storage/".$req['image']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil diupdate',
                'data' => $banner
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating banner: ' . $e->getMessage()
            ], 500);
        }
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            DB::beginTransaction();
            
            $banner = Banner::where('uuid', $uuid)->first();
            
            if (!$banner) {
                throw new \Exception('Banner tidak ditemukan');
            }

            // Remove image file if exists
            $imagePath = str_replace('/storage/', '', $banner->image);
            if (\Storage::disk('public')->exists($imagePath)) {
                \Storage::disk('public')->delete($imagePath);
            }

            // Delete the banner record
            $banner->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Banner berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Banner delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus banner: ' . $e->getMessage()
            ], 500);
        }
    }
}
