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
        //
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
    })->create();
