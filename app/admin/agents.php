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

// Agents WITH a start date (the active ones, whose flyers matter) that have no
// photo / no logo on file. An empty value means none, as elsewhere in the app.
$noPhotoAgents = Propagent::select([
    'id',
    'agtFirst',
    'agtLast',
    'agtFullName',
    'agtUname',
    'agtEmail',
    'remCreds',
    'startDate',
])
->whereNotNull('startDate')
->where(function ($query) {
    $query->whereNull('agtPhoto')->orWhere('agtPhoto', '');
})
->orderBy('startDate', 'desc')
->paginate(25, ['*'], 'nophoto_page');

$noLogoAgents = Propagent::select([
    'id',
    'agtFirst',
    'agtLast',
    'agtFullName',
    'agtUname',
    'agtEmail',
    'remCreds',
    'startDate',
])
->whereNotNull('startDate')
->where(function ($query) {
    $query->whereNull('agtLogo')->orWhere('agtLogo', '');
})
->orderBy('startDate', 'desc')
->paginate(25, ['*'], 'nologo_page');

$data = [
    'activeAgents' => $activeAgents,
    'noStartAgents' => $noStartAgents,
    'noStartCreditAgents' => $noStartCreditAgents,
    'noPhotoAgents' => $noPhotoAgents,
    'noLogoAgents' => $noLogoAgents,
    // admin Settings: does Delete ask "Delete this agent?" first
    'confirmDelete' => \App\Models\Core\AdminSetting::confirmAgentDeletion(),
];
