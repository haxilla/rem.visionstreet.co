<?php

use App\Models\Core\AdminSetting;
use App\Models\Core\Propflyer;
use App\Models\Core\Propdelivnow;

$validatedData = $request->validate([

    'flyerId'           => 'required|integer',
    'emSubject'         => 'nullable|string|max:255',
    'areas'             => 'nullable|array|max:2',
    'areas.*'           => 'string|in:phoenix_metro,northeast_valley,southeast_valley,west_valley,northern_az,southern_az',
    'openHouseDate1'    => 'nullable|date',
    'openHouseTime1'    => 'nullable|date_format:H:i',
    'openHouseEndTime1' => 'nullable|date_format:H:i',
    'openHouseDate2'    => 'nullable|date',
    'openHouseTime2'    => 'nullable|date_format:H:i',
    'openHouseEndTime2' => 'nullable|date_format:H:i',
    'agentBonusAmount'  => 'nullable|string|max:255',
    'agentBonusComment' => 'nullable|string|max:255',
    'reducedAmount'     => 'nullable|integer|min:0',
    'reducedDate'       => 'nullable|date',

]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->openHouseDate1    = $validatedData['openHouseDate1'] ?? null;
$flyer->openHouseTime1    = $validatedData['openHouseTime1'] ?? null;
$flyer->openHouseEndTime1 = $validatedData['openHouseEndTime1'] ?? null;
$flyer->openHouseDate2    = $validatedData['openHouseDate2'] ?? null;
$flyer->openHouseTime2    = $validatedData['openHouseTime2'] ?? null;
$flyer->openHouseEndTime2 = $validatedData['openHouseEndTime2'] ?? null;
$flyer->agentBonusAmount  = $validatedData['agentBonusAmount'] ?? null;
$flyer->agentBonusComment = $validatedData['agentBonusComment'] ?? null;

// Manual override - lets an agent set this directly when the flyer was
// created after the reduction already happened, so there's no earlier
// price on record for save_details.php's auto-calc to compare against.
$flyer->reducedAmount = $validatedData['reducedAmount'] ?? null;
$flyer->reducedDate   = $validatedData['reducedDate'] ?? null;

$flyer->save();

// Turn selected areas into pending (unauthorized) campaign requests -
// one propdelivnow row per area, picked up later by the admin approval
// queue and the separate mail-sending system. Costs 1 credit for the
// whole submission (covers up to 2 areas). A flyer that already has a
// request waiting for delivery is never given another one (checked
// below, before anything is added), so nothing can be requested twice.
$agent = auth()->user();

// The same area twice in one submission must not create two requests.
$selectedAreas = array_values(array_unique($validatedData['areas'] ?? []));

// Trial mode (admin Settings): sends are tests, so no credit is needed
// and none is ever charged. Requests are still created normally so the
// whole flow (including admin approval) can be exercised.
$trialMode = AdminSetting::trialMode();

$queued           = false;   // the flyer is now (or already was) waiting for delivery
$alreadyQueued    = false;   // refused: it's already waiting and different areas were asked for
$notEnoughCredits = false;
$busy             = false;   // another submission for this flyer is mid-flight

if (!empty($selectedAreas)) {

    // One submission at a time per flyer. A double-click (or a refresh
    // re-POST) sends two identical requests at once; without this both see
    // "no pending request for this area yet" and each creates its own
    // rows - 2 chosen areas became 4. A MySQL advisory lock needs no
    // tables and works whatever the storage engine. If locking isn't
    // available (e.g. a non-MySQL dev database) carry on unlocked.
    $lockName = 'sendsetup-flyer-' . $flyer->id;
    $haveLock = false;

    try {
        $haveLock = (bool) (DB::selectOne('SELECT GET_LOCK(?, 10) AS got', [$lockName])->got ?? false);
        $busy     = !$haveLock;
    } catch (\Throwable $e) {
        report($e);
    }

    if (!$busy) {
        try {
            $campaignAreaMap = include app_path('flyers/campaignAreas.php');

            // Existence check FIRST, before anything is added: a flyer that
            // already has a request waiting for delivery (not started, not
            // finished) never gets another one.
            $pendingNow = Propdelivnow::where('propflyer_id', $flyer->id)
                ->whereNull('emStart')
                ->whereNull('emComplete')
                ->pluck('emArea')
                ->all();

            $requestedDb = [];
            foreach ($selectedAreas as $memberAreaKey) {
                if (isset($campaignAreaMap[$memberAreaKey])) {
                    $requestedDb[] = $campaignAreaMap[$memberAreaKey]['db'];
                }
            }

            if (!empty($pendingNow)) {

                // Same areas again = a repeated submit (double-click, refresh):
                // nothing to add, and from the agent's side it's still just
                // "in the queue". Different areas = an attempt to add more
                // while one is already queued, which is refused.
                if (empty(array_diff($requestedDb, $pendingNow))) {
                    $queued = true;
                } else {
                    $alreadyQueued = true;
                }

            // Nothing waiting yet. Credits are re-read now that we hold the
            // lock - a request that just finished ahead of us may already
            // have charged one.
            } elseif ($agent->refresh() && ($trialMode || ($agent->remCreds ?? 0) >= 1)) {
                $createdAny = false;

                // Every time recorded here is in the AGENT'S timezone, worked
                // out from the state they live in (see App\Support\AgentTime).
                $agentNow = \App\Support\AgentTime::now($agent);

                $slot = 0;

                foreach ($selectedAreas as $memberAreaKey) {
                    $areaInfo = $campaignAreaMap[$memberAreaKey] ?? null;

                    if (!$areaInfo) {
                        continue;
                    }

                    $slot++;

                    $campaign = new Propdelivnow();
                    $campaign->propflyer_id   = $flyer->id;
                    $campaign->propagent_id   = $flyer->propagent_id;
                    $campaign->emArea         = $areaInfo['db'];
                    $campaign->emArea_display = $areaInfo['label'];
                    $campaign->emSubject      = $validatedData['emSubject'] ?? null;
                    $campaign->totalEmails    = DB::connection('rememaildb')->table($areaInfo['db'])->count();
                    $campaign->emRequest      = $agentNow;
                    $campaign->campCreated    = $agentNow;   // the legacy rows have this equal to emRequest
                    $campaign->created_at     = $agentNow;
                    $campaign->updated_at     = $agentNow;

                    // An agent's own send (first send or a resend), marked the
                    // way the legacy data marks it: campLabel area1 = first
                    // area picked, area2 = second; free and admin_add left
                    // empty (NULL) - those are only set on admin-added areas
                    // (campLabel 'admin', admin_add 1, free 1 - adminController).
                    // camp_order is left empty too: the mailer fills it in.
                    $campaign->campLabel      = 'area' . $slot;
                    $campaign->authorized     = 0;
                    $campaign->save();

                    $createdAny = true;
                    $queued     = true;
                }

                if ($createdAny && !$trialMode) {
                    $agent->remCreds = $agent->remCreds - 1;
                    $agent->save();
                }
            } else {
                $notEnoughCredits = true;
            }
        } finally {
            if ($haveLock) {
                DB::select('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
        }
    }
}

// This handler ends with redirect()->send() + exit(), which skips the
// normal end-of-request step that saves the session - so anything flashed
// for the next page has to be saved explicitly first or it is lost.
if ($busy) {

    session()->flash('sendsetup_error', 'This request is already being processed. Please wait a moment.');
    session()->save();
    redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

} elseif ($alreadyQueued) {

    session()->flash('sendsetup_error', 'This flyer is already in the delivery queue. You can request another send once delivery has started.');
    session()->save();
    redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

} elseif ($notEnoughCredits) {

    session()->flash('sendsetup_error', 'You need at least 1 credit to request a send.');
    session()->save();
    redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

} elseif ($queued) {

    // Finished: back to the dashboard, where a modal confirms the flyer is
    // in the delivery queue and it is listed under "Waiting Delivery".
    session()->flash('delivery_queued', $flyer->xFullStreet ?: 'Your flyer');
    session()->save();
    redirect('/member/dashboard')->send();

} else {

    // No area chosen: only the subject / open house / bonus details were saved.
    session()->flash('sendsetup_status', 'Saved. Choose an area to send this flyer.');
    session()->save();
    redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

}

exit();
