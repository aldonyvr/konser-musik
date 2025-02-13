<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MitraAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Debug log
        \Log::info('User role_id:', ['role_id' => $user->role_id]);
        
        // Allow access for admin (1) and mitra (3)
        if ($user && (in_array($user->role_id, ['1', '3', 1, 3]))) {
            return $next($request);
        }

        \Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'role_id' => $user->role_id
        ]);

        return response()->json([
            'message' => 'Unauthorized access',
            'role_id' => $user->role_id
        ], 403);
    }
}
