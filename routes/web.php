<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\guest\guestController;
use App\Http\Controllers\admin\adminController;
use App\Http\Controllers\member\memberController;
use App\Http\Controllers\bounceboxController;


//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\guest\guestController@index',
]);

//admin/login
Route::redirect('/admin', '/admin/login');
Route::get('/admin/login', [guestController::class, 'adminLoginForm'])->name('admin.login');
Route::post('/admin/login', [guestController::class, 'adminLogin'])->name('admin.login.submit');

//member/login
Route::get('/member', fn () => redirect('/member/login'));
Route::get('/login', fn () => redirect('/member/login'));
Route::get('/member/login', 
[guestController::class, 'memberLoginForm'])->name('member.login');
Route::post('/member/login', 
[guestController::class, 'memberLogin'])->name('member.login.submit');


//bouncebox
Route::get('/admin/bouncebox/{messageNumber}', [bounceboxController::class, 'view'])
    ->whereNumber('messageNumber')
    ->name('admin.bouncebox.view');

//public details
Route::get('/homedetails/{flyerslug}',
[guestController::class, 'publicDetails'])->name('public.details');

//agentDelete
Route::get('/admin/agentDelete/{id}',
[adminController::class, 'agentDelete'])->name('admin.agentDelete');

Route::get('/admin/agentView/{id}',
[adminController::class, 'agentView'])->name('admin.agentView');

Route::get('/admin/agentLogin/{id}',
[adminController::class, 'agentLogin'])->name('admin.agentLogin');

//flyer details
Route::get('/flyer/{flyerId}', [guestController::class, 'flyerDetail'])->name('flyer.detail');

//route for multiple segments
Route::get('/admin/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+');

//route for multiple segments
Route::get('/member/{segments}', [memberController::class, 'segments'])
    ->where('segments', '.+');

//route for single segment only
Route::get('/{segment}', [guestController::class, 'segment'])
    ->where('segment', '[^/]+');