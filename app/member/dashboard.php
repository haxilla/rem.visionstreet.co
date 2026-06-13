<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propdelivnow;
use Illuminate\Support\Facades\Auth;

$agentID = Auth::guard('member')->id();

$propflyers = Propflyer::with([
        'theAgent',
        'theStats',
        'thePhotos' => function ($query) {
            $query->where('def', 1);
        },
        'theMeta'
    ])
    ->where('propagent_id', $agentID)
    ->get();

$propdelivs = Propdelivnow::with([
        'theFlyer.thePhotos' => function ($query) {
            $query->where('def', 1);
        },
        'theFlyer.theStats',
        'theFlyer.theMeta'
    ])
    ->where('propagent_id', $agentID)
    ->get();

$data = [
    'propflyers' => $propflyers,
    'propdelivs' => $propdelivs,
];