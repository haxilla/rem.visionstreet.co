<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\public\indexController;
use App\Http\Controllers\admin\adminController;

//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\public\indexController@index',
]);

Route::get('/admin/login', [GuestController::class, 'adminLoginForm'])->name('admin.login');
Route::post('/admin/login', [GuestController::class, 'adminLogin'])->name('admin.login.submit');

Route::get('/member/login', [GuestController::class, 'memberLoginForm'])->name('member.login');
Route::post('/member/login', [GuestController::class, 'memberLogin'])->name('member.login.submit');

//route for single segment only
Route::get('/{segment}', [GuestController::class, 'segment'])
    ->where('segment', '[^/]+');

//route for multiple segments
Route::get('/admin/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+/.+');

//route for multiple segments
Route::get('/member/{segments}', [memberController::class, 'segments'])
    ->where('segments', '.+/.+');