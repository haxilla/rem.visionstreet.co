<?php

use App\Models\Core\Propagent;

$agents = Propagent::select([
    'id',
    'agtFirst',
    'agtLast',
    'remCreds',
    'startDate',
    'expireDate',
])
->orderBy('agtLast')
->orderBy('agtFirst')
->paginate(25);

$data = [
    'agents' => $agents,
];

$agents=Propagent::limit(25)->get();

dd($agents);