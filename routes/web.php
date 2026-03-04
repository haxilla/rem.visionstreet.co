<?php

//index
Route::get('/', [
    'as'=>'public.index',
    'uses'=>'public\indexController@index']);
