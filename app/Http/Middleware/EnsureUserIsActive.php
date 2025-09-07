<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $u = $request->user();

            if ($u->deactivated_at || $u->banned_at) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $msg = $u->deactivated_at
                    ? 'Your account is deactivated.'
                    : 'Your account is banned.';

                if ($u->banned_at && $u->ban_reason) {
                    $msg .= ' Reason: '.$u->ban_reason;
                }

                return redirect('/')->with('message', $msg);
            }
        }

        return $next($request);
    }
}
