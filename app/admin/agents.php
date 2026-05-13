<?php
use App\Models\Core\Propagent;

$activeAgents = Propagent::select([
    'id',
    'agtFirst',
    'agtLast',
    'agtFullName',
    'agtUname',
    'agtEmail',
    'remCreds',
    'startDate',
    'expireDate',
])
->whereNotNull('startDate')
->orderBy('startDate', 'desc')
->paginate(25, ['*'], 'active_page');

$noStartAgents = Propagent::select([
    'id',
    'agtFirst',
    'agtLast',
    'agtFullName',
    'agtUname',
    'agtEmail',
    'remCreds',
    'startDate',
    'expireDate',
])
->whereNull('startDate')
->orderBy('id', 'desc')
->paginate(25, ['*'], 'nostart_page');

$data = [
    'activeAgents' => $activeAgents,
    'noStartAgents' => $noStartAgents,
];
