<?php

namespace App\Http\Middleware;

use App\Support\AgentTime;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * In the member area everything the system records is in the signed-in
 * agent's own timezone (from their state) - see App\Support\AgentTime.
 * An admin impersonating an agent is the agent as far as the member guard
 * is concerned, so it records in that agent's timezone too.
 */
class SetAgentTimezone
{
    public function handle(Request $request, Closure $next)
    {
        $agent = Auth::guard('member')->user();

        if ($agent) {
            AgentTime::apply($agent);
        }

        return $next($request);
    }
}
