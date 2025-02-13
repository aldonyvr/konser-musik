<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MitraUserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Allow access for admin and mitra
        if ($user && in_array($user->role_id, ['1', '3', 1, 3])) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized access'
        ], 403);
    }
}
