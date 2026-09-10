<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('admin')->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }
            return redirect()->route('admin.login')->withErrors(['email' => 'Please log in to access the Admin Control Center.']);
        }

        $admin = auth('admin')->user();
        if (!$admin->isActive()) {
            auth('admin')->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Your admin account is suspended.']);
        }

        return $next($request);
    }
}
