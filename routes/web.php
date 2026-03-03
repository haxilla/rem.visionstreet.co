<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    require app_path('code/users_rebuild.php');
    require app_path('')    // require throws fatal if file has parse error / etc.
    return view('index');
});
