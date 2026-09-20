<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // admin / member pages are never served from the browser's cache (stale CSRF tokens -> 419)
        $middleware->web(append: [\App\Http\Middleware\NoStorePages::class]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('member') || $request->is('member/*')) {
                return route('member.login');
            }

            return route('member.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel does not log "404 not found" at all, which made an intermittent 404 impossible to
        // trace. One short line per 404 - what was asked for, how, and from which page - so the log
        // shows it. (Returning nothing lets the normal 404 page render as before.)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            \Illuminate\Support\Facades\Log::warning('404 Not Found', [
                'method'  => $request->method(),
                'url'     => $request->fullUrl(),
                'referer' => $request->headers->get('referer'),
                'reason'  => $e->getPrevious() ? class_basename($e->getPrevious()) . ': ' . $e->getPrevious()->getMessage() : $e->getMessage(),
                'ajax'    => $request->ajax(),
            ]);

            return null;
        });

        // "419 Page Expired" = the form's security token no longer matches the session (the page sat
        // open past the session's life, or was a stored copy from an older session). Instead of a
        // dead-end error page, go back to the page with a fresh token, keep what was typed (never
        // a password) and say what happened - then the same click works. The details are logged so
        // a pattern can be traced.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            \Illuminate\Support\Facades\Log::warning('419 Page Expired', [
                'method'         => $request->method(),
                'url'            => $request->fullUrl(),
                'referer'        => $request->headers->get('referer'),
                'session_cookie' => $request->hasCookie(config('session.cookie')),
                'form_token'     => $request->has('_token') || $request->headers->has('X-CSRF-TOKEN'),
                'admin'          => \Illuminate\Support\Facades\Auth::guard('admin')->check(),
                'member'         => \Illuminate\Support\Facades\Auth::guard('member')->check(),
                // how this server is set up to keep sessions, and how the request looked to it
                'session'        => [
                    'driver'   => config('session.driver'),
                    'lifetime' => config('session.lifetime'),
                    'domain'   => config('session.domain'),
                    'secure'   => config('session.secure'),
                    'same_site' => config('session.same_site'),
                ],
                'request'        => [
                    'host'   => $request->getHost(),
                    'secure' => $request->secure(),
                    'agent'  => substr((string) $request->userAgent(), 0, 80),
                ],
            ]);

            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation', 'recaptcha_token']))
                ->withErrors(['csrf' => 'Your page had expired, so nothing was saved. Please try again.']);
        });
    })->create();
