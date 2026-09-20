<?php

use App\Models\Core\AdminSetting;
use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;

include app_path('queries/campaigns.php');
include app_path('queries/flyerdetails.php');

// queries/campaigns.php's completed-campaigns query is capped to the 10
// most recently completed campaigns SITE-WIDE (for the admin dashboard's
// recent-activity widget), then filtered down per-flyer here. That means
// an older flyer's real completed campaigns can be silently missing if
// they fall outside that site-wide top-10 window. This page is about one
// specific flyer, so replace that entry with an unlimited, properly
// scoped query for just this flyer.
$completeFlyerCamps[$propInfo->id] = Propdelivnow::where('propflyer_id', $propInfo->id)
    ->whereNotNull('emComplete')
    ->orderBy('emComplete', 'desc')
    ->select(
        'propflyer_id', 'propagent_id', 'emRequest', 'campLabel', 'free', 'admin_add',
        'authorized', 'emStart', 'emComplete', 'cid', 'emArea',
        'emArea_display', 'emSubject', 'totalEmails'
    )
    ->get()
    ->map(function ($item) use ($propInfo) {
        return [
            'flyer'        => $propInfo,
            'address'      => $propInfo->xFullStreet ?? 'N/A',
            'campLabel'    => $item->campLabel,
            'admin_added'  => $item->isAdminAdded(),
            'slot'         => $item->campaignSlot(),
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

$campaignAreas = include app_path('flyers/campaignAreas.php');

$emailCounts = [];
$areaLabels = [];

foreach ($campaignAreas as $memberKey => $area) {
    $emailCounts[$area['db']] = DB::connection('rememaildb')->table($area['db'])->count();
    $areaLabels[$area['db']] = $area['label'];
}

// ---- approval screen data ----

// Everything still queued for this flyer (not started, not completed),
// approved or not, so the admin sees the full request in one place.
$pendingRequests = Propdelivnow::where('propflyer_id', $propInfo->id)
    ->whereNull('emStart')
    ->whereNull('emComplete')
    ->orderBy('emRequest')
    ->get();

// Areas already waiting OR running for this flyer - not offered again in
// the "add free area" dropdown.
$busyAreas = Propdelivnow::where('propflyer_id', $propInfo->id)
    ->whereNull('emComplete')
    ->pluck('emArea')
    ->unique()
    ->values()
    ->all();

$agent = Propagent::select('id', 'agtFullName', 'agtEmail', 'agtMainPhone', 'remCreds')
    ->find($propInfo->propagent_id);

// Send-setup details the agent entered (not part of flyerdetails.php's
// select): open houses, agent bonus and price reduction.
$sendDetails = Propflyer::select(
    'id',
    'openHouseDate1', 'openHouseTime1', 'openHouseEndTime1',
    'openHouseDate2', 'openHouseTime2', 'openHouseEndTime2',
    'agentBonusAmount', 'agentBonusComment', 'reducedAmount', 'reducedDate'
)->find($propInfo->id);

$data = [
    // the subject Quick Email will send (and Custom Email starts from) - same lookup the send uses
    'emailSubject'         => $this->currentEmailSubject($propInfo->id),
    'pendingRequests'      => $pendingRequests,
    'busyAreas'            => $busyAreas,
    'agent'                => $agent,
    'sendDetails'          => $sendDetails,
    'trialMode'            => AdminSetting::trialMode(),
    'trialEmail'           => AdminSetting::trialEmail(),
    'waitingFlyerCamps'    => $waitingFlyerCamps,
    'inProgressFlyerCamps' => $inProgressFlyerCamps,
    'completeFlyerCamps'   => $completeFlyerCamps,
    'emailCounts'          => $emailCounts,
    'areaLabels'           => $areaLabels,
    'campaignsWaiting'     => $campaignsWaiting,
    'campaignsInProgress'  => $campaignsInProgress,
    'campaignsCompleted'   => $campaignsCompleted,
    'propInfo'             => $propInfo,
];