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
// start dates are plain dates, so many agents share one: without a tiebreaker MySQL is free to
// list those in any order (and to change it after an edit, and to repeat / skip rows between pages)
->orderBy('id', 'desc')
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

// Which of THIS page's no-start-date agents can't be deleted because they still own flyers,
// orders or campaign records (flagged in the list and skipped by the bulk delete).
$noStartBlockers = request()->has('nostart_page')
    ? \App\Support\DuplicateAccounts::blockersFor($noStartAgents->getCollection())
    : [];

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
// start dates are plain dates, so many agents share one: without a tiebreaker MySQL is free to
// list those in any order (and to change it after an edit, and to repeat / skip rows between pages)
->orderBy('id', 'desc')
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
// start dates are plain dates, so many agents share one: without a tiebreaker MySQL is free to
// list those in any order (and to change it after an edit, and to repeat / skip rows between pages)
->orderBy('id', 'desc')
->paginate(25, ['*'], 'nologo_page');

// DUPLICATE LOGINS: the same login email (xxAgtUname) on more than one account. Such agents
// have to be merged by hand - move the flyers into the account they really use, then delete
// the old one(s) - so this tab lists them with what each account holds. (MySQL compares the
// email case-insensitively, so "Bob@x.com" and "bob@x.com" are one group.)
$dupEmails = Propagent::select('xxAgtUname')
    ->whereNotNull('xxAgtUname')
    ->where('xxAgtUname', '<>', '')
    ->groupBy('xxAgtUname')
    ->havingRaw('COUNT(*) > 1')
    ->orderBy('xxAgtUname')
    ->pluck('xxAgtUname');

$dupCount  = $dupEmails->count();
$dupGroups = collect();

// The detail is only worked out when the tab is open.
if (request()->has('duplicates') && $dupCount > 0) {

    $dupAccounts = Propagent::with('theAgtOffice')
        ->whereIn('xxAgtUname', $dupEmails->all())
        ->orderBy('xxAgtUname')
        ->orderBy('id')
        ->get();

    // flyers each account holds (live ones, and all of them counting deleted ones - those
    // still hold campaign history) and when it last sent one
    $dupStats = \App\Models\Core\Propflyer::withTrashed()
        ->leftJoin('propflyerstats', 'propflyers.id', '=', 'propflyerstats.propflyer_id')
        ->whereIn('propflyers.propagent_id', $dupAccounts->pluck('id')->all())
        ->groupBy('propflyers.propagent_id')
        ->selectRaw('propflyers.propagent_id as agent_id, COUNT(*) as flyers_all, SUM(propflyers.deleted_at IS NULL) as flyers, MAX(propflyerstats.xLastDeliveryDate) as last_sent')
        ->get()
        ->keyBy('agent_id');

    // what stops an account being deleted (see DuplicateAccounts): flyers, credits, orders, campaigns
    $dupBlockers = \App\Support\DuplicateAccounts::blockersFor($dupAccounts);

    $dupGroups = $dupAccounts
        ->groupBy(fn ($a) => mb_strtolower(trim((string) $a->xxAgtUname)))
        ->map(function ($accounts) use ($dupStats, $dupBlockers) {

            $rows = $accounts->map(function ($a) use ($dupStats, $dupBlockers) {
                $s = $dupStats->get($a->id);
                $b = $dupBlockers[(int) $a->id] ?? ['flyers' => 0, 'reasons' => []];

                $name = trim(($a->agtFirst ?? '') . ' ' . ($a->agtLast ?? ''))
                    ?: ($a->agtFullName ?: 'No name');

                return [
                    'id'        => $a->id,
                    'name'      => $name,
                    'office'    => optional($a->theAgtOffice)->officeName,
                    'flyers'    => (int) ($s->flyers ?? 0),
                    'flyers_all' => (int) ($s->flyers_all ?? 0),
                    'last_sent' => $s->last_sent ?? null,
                    // the Delete button shows only when there are no flyers at all (and nothing else to lose)
                    'can_delete' => \App\Support\DuplicateAccounts::canDelete($b),
                    'reasons'   => $b['reasons'],
                    'credits'   => (int) ($a->remCreds ?? 0),
                    'start'     => $a->startDate,
                    'blocked'   => (int) ($a->loginBlocked ?? 0) === 1,
                ];
            })->values();

            // The account they actually use: the most flyers, then the latest send, then the oldest.
            $keep = $rows
                ->sortBy(fn ($r) => [-$r['flyers'], $r['last_sent'] ? -strtotime($r['last_sent']) : 0, $r['id']])
                ->first()['id'];

            $rows = $rows->map(function ($r) use ($keep) {
                $r['keep']  = $r['id'] === $keep;
                // nothing in it to lose: no flyers, no start date, no credits
                $r['empty'] = $r['flyers_all'] === 0 && !$r['start'] && $r['credits'] <= 0;
                return $r;
            });

            return [
                'email'    => $accounts->first()->xxAgtUname,
                'anchor'   => \App\Support\AgentPasswords::groupAnchor($accounts->first()->xxAgtUname),
                'accounts' => $rows,
                // flyers still sitting in the accounts that are NOT the one to keep
                'toMove'   => $rows->where('keep', false)->sum('flyers_all'),
            ];
        })
        // ALWAYS alphabetical by email. (It used to list groups with flyers to move first, so a
        // group jumped somewhere else in the list the moment you moved its flyers or deleted an
        // account. A fixed order means every group stays where you found it.)
        ->sortBy(fn ($g) => mb_strtolower($g['email']))
        ->values();
}

// 'Back to Agents' on an agent's page returns to THIS list (same tab, same page)
session(['admin_agents_list_url' => request()->fullUrl()]);

$data = [
    'noStartBlockers' => $noStartBlockers,
    'dupCount' => $dupCount,
    'dupGroups' => $dupGroups,
    'activeAgents' => $activeAgents,
    'noStartAgents' => $noStartAgents,
    'noStartCreditAgents' => $noStartCreditAgents,
    'noPhotoAgents' => $noPhotoAgents,
    'noLogoAgents' => $noLogoAgents,
    // admin Settings: does Delete ask "Delete this agent?" first
    'confirmDelete' => \App\Models\Core\AdminSetting::confirmAgentDeletion(),
];
