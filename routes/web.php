<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    include(app_path('code/hi.php'));
    return view('welcome');
});
