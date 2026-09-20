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
// no credits: none recorded, or none left
->where(function ($query) {
    $query->whereNull('remCreds')->orWhere('remCreds', '<=', 0);
})
->orderBy('id', 'desc')
->paginate(25, ['*'], 'nostart_page');

// No start date, but they DO have credits (a balance above zero).
$noStartCreditAgents = Propagent::select([
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
->where('remCreds', '>', 0)
->orderBy('remCreds', 'desc')
->orderBy('id', 'desc')
->paginate(25, ['*'], 'nostartcredits_page');

$data = [
    'activeAgents' => $activeAgents,
    'noStartAgents' => $noStartAgents,
    'noStartCreditAgents' => $noStartCreditAgents,
    // admin Settings: does Delete ask "Delete this agent?" first
    'confirmDelete' => \App\Models\Core\AdminSetting::confirmAgentDeletion(),
];
