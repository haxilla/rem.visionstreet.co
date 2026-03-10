<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\public\indexController;
use App\Http\Controllers\admin\adminController;

//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\public\indexController@index',
]);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/hello', [adminController::class, 'segments']);
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

//route for single segment only
Route::get('/{segment}', [indexController::class, 'segment'])
    ->where('segment', '[^/]+');

//route for multiple segments
Route::get('/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+/.+');