<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAccountApproved
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            $isPendingOrRejected = in_array($user->status, ['pending', 'rejected']);

            if ($isPendingOrRejected) {
                // Allow user to access waiting room, logout, status poll, or language switcher
                $allowed = $request->routeIs('waiting-room') 
                    || $request->routeIs('waiting-room.status')
                    || $request->is('logout') 
                    || $request->is('lang/*')
                    || $request->is('api/waiting-room/status');

                if (!$allowed) {
                    return redirect()->route('waiting-room');
                }
            } else {
                // If user is already approved/super admin and tries to visit waiting room, redirect to dashboard
                if ($request->routeIs('waiting-room')) {
                    return redirect()->intended('/');
                }
            }
        }

        return $next($request);
    }
}
