<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
 $file = app_path('code/users_rebuild.php');

    if (!is_file($file)) {
        dd('MISSING FILE', $file);
    }

    require $file;   // require throws fatal if file has parse error / etc.

    return view('welcome');
});
