<?php

use App\Models\Core\Propdelivnow;
use Carbon\Carbon;

$theDate = Carbon::today()->subDays(7);

/*
|--------------------------------------------------------------------------
| Waiting Campaigns
|--------------------------------------------------------------------------
| Requested, not started, not completed
*/

$waitingCampsQuery = Propdelivnow::with([
    'theFlyer.thePhotos' => function ($query) {
        $query->where('def', 1);
    },
    'theFlyer.theAgent',
    'theFlyer.theOffice',
    'theFlyer.theMeta',
])->select(
        'propflyer_id',
        'propagent_id',
        'emRequest',
        'campLabel',
        'authorized',
        'emStart',
        'cid',
        'emArea',
        'emArea_display',
        'emSubject'
)
->whereNotNull('emRequest')
->whereNull('emStart')
->whereNull('emComplete')
->orderBy('emRequest')
->get();

$waitingCampsMap = $waitingCampsQuery->map(function ($item) {
    return [
        'flyer'         => $item->theFlyer,
        // theFlyer is null if the flyer has been (soft) deleted
        'address'       => $item->theFlyer->xFullStreet ?? 'Deleted flyer',
        'campLabel'     => $item->campLabel,
        'propflyer_id'  => $item->propflyer_id,
        'emSubject'     => $item->emSubject,
        'emArea'        => $item->emArea,
        'emStart'       => $item->emStart,
        'emRequest'     => $item->emRequest,
        'cid'           => $item->cid,
        // needed so the admin dashboard/campaign page can tell approved
        // requests from ones still waiting on approval
        'authorized'    => $item->authorized,
    ];
});

$waitingFlyerCamps = $waitingCampsMap->groupBy('propflyer_id');

/*
|--------------------------------------------------------------------------
| In Progress Campaigns
|--------------------------------------------------------------------------
| Requested, started, not completed
*/

$inProgressCampsQuery = Propdelivnow::with([
    'theFlyer.thePhotos' => function ($query) {
        $query->where('def', 1);
    },
    'theFlyer.theAgent',
    'theFlyer.theOffice',
    'theFlyer.theMeta',
])->select(
        'propflyer_id',
        'propagent_id',
        'emRequest',
        'campLabel',
        'authorized',
        'emStart',
        'cid',
        'emArea',
        'emArea_display',
        'emSubject'
)
->whereNotNull('emRequest')
->whereNotNull('emStart')
->whereNull('emComplete')
->orderBy('emRequest')
->get();

$inProgressCampsMap = $inProgressCampsQuery->map(function ($item) {
    return [
        'flyer'        => $item->theFlyer,
        'address'      => $item->theFlyer->xFullStreet ?? 'No Address',
        'campLabel'    => $item->campLabel,
        'propflyer_id' => $item->propflyer_id,
        'emSubject'    => $item->emSubject,
        'emArea'       => $item->emArea,
        'emStart'      => $item->emStart,
        'emRequest'    => $item->emRequest,
        'cid'          => $item->cid,
        'authorized'   => $item->authorized,
    ];
});

$inProgressFlyerCamps = $inProgressCampsMap->groupBy('propflyer_id');

/*
|--------------------------------------------------------------------------
| Completed Campaigns
|--------------------------------------------------------------------------
| Completed within last 7 days
*/

$completeCampsQuery = Propdelivnow::with([
    'theFlyer.thePhotos' => function ($query) {
        $query->where('def', 1);
    },
    'theFlyer.theAgent',
    'theFlyer.theOffice',
    'theFlyer.theMeta',
])->select(
        'propflyer_id',
        'propagent_id',
        'emRequest',
        'campLabel',
        'authorized',
        'emStart',
        'emComplete',
        'cid',
        'emArea',
        'emArea_display',
        'emSubject',
        'totalEmails'
)
->whereNotNull('emComplete')
->orderBy('emComplete', 'desc')
->limit(10)
->get();

$completeCampsMap = $completeCampsQuery->map(function ($item) {
    return [
        'flyer'        => $item->theFlyer,
        'address'      => $item->theFlyer->xFullStreet ?? 'N/A',
        'campLabel'    => $item->campLabel,
        'propflyer_id' => $item->propflyer_id,
        'emSubject'    => $item->emSubject,
        'emArea'       => $item->emArea,
        'emStart'      => $item->emStart,
        'emComplete'   => $item->emComplete,
        'emRequest'    => $item->emRequest,
        'cid'          => $item->cid,
        'totalEmails'  => $item->totalEmails,
    ];
});

$completeFlyerCamps = $completeCampsMap->groupBy('propflyer_id');

/*
|--------------------------------------------------------------------------
| Counts
|--------------------------------------------------------------------------
*/

$campaignsWaiting       = $waitingCampsQuery->count();
$campaignsInProgress    = $inProgressCampsQuery->count();
$campaignsCompleted     = $completeCampsQuery->count();