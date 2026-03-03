<?php

use Illuminate\Support\Facades\Route;
use App\Models\Core\PropFlyer;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    require app_path('code/users_rebuild.php');
    require app_path('home/index.php');    // require throws fatal if file has parse error / etc.
    return view('index');
});



Route::get('/_diag', function () {
    // proves Laravel booted + shows DB connection details without dumping secrets
    return [
        'app_debug' => config('app.debug'),
        'env' => app()->environment(),
        'db_connection' => config('database.default'),
        'db_database' => config('database.connections.'.config('database.default').'.database'),
        'db_host' => config('database.connections.'.config('database.default').'.host'),
    ];
});

Route::get('/_flyers', function () {
    DB::connection()->getPdo(); // forces connection; will throw a useful error if DB is wrong
    return PropFlyer::query()->limit(5)->get();
});
