<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MitraController extends Controller
{
    public function index()
    {
        return response()->json(
            User::role('mitra')
                ->select('id', 'name', 'email', 'phone', 'company_name')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required|string',
            'company_name' => 'required|string'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'company_name' => $validated['company_name']
        ]);

        $user->assignRole('mitra');

        return response()->json([
            'message' => 'Mitra berhasil ditambahkan',
            'data' => $user
        ], 201);
    }

    public function destroy($id)
    {
        $mitra = User::role('mitra')->findOrFail($id);
        $mitra->delete();

        return response()->json([
            'message' => 'Mitra berhasil dihapus'
        ]);
    }
}
