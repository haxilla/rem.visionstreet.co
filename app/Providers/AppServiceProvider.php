<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RequireRole;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // ✅ Register the alias here (once, globally available)
        Route::aliasMiddleware('role', RequireRole::class);

        // ✅ Then group routes as usual
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

    }
}
