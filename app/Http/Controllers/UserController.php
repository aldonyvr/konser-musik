<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request)
    {
        try {
            $user = auth()->user();
            $query = User::query();

            // If user is mitra, only return their own data
            if ($user->role_id === '3') {
                $query->where('id', $user->id);
            }

            \Log::info('User query:', [
                'user_role' => $user->role_id,
                'is_mitra' => $user->role_id === '3'
            ]);

            return response()->json([
                'success' => true,
                'data' => $query->get()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in UserController@get: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users'
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }

    /**
     * Display a paginated list of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $query = User::query();

            // If user is mitra, only show their own data
            if ($user->role_id === '3') {
                $query->where('id', $user->id);
            }

            \Log::info('User index query:', [
                'user_role' => $user->role_id,
                'is_mitra' => $user->role_id === '3'
            ]);

            $data = $query->paginate($request->per ?? 10);
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error in UserController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users'
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Create user with konser_id if role is mitra
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role_id' => $request->role_id
            ];

            // Add konser_id if role is mitra (role_id = 3)
            if ($request->role_id == '3' && $request->konser_id) {
                $userData['konser_id'] = $request->konser_id;
            }

            $user = User::create($userData);

            $role = Role::findById($request->role_id);
            $user->assignRole($role);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user['role_id'] = $user?->role?->id;
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        } else {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
                $validatedData['photo'] = null;
            }
        }

        $user->update($validatedData);

        $role = Role::findById($validatedData['role_id']);
        $user->syncRoles($role);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return response()->json([
            'success' => true
        ]);
    }
}