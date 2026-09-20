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

//member password: "forgot password" and the emailed one-time "set your new password" link
//(guest pages - no login needed; defined before the /member/{segments} catch-all below)
Route::get('/member/password/forgot',
[guestController::class, 'passwordForgotForm'])->name('member.password.forgot');
Route::post('/member/password/forgot',
[guestController::class, 'passwordForgot'])->middleware('throttle:5,1')->name('member.password.forgot.send');
Route::get('/member/password/set/{token}',
[guestController::class, 'passwordSetForm'])->where('token', '[A-Za-z0-9]{64}')->name('member.password.set');
Route::post('/member/password/set/{token}',
[guestController::class, 'passwordSet'])->where('token', '[A-Za-z0-9]{64}')->middleware('throttle:10,1')->name('member.password.set.save');

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

//every campaign an agent has, grouped by flyer (linked from the "Campaigns Sent" tile)
Route::get('/admin/agentCampaigns/{id}',
[adminController::class, 'agentCampaigns'])->whereNumber('id')->name('admin.agentCampaigns');

//upload (add / change) or clear an agent's photo or logo (POST only; {kind} is "photo" or "logo")
Route::post('/admin/agentImage/{id}/{kind}',
[adminController::class, 'agentImageUpload'])->whereNumber('id')->whereIn('kind', ['photo', 'logo'])->name('admin.agentImageUpload');

Route::post('/admin/agentImage/{id}/{kind}/clear',
[adminController::class, 'agentImageClear'])->whereNumber('id')->whereIn('kind', ['photo', 'logo'])->name('admin.agentImageClear');

//set an agent's credits, and set / change their start date (POST only - each changes data)
Route::post('/admin/agentCredits/{id}',
[adminController::class, 'agentCredits'])->whereNumber('id')->name('admin.agentCredits');

Route::post('/admin/agentStartDate/{id}',
[adminController::class, 'agentStartDate'])->whereNumber('id')->name('admin.agentStartDate');

//password reset email for an agent (stand-in until email sending exists) and login block on/off
//(POST only - both change or will change data, so they carry a CSRF token; admin-only via the controller)
Route::post('/admin/agentPasswordReset/{id}',
[adminController::class, 'agentPasswordReset'])->whereNumber('id')->name('admin.agentPasswordReset');

//move an agent's login to a new email (for an agent who lost access to the old one); POST only, admin-only
Route::post('/admin/agentLoginEmail/{id}',
[adminController::class, 'agentLoginEmail'])->whereNumber('id')->name('admin.agentLoginEmail');

//merge duplicate accounts (same login email): pick flyers, move them into one account. Only ever
//available when the agent has a duplicate - the controller refuses otherwise. POST changes data.
Route::get('/admin/agentMerge/{id}',
[adminController::class, 'agentMerge'])->whereNumber('id')->name('admin.agentMerge');
Route::post('/admin/agentMerge/{id}',
[adminController::class, 'agentMergeSave'])->whereNumber('id')->name('admin.agentMergeSave');

//edit an agent's contact details and their address / office (POST only; admin-only via the controller)
Route::post('/admin/agentContact/{id}',
[adminController::class, 'agentContactSave'])->whereNumber('id')->name('admin.agentContactSave');
Route::post('/admin/agentOffice/{id}',
[adminController::class, 'agentOfficeSave'])->whereNumber('id')->name('admin.agentOfficeSave');

//delete an agent from their own page - only one with no start date and no credits (POST only; re-checked in the controller)
Route::post('/admin/agentDelete/{id}',
[adminController::class, 'agentDelete'])->whereNumber('id')->name('admin.agentDelete');

//backdate the receiving account's start date to the earliest one in the duplicate group (POST only)
Route::post('/admin/agentMergeStartDate/{id}',
[adminController::class, 'agentMergeStartDate'])->whereNumber('id')->name('admin.agentMergeStartDate');

//move a duplicate account's order + campaign history into another account on the same email (POST only)
Route::post('/admin/agentMoveRecords/{id}',
[adminController::class, 'agentMoveRecords'])->whereNumber('id')->name('admin.agentMoveRecords');

//delete a leftover duplicate account (POST only); the controller refuses unless it's a duplicate with no flyers
Route::post('/admin/agentDeleteDuplicate/{id}',
[adminController::class, 'agentDeleteDuplicate'])->whereNumber('id')->name('admin.agentDeleteDuplicate');

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

//the agent's own "Account Info" page (plan, credits, username + password reset link, order history)
Route::get('/member/account', [memberController::class, 'accountInfo'])->name('member.account');
Route::post('/member/account/password-link', [memberController::class, 'accountPasswordLink'])
    ->middleware('throttle:5,1')->name('member.account.passwordLink');

//the agent's own "Agent Info" page: edit details, add / change / remove photo and logo (member-only via the controller)
Route::get('/member/agent-info', [memberController::class, 'agentInfo'])->name('member.agentInfo');
Route::post('/member/agent-info', [memberController::class, 'agentInfoSave'])->name('member.agentInfo.save');
Route::post('/member/agent-info/image/{kind}', [memberController::class, 'agentImageUpload'])
    ->whereIn('kind', ['photo', 'logo'])->name('member.agentInfo.image');
Route::post('/member/agent-info/image/{kind}/clear', [memberController::class, 'agentImageClear'])
    ->whereIn('kind', ['photo', 'logo'])->name('member.agentInfo.imageClear');

//live flyer preview for the Details step (renders unsaved form values; never saves)
Route::post('/member/flyerPreview', [memberController::class, 'flyerPreview'])->name('member.flyerPreview');

Route::get('/admin/flyerEdit/{flyerId}', [adminController::class, 'flyerEdit'])->name('admin.flyeredit');
Route::get('/admin/flyerCamps/{flyerId}', [adminController::class, 'flyerCamps'])->name('admin.flyerCamps');

//campaign approval (POST only - changes data, so it must carry a CSRF token)
Route::post('/admin/campaignApprove/{flyerId}', [adminController::class, 'campaignApprove'])
    ->whereNumber('flyerId')->name('admin.campaignApprove');
Route::post('/admin/campaignUnapprove/{flyerId}', [adminController::class, 'campaignUnapprove'])
    ->whereNumber('flyerId')->name('admin.campaignUnapprove');
Route::post('/admin/campaignAddArea/{flyerId}', [adminController::class, 'campaignAddArea'])
    ->whereNumber('flyerId')->name('admin.campaignAddArea');
//edit a campaign's requested / started / completed dates (POST only)
Route::post('/admin/campaignDates/{cid}', [adminController::class, 'campaignDates'])
    ->whereNumber('cid')->name('admin.campaignDates');

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

