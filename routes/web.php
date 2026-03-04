<?php

use Illuminate\Support\Facades\Route;
use App\Models\Core\Propflyer;
use Illuminate\Support\Facades\DB;

//index
Route::get('/', [
    'as'=>'public.index',
    'uses'=>'public\indexController@index']);
