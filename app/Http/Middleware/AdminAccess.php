<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Redirect to login page if not authenticated
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        // Check if user has valid role (agent or staff)
        $user = Auth::user();
        if (!in_array($user->role, ['agent', 'staff', 'admin'])) {
            // Abort with 403 Forbidden if user doesn't have proper role
            abort(403, 'Access denied. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
