<?php

use App\Models\Core\AdminSetting;
use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

// A flyer whose delivery has STARTED (running, not finished) can't be deleted - the mailer is
// working on it. A flyer that is only WAITING in the queue can: its queued requests are cancelled
// (removed from the queue and from admin approval) and the flyer is soft-deleted. Drafts and
// flyers already sent can always be deleted.
//
// This file ends with redirect()->send() + exit(), which skips the normal end-of-request
// session save - so flashed messages are saved explicitly.
$deliveryStarted = fn () => Propdelivnow::where('propflyer_id', $flyer->id)
    ->whereNotNull('emStart')
    ->whereNull('emComplete')
    ->exists();

$refusal = "This flyer can't be deleted while its delivery is in progress. You can delete it once delivery has finished.";

$cancelled = 0;
$refunded  = false;

try {
    DB::transaction(function () use ($flyer, $deliveryStarted, &$cancelled, &$refunded, $refusal) {

        if ($deliveryStarted()) {
            throw new RuntimeException($refusal);
        }

        // Requests still waiting (not started, not finished).
        $queued = Propdelivnow::where('propflyer_id', $flyer->id)
            ->whereNull('emStart')
            ->whereNull('emComplete')
            ->lockForUpdate()
            ->get();

        if ($queued->isNotEmpty()) {
            $charged   = $queued->contains(fn ($campaign) => !$campaign->isAdminAdded());
            $cancelled = Propdelivnow::whereIn('cid', $queued->pluck('cid')->all())->delete();

            // One credit pays for one send request (however many areas it has), and areas an
            // admin added are free. So the credit comes back when an agent's own request is
            // cancelled - unless the system was in test mode, when nothing was charged.
            if ($charged && !AdminSetting::trialMode()) {
                $agent = Propagent::lockForUpdate()->find(auth()->id());

                if ($agent) {
                    $agent->remCreds = ($agent->remCreds ?? 0) + 1;
                    $agent->save();
                    $refunded = true;
                }
            }
        }

        // The mailer may have picked a request up while we were deciding: if anything unfinished
        // is left, it has started - undo everything and refuse.
        if (Propdelivnow::where('propflyer_id', $flyer->id)->whereNull('emComplete')->exists()) {
            throw new RuntimeException($refusal);
        }

        // Soft delete only (Propflyer uses SoftDeletes) - nothing is removed from disk or from any
        // related table (photos, styles, remarks, campaign history, etc.). The flyer just stops
        // appearing in normal member queries since they all go through this model, which is enough
        // to hide everything hanging off it too.
        $flyer->delete();
    });
} catch (RuntimeException $e) {
    session()->flash('dashboard_error', $e->getMessage());
    session()->save();

    redirect('/member/dashboard')->send();
    exit();
}

if ($cancelled > 0) {
    Log::info('Agent deleted a flyer with a waiting request; the request was cancelled', [
        'agent_id'  => auth()->id(),
        'flyer_id'  => $flyer->id,
        'cancelled' => $cancelled,
        'refunded'  => $refunded,
    ]);
}

session()->flash(
    'dashboard_status',
    'Flyer deleted.'
        . ($cancelled > 0 ? ' Its request in the delivery queue was cancelled.' : '')
        . ($refunded ? ' Your credit was returned.' : '')
);
session()->save();

redirect('/member/dashboard')->send();
exit();
