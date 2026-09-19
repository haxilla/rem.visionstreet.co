<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\guest\guestController;
use App\Http\Controllers\admin\adminController;
use App\Http\Controllers\member\memberController;
use App\Http\Controllers\admin\bounceboxController;

//index
Route::get('/', [
  'as'   => 'public.index',
  'uses' => '\App\Http\Controllers\guest\guestController@index',
]);

//admin/login
Route::redirect('/admin', '/admin/login');
Route::get('/admin/login', [guestController::class, 'adminLoginForm'])->name('admin.login');
Route::post('/admin/login', [guestController::class, 'adminLogin'])->middleware('throttle:5,1')->name('admin.login.submit');

//member/login
Route::get('/member', fn () => redirect('/member/login'));
Route::get('/login', fn () => redirect('/member/login'));
Route::get('/member/login', 
[guestController::class, 'memberLoginForm'])->name('member.login');
Route::post('/member/login',
[guestController::class, 'memberLogin'])->middleware('throttle:5,1')->name('member.login.submit');

//bouncebox - retired, views/app files moved to 0ld
//Route::post('/admin/bounces/group-delete', [bounceboxController::class, 'groupDelete'])
//    ->name('admin.bounces.groupDelete');
//
//Route::get('/admin/bounces/{messageNumber}', [bounceboxController::class, 'view'])
//    ->whereNumber('messageNumber')
//    ->name('admin.bounces.view');

//public details
Route::get('/homedetails/{flyerslug}',
[guestController::class, 'publicDetails'])->name('public.details');

//delete the ticked agents from the "No Start Date" list (POST only - it deletes data, so it carries a CSRF token)
Route::post('/admin/agentsDeleteMany',
[adminController::class, 'agentsDeleteMany'])->name('admin.agentsDeleteMany');

Route::get('/admin/agentView/{id}',
[adminController::class, 'agentView'])->name('admin.agentView');

//add / subtract an agent's credits, and set / change their start date (POST only - each changes data)
Route::post('/admin/agentCredits/{id}',
[adminController::class, 'agentCredits'])->whereNumber('id')->name('admin.agentCredits');

Route::post('/admin/agentStartDate/{id}',
[adminController::class, 'agentStartDate'])->whereNumber('id')->name('admin.agentStartDate');

//password reset email for an agent (stand-in until email sending exists) and login block on/off
//(POST only - both change or will change data, so they carry a CSRF token; admin-only via the controller)
Route::post('/admin/agentPasswordReset/{id}',
[adminController::class, 'agentPasswordReset'])->whereNumber('id')->name('admin.agentPasswordReset');

Route::post('/admin/agentLoginBlock/{id}',
[adminController::class, 'agentLoginBlock'])->whereNumber('id')->name('admin.agentLoginBlock');

Route::get('/admin/agentLogin/{id}',
[adminController::class, 'agentLogin'])->name('admin.agentLogin');

Route::get('/admin/agentFlyerCreate/{id}',
[adminController::class, 'agentFlyerCreate'])->name('admin.agentFlyerCreate');

Route::get('/admin/returnToAdmin',
[adminController::class, 'returnToAdmin'])->name('admin.returnToAdmin');

Route::get('/admin/logout',
[adminController::class, 'logout'])->name('admin.logout');

//flyer details
Route::get('/flyer/{flyerId}', [guestController::class, 'flyerDetail'])->name('flyer.detail');
Route::get('/member/flyerEdit/{flyerId}', [memberController::class, 'flyerEdit'])->name('member.flyeredit');
Route::get('/member/flyer/text/{flyerId}', [memberController::class, 'flyerText'])->name('member.flyerText');
Route::get('/member/flyer/photos/{flyerId}', [memberController::class, 'flyerPhotos'])->name('member.flyerPhotos');

//full campaign history for one of the agent's flyers (linked from the dashboard's flyer cards)
Route::get('/member/campaigns/{flyerId}', [memberController::class, 'flyerCampaigns'])
    ->whereNumber('flyerId')->name('member.flyerCampaigns');

//live flyer preview for the Details step (renders unsaved form values; never saves)
Route::post('/member/flyerPreview', [memberController::class, 'flyerPreview'])->name('member.flyerPreview');

Route::get('/admin/flyerEdit/{flyerId}', [adminController::class, 'flyerEdit'])->name('admin.flyeredit');
Route::get('/admin/flyerCamps/{flyerId}', [adminController::class, 'flyerCamps'])->name('admin.flyerCamps');

//campaign approval (POST only - changes data, so it must carry a CSRF token)
Route::post('/admin/campaignApprove/{flyerId}', [adminController::class, 'campaignApprove'])
    ->whereNumber('flyerId')->name('admin.campaignApprove');
Route::post('/admin/campaignAddArea/{flyerId}', [adminController::class, 'campaignAddArea'])
    ->whereNumber('flyerId')->name('admin.campaignAddArea');

//trial mode on/off from the impersonation banner (POST only; changes a system-wide setting)
Route::post('/admin/trialToggle', [adminController::class, 'trialToggle'])->name('admin.trialToggle');

//system-wide admin settings (GET /admin/settings is served by the segments convention)
Route::post('/admin/settings', [adminController::class, 'settingsSave'])->name('admin.settingsSave');


Route::match(['get', 'post'], '/admin/{segments}', [adminController::class, 'segments'])
    ->where('segments', '.+');

//route for multiple segments
Route::match(['get', 'post'], '/member/{segments}', [memberController::class, 'segments'])
    ->where('segments', '.+');

//route for single segment only
Route::get('/{segment}', [guestController::class, 'segment'])
    ->where('segment', '[^/]+');

