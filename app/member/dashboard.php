<?php

Use App\Models\Core\Propflyer;
use Illuminate\Support\Facades\Auth;


$agentID=Auth::guard('member')->id();

$propflyers = Propflyer::with('theAgent')->where('propagent_id', $agentID)->get();

dd($propflyers);