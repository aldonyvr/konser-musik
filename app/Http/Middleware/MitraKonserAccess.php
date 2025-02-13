<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MitraKonserAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user->role_id === 3) {
            // For mitra, check if they're accessing their own konser data
            $konserId = $request->route('konser_id') ?? $request->input('konser_id');
            
            if ($konserId && $konserId != $user->konser_id) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
        }

        return $next($request);
    }
}
