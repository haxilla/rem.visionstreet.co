<?php

use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;

// Deleting changes data, so it only accepts a POST (the dashboard's Delete
// button is a small CSRF-protected form) - never a plain link that any page
// could trigger just by getting the agent's browser to open a URL.
if (!request()->isMethod('post')) {
    abort(405);
}

//make sure it belongs to user or error
$flyer = Propflyer::where('id', request('flyerId'))
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    abort(404);
}

// A flyer whose delivery is still waiting or in progress (any campaign not
// yet finished) can't be deleted: the request would stay in the delivery /
// admin approval queue pointing at a flyer that no longer exists. Flyers
// that have already been sent, and drafts, can always be deleted.
$hasActiveDelivery = Propdelivnow::where('propflyer_id', $flyer->id)
    ->whereNull('emComplete')
    ->exists();

// This file ends with redirect()->send() + exit(), which skips the normal
// end-of-request session save - so flashed messages are saved explicitly.
if ($hasActiveDelivery) {
    session()->flash('dashboard_error', 'This flyer can\'t be deleted while its delivery is waiting or in progress. You can delete it once delivery has finished.');
    session()->save();

    redirect('/member/dashboard')->send();
    exit();
}

// Soft delete only (Propflyer uses SoftDeletes) - nothing is removed from
// disk or from any related table (photos, styles, remarks, campaign
// history, etc.). The flyer just stops appearing in normal member
// queries since they all go through this model, which is enough to
// hide everything hanging off it too.
$flyer->delete();

session()->flash('dashboard_status', 'Flyer deleted.');
session()->save();

redirect('/member/dashboard')->send();
exit();
