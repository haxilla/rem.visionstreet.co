<?php

use App\Models\Core\Propagent;

$agents = Propagent::select([
    'id',
    'agtFullName',
    'remCreds',
    'startDate',
    'expireDate',
])
->orderBy('agtFullName')
->paginate(25);

$data = [
    'agents' => $agents,
];