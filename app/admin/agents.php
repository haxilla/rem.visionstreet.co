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

// DUPLICATE NAMES: accounts that share a NAME (the same match the agent web address slugs use - see
// App\Support\AgentNameDuplicates). Unlike a shared login email, a shared name can be two different
// people, so each group says what points to one person (the same email or phone) and what doesn't.
// It scans every agent, so it is only worked out while the tab is open.
$nameDupCount  = \App\Support\AgentNameDuplicates::count();   // kept for a few minutes: the tab hides itself at 0
$nameDupLikely = 0;
$nameDupGroups = collect();

if (request()->has('duplicateNames')) {

    $sharedNames  = \App\Support\AgentNameDuplicates::groups();   // name key => account ids
    $nameDupCount = count($sharedNames);
    \App\Support\AgentNameDuplicates::rememberCount($nameDupCount);
    $nameIds      = collect($sharedNames)->flatten()->map(fn ($id) => (int) $id)->all();

    $nameAccounts = Propagent::with('theAgtOffice')->whereIn('id', $nameIds)->get()->keyBy('id');

    $nameStats = \App\Models\Core\Propflyer::withTrashed()
        ->leftJoin('propflyerstats', 'propflyers.id', '=', 'propflyerstats.propflyer_id')
        ->whereIn('propflyers.propagent_id', $nameIds)
        ->groupBy('propflyers.propagent_id')
        ->selectRaw('propflyers.propagent_id as agent_id, COUNT(*) as flyers_all, SUM(propflyers.deleted_at IS NULL) as flyers, MAX(propflyerstats.xLastDeliveryDate) as last_sent')
        ->get()
        ->keyBy('agent_id');

    $nameBlockers = \App\Support\DuplicateAccounts::blockersFor($nameAccounts->values());

    // emails an account is also known by (kept when duplicate accounts were merged into it earlier)
    $nameKnown = \App\Support\AgentKnownEmails::forMany($nameIds);

    // true when two or more of these values are the same (blanks never count)
    $repeats = fn ($values) => collect($values)->filter()->duplicates()->isNotEmpty();

    $nameDupGroups = collect($sharedNames)->map(function ($ids, $key) use ($nameAccounts, $nameStats, $nameBlockers, $nameKnown, $repeats) {

        $rows = collect($ids)->map(function ($id) use ($nameAccounts, $nameStats, $nameBlockers, $nameKnown) {
            $a = $nameAccounts->get($id);
            $s = $nameStats->get($id);
            $b = $nameBlockers[(int) $id] ?? ['flyers' => 0, 'reasons' => []];

            return [
                'id'         => (int) $id,
                'name'       => $a->agtFullName ?: (trim(($a->agtFirst ?? '') . ' ' . ($a->agtLast ?? '')) ?: 'No name'),
                'login'      => $a->xxAgtUname,
                'known'      => $nameKnown[(int) $id] ?? [],
                'email'      => $a->agtEmail,
                'phone'      => $a->agtMainPhone,
                'office'     => optional($a->theAgtOffice)->officeName,
                'flyers'     => (int) ($s->flyers ?? 0),
                'flyers_all' => (int) ($s->flyers_all ?? 0),
                'last_sent'  => $s->last_sent ?? null,
                'can_delete' => \App\Support\DuplicateAccounts::canDelete($b),
                'reasons'    => $b['reasons'],
                'credits'    => (int) ($a->remCreds ?? 0),
                'start'      => $a->startDate,
                'blocked'    => (int) ($a->loginBlocked ?? 0) === 1,
            ];
        })->values();

        // what points to ONE person with two accounts
        $sameEmail  = $repeats($rows->flatMap(fn ($r) => array_unique(array_map(fn ($e) => mb_strtolower(trim((string) $e)), array_merge([$r['login'], $r['email']], $r['known'])))));
        $samePhone  = $repeats($rows->map(fn ($r) => strlen($d = preg_replace('/\D/', '', (string) $r['phone'])) >= 7 ? substr($d, -10) : null));
        $sameOffice = $repeats($rows->map(fn ($r) => mb_strtolower(trim((string) $r['office']))));

        // the account they actually use: the most flyers, then the latest send, then the oldest
        $keep = $rows
            ->sortBy(fn ($r) => [-$r['flyers'], $r['last_sent'] ? -strtotime($r['last_sent']) : 0, $r['id']])
            ->first()['id'];

        $rows = $rows->map(function ($r) use ($keep) {
            $r['keep']  = $r['id'] === $keep;
            $r['empty'] = $r['flyers_all'] === 0 && !$r['start'] && $r['credits'] <= 0;

            return $r;
        });

        return [
            'key'        => $key,
            'anchor'     => \App\Support\AgentNameDuplicates::anchor($key),
            'name'       => $rows->first()['name'],
            'accounts'   => $rows,
            'sameEmail'  => $sameEmail,
            'samePhone'  => $samePhone,
            'sameOffice' => $sameOffice,
            'likely'     => $sameEmail || $samePhone,
            'toMove'     => $rows->where('keep', false)->sum('flyers_all'),
        ];
    });

    $nameDupLikely = $nameDupGroups->where('likely', true)->count();

    // ?likely=1 shows only the groups that look like one person twice
    if (request()->boolean('likely')) {
        $nameDupGroups = $nameDupGroups->where('likely', true);
    }

    // likely-one-person first (those are the ones to merge), then alphabetical - fixed, so a group stays where you left it
    $nameDupGroups = $nameDupGroups
        ->sortBy(fn ($g) => [$g['likely'] ? 0 : 1, mb_strtolower($g['name'])])
        ->values();
}

// 'Back to Agents' on an agent's page returns to THIS list (same tab, same page)
session(['admin_agents_list_url' => request()->fullUrl()]);

$data = [
    'noStartBlockers' => $noStartBlockers,
    'dupCount' => $dupCount,
    'dupGroups' => $dupGroups,
    'nameDupCount'  => $nameDupCount,
    'nameDupLikely' => $nameDupLikely,
    'nameDupGroups' => $nameDupGroups,
    'activeAgents' => $activeAgents,
    'noStartAgents' => $noStartAgents,
    'noStartCreditAgents' => $noStartCreditAgents,
    'noPhotoAgents' => $noPhotoAgents,
    'noLogoAgents' => $noLogoAgents,
    // admin Settings: does Delete ask "Delete this agent?" first
    'confirmDelete' => \App\Models\Core\AdminSetting::confirmAgentDeletion(),
];
