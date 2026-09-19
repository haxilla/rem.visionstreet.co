<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Enforces the per-agent login block (propagents.loginBlocked = 1) on
 * every request in the member area, so blocking an agent also ends a
 * session that is already open - not just future sign-ins (those are
 * checked in guestController::memberLogin).
 *
 * An admin impersonating an agent is deliberately let through, so a
 * blocked agent's account can still be viewed and investigated.
 *
 * Until the loginBlocked column exists the attribute is simply absent
 * and nobody is treated as blocked, so nothing is locked out by a
 * missing column.
 */
class EnsureAgentNotBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $agent = Auth::guard('member')->user();

        if (
            $agent
            && (int) ($agent->loginBlocked ?? 0) === 1
            && !$request->session()->has('impersonator_admin_id')
        ) {
            Auth::guard('member')->logout();
            $request->session()->regenerateToken();

            return redirect('/member/login')->withErrors([
                'xxAgtUname' => 'Your account has been blocked. Please contact support.',
            ]);
        }

        return $next($request);
    }
}
