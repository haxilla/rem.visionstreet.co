<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The admin and member pages are never kept in the browser's cache.
 *
 * Every form on those pages carries a one-time-per-session security token (CSRF). If the
 * browser re-shows a stored copy of a page - the Back button, or reopening a tab - the copy
 * can hold a token from a session that has since been replaced, and submitting it gives
 * "419 Page Expired". With no-store the browser always asks the server, so the page (and
 * its token) is fresh. It also means a signed-out browser can't show an old account page
 * from the cache.
 */
class NoStorePages
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $request->is('admin', 'admin/*', 'member', 'member/*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        }

        return $response;
    }
}
