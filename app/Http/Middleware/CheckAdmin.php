<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the currently authenticated user
            $user = Auth::user();

            // Check if the user's role is 'admin'
            if ($user->role === 'admin') {
                return $next($request); // Allow access
            }
        }

        // If the user is not an admin, deny access
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
