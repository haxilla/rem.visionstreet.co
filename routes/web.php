<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\public\indexController;
use App\Http\Controllers\admin\adminController;

//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\public\indexController@index',
]);

//route for single segment only
Route::get('/{segment}', [indexController::class, 'segment'])
    ->where('segment', '[^/]+');

//route for multiple segments
Route::get('/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+/.+');