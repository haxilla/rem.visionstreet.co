<?php

//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\public\indexController@index',
]);
