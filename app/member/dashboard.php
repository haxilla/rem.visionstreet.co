<?php

Use App\Models\Core\Propflyer;
use Illuminate\Support\Facades\Auth;


$agentID=Auth::guard('member')->id();

$propflyers = Propflyer::where('agentId', $agentID)->get();

dd($propflyers);