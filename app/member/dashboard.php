<?php

Use App\Models\Core\Propflyer;
Use App\Models\Core\Propdelivnow;
Use Illuminate\Support\Facades\Auth;

$agentID=Auth::guard('member')->id();

$propflyers = Propflyer::with(['theAgent','theStats'])->where('propagent_id', $agentID)->get();
$propdelivs = Propdelivnow::with([
    'theFlyer.thePhotos' => function ($query) {
        $query->where('def', 1);
    },
    'theFlyer.theStats'
])
->where('propagent_id', $agentID)
->get();

dd($propflyers, $propdelivs);