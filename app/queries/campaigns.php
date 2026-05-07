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

$waitingCampsQuery = propdelivnow::with('theFlyer')
    ->select(
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
        'campLabel'    => $item->campLabel,
        'propflyer_id' => $item->propflyer_id,
        'emSubject'    => $item->emSubject,
        'emArea'       => $item->emArea,
        'emStart'      => $item->emStart,
        'emRequest'    => $item->emRequest,
        'cid'          => $item->cid,
    ];
});

$waitingFlyerCamps = $waitingCampsMap->groupBy('propflyer_id');

/*
|--------------------------------------------------------------------------
| In Progress Campaigns
|--------------------------------------------------------------------------
| Requested, started, not completed
*/

$inProgressCampsQuery = propdelivnow::with('theFlyer')
    ->select(
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
        'campLabel'    => $item->campLabel,
        'propflyer_id' => $item->propflyer_id,
        'emSubject'    => $item->emSubject,
        'emArea'       => $item->emArea,
        'emStart'      => $item->emStart,
        'emRequest'    => $item->emRequest,
        'cid'          => $item->cid,
    ];
});

$inProgressFlyerCamps = $inProgressCampsMap->groupBy('propflyer_id');

/*
|--------------------------------------------------------------------------
| Completed Campaigns
|--------------------------------------------------------------------------
| Completed within last 7 days
*/

$completeCampsQuery = Propdelivnow::with('theFlyer')
    ->select(
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
        'emSubject'
)
->whereNotNull('emComplete')
->orderBy('emComplete', 'desc')
->limit(10)
->get();

$completeCampsMap = $completeCampsQuery->map(function ($item) {
    return [
        'campLabel'    => $item->campLabel,
        'propflyer_id' => $item->propflyer_id,
        'emSubject'    => $item->emSubject,
        'emArea'       => $item->emArea,
        'emStart'      => $item->emStart,
        'emComplete'   => $item->emComplete,
        'emRequest'    => $item->emRequest,
        'cid'          => $item->cid,
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

dd($campaignsWaiting, $campaignsInProgress, $campaignsCompleted);