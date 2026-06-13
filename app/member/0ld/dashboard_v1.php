<?php

Use App\Models\Core\Propflyer;
Use App\Models\Core\Propdelivnow;
Use Illuminate\Support\Facades\Auth;

$agentID=Auth::guard('member')->id();

//$propflyers = Propflyer::with(['theAgent','theStats'])->where('propagent_id', $agentID)->get();
$propflyers = Propflyer::with(['theAgent','theStats'])
    ->where('propflyers.propagent_id', $agentID)
    ->join('propflyerstats', 'propflyers.id', '=', 'propflyerstats.propflyer_id')
    ->orderByDesc('propflyerstats.xLastDeliveryDate')
    ->select('propflyers.*')
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

$data=[
    'propflyers' => $propflyers,
    'propdelivs' => $propdelivs,
];