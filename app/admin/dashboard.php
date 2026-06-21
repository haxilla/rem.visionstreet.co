<?php

include app_path('queries/campaigns.php');

dd(
    DB::connection('rememaildb')
        ->table('azphxmetro')
        ->count()
);

$emailCounts = [
    'azphxmetro' => DB::table('azphxmetro')->count(),
    'azphxne'    => DB::table('azphxne')->count(),
    'azphxse'    => DB::table('azphxse')->count(),
    'azphxwv'    => DB::table('azphxwv')->count(),
    'aznaz'      => DB::table('aznaz')->count(),
    'azsaz'      => DB::table('azsaz')->count(),
];

$data = [
    'waitingFlyerCamps'    => $waitingFlyerCamps,
    'inProgressFlyerCamps' => $inProgressFlyerCamps,
    'completeFlyerCamps'   => $completeFlyerCamps,

    'campaignsWaiting'     => $campaignsWaiting,
    'campaignsInProgress'  => $campaignsInProgress,
    'campaignsCompleted'   => $campaignsCompleted,
];