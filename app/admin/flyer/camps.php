<?php

use App\Models\Core\Propdelivnow;

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
        'propflyer_id', 'propagent_id', 'emRequest', 'campLabel',
        'authorized', 'emStart', 'emComplete', 'cid', 'emArea',
        'emArea_display', 'emSubject', 'totalEmails'
    )
    ->get()
    ->map(function ($item) use ($propInfo) {
        return [
            'flyer'        => $propInfo,
            'address'      => $propInfo->xFullStreet ?? 'N/A',
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

$emailCounts = [
    'azphxmetro' => DB::connection('rememaildb')->table('azphxmetro')->count(),
    'azphxne'    => DB::connection('rememaildb')->table('azphxne')->count(),
    'azphxse'    => DB::connection('rememaildb')->table('azphxse')->count(),
    'azphxwv'    => DB::connection('rememaildb')->table('azphxwv')->count(),
    'aznaz'      => DB::connection('rememaildb')->table('aznaz')->count(),
    'azsaz'      => DB::connection('rememaildb')->table('azsaz')->count(),
];

$areaLabels = [
    'azphxmetro' => 'Phoenix Metro',
    'azphxne'    => 'Phoenix Northeast',
    'azphxse'    => 'Phoenix Southeast',
    'azphxwv'    => 'Phoenix West Valley',
    'aznaz'      => 'North Arizona',
    'azsaz'      => 'South Arizona',
];

$data = [
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