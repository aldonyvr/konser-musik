<?php

namespace App\Http\Controllers;

use App\Models\Register;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    public function index(Request $request) 
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Register::when($request->search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        })->latest()->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);   
    }  
    public function get(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => Register::all(),
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|unique:register,email',
                'password' => 'required|confirmed|min:6',
            ]);
    
            $data = $validatedData;
            if ($request->hasFile('photo')) {
                $data['photo'] = '/storage/' . $request->file('photo')->store('user', 'public');
            }
    
            $register = Register::create($data);
    
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $register,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => 'error data',
            ], 500);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 400);
        }
    }

    public function destroy(Register $register)
    {
        if ($register->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $register->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $register->photo));
        }

        $register->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function update(Request $request, $uuid)
    {
        $register = Register::where('uuid', $uuid)->first();

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        $validatedData = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:15',
            'email' => 'sometimes|required|email|unique:registers,email,' . $register->id,
            'password' => 'sometimes|required|confirmed|min:6',
        ]);

        $data = $validatedData;

        if ($request->hasFile('photo')) {
            if ($register->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $register->photo))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $register->photo));
            }

            $data['photo'] = '/storage/' . $request->file('photo')->store('user', 'public');
        }

        $register->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diubah',
            'data' => $register,
        ]);
    }

    public function show($uuid)
    {
        $register = Register::where('uuid', $uuid)->first();

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $register,
        ]);
    }

    public function edit($uuid)
    {
        $register = Register::where('uuid', $uuid)->first();

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $register,
        ]);
    }
}
