<?php

use App\Models\Core\Propagent;

$agent = Propagent::with([
    'theAgentMeta',
    'theAgentCleanup',
    'theAgtOffice',
])->first();

dd($agent);