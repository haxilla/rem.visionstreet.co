<?php

Use App\Models\Core\Propflyer;

$propflyers = Propflyer::where('agentId', $id)->get();

dd($propflyers);