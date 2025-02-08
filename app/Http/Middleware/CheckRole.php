<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->hasRole('concert_admin')) {
            // Only allow access to scanning and report routes
            $allowedRoutes = [
                'scanner.index',
                'scanner.history',
                'scanner.reports',
                'scanner.dashboard'
            ];

            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        return $next($request);
    }
}
