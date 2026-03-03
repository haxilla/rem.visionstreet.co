<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
 $file = app_path('code/users_rebuild.php');
 require $file;   // require throws fatal if file has parse error / etc.
    return view('index');
});
