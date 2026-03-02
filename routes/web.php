<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    include(app_path('code/users_rebuild.php'));
    return view('welcome');
});
