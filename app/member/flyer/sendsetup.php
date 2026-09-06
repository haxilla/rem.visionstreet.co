<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propdelivnow;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to set up sending.");
}

$flyer = Propflyer::select(
    'id', 'propagent_id', 'xFullStreet', 'xCity', 'xState', 'xZip',
    'xListPrice', 'xBeds', 'xBaths', 'xSqft',
    'openHouseDate1', 'openHouseTime1', 'openHouseEndTime1',
    'openHouseDate2', 'openHouseTime2', 'openHouseEndTime2',
    'agentBonusAmount', 'agentBonusComment', 'reducedAmount', 'reducedDate'
)
->where('id', $flyerId)
->where('propagent_id', auth()->id())
->with(['thePhotos' => function ($query) {
    $query->select('propflyer_id', 'photoName', 'photoID', 'def', 'resized')
        ->where('resized', 500)
        ->where('def', 1);
}])
->with(['theMeta' => function ($query) {
    $query->select('propflyer_id', 'zipDir', 'mlsDir');
}])
->first();

if (!$flyer) {
    dd("Error: Flyer not found or access denied.");
}

// Clear out any open house that's already happened so it stops showing
// here (and never gets re-sent in a campaign). Uses the end time when
// set, falling back to the start time, then end of day.
$openHouseChanged = false;

foreach ([1, 2] as $n) {
    $date = $flyer->{"openHouseDate$n"};

    if (!$date) {
        continue;
    }

    $endTime = $flyer->{"openHouseEndTime$n"} ?? $flyer->{"openHouseTime$n"} ?? '23:59:59';
    $endsAt  = \Carbon\Carbon::parse($date . ' ' . $endTime);

    if ($endsAt->isPast()) {
        $flyer->{"openHouseDate$n"}    = null;
        $flyer->{"openHouseTime$n"}    = null;
        $flyer->{"openHouseEndTime$n"} = null;
        $openHouseChanged = true;
    }
}

if ($openHouseChanged) {
    $flyer->save();
}

// Prefill the subject with the most recent campaign subject for this
// flyer, if one already exists (read-only lookup, same table the admin
// campaigns page already reads).
$lastSubject = Propdelivnow::where('propflyer_id', $flyer->id)
    ->orderByDesc('emRequest')
    ->value('emSubject');

$data['flyer'] = $flyer;
$data['lastSubject'] = $lastSubject;
