<?php

Use App\Models\Core\Propflyer;
Use App\Models\Core\Propdelivnow;
Use Illuminate\Support\Facades\Auth;

$agentID=Auth::guard('member')->id();

$propflyers = Propflyer::with('theAgent')->where('propagent_id', $agentID)->get();
$propdelivs = Propdelivnow::with('theFlyer.thePhotos')->where('propagent_id', $agentID)->get();

dd($propflyers, $propdelivs);