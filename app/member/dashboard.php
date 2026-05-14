<?php

Use App\Models\Core\Propflyer;

$agentID=auth::guard('member')->id();

$propflyers = Propflyer::where('agentId', $agentID)->get();

dd($propflyers);