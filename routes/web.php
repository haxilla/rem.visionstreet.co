<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\guest\guestController;
use App\Http\Controllers\admin\adminController;
use App\Http\Controllers\member\memberController;


//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\public\indexController@index',
]);

//admin/login
Route::redirect('/admin', '/admin/login');
Route::get('/admin/login', [guestController::class, 'adminLoginForm'])->name('admin.login');
Route::post('/admin/login', [guestController::class, 'adminLogin'])->name('admin.login.submit');

//member/login
Route::redirect('/member', '/member/login');
Route::redirect('/login', '/member/login');
Route::get('/member/login', [guestController::class, 'memberLoginForm'])->name('member.login');
Route::post('/member/login', [guestController::class, 'memberLogin'])->name('member.login.submit');

//route for multiple segments
Route::get('/admin/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+/.+');

//route for multiple segments
Route::get('/member/{segments}', [memberController::class, 'segments'])
    ->where('segments', '.+/.+');

//route for single segment only
Route::get('/{segment}', [guestController::class, 'segment'])
    ->where('segment', '[^/]+');