<?php

include app_path('queries/campaigns.php');


$emailCounts = [
    'azphxmetro' => DB::connection('rememaildb')->table('azphxmetro')->count(),
    'azphxne'    => DB::connection('rememaildb')->table('azphxne')->count(),
    'azphxse'    => DB::connection('rememaildb')->table('azphxse')->count(),
    'azphxwv'    => DB::connection('rememaildb')->table('azphxwv')->count(),
    'aznaz'      => DB::connection('rememaildb')->table('aznaz')->count(),
    'azsaz'      => DB::connection('rememaildb')->table('azsaz')->count(),
];

$data = [
    'waitingFlyerCamps'    => $waitingFlyerCamps,
    'inProgressFlyerCamps' => $inProgressFlyerCamps,
    'completeFlyerCamps'   => $completeFlyerCamps,
    'emailCounts'          => $emailCounts,
    'campaignsWaiting'     => $campaignsWaiting,
    'campaignsInProgress'  => $campaignsInProgress,
    'campaignsCompleted'   => $campaignsCompleted,
];