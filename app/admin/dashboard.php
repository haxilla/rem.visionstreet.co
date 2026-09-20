<?php

/*
|--------------------------------------------------------------------------
| Admin dashboard: campaigns grouped by flyer
|--------------------------------------------------------------------------
| One entry per flyer per stage (a flyer's requested areas are its campaign
| rows in propdelivnow), each carrying its areas for the dropdown plus what an
| admin wants to know before acting: who the agent is and their credits, when
| it was requested, how many contacts, whether it is free/admin-added, the
| flyer's next open house, bonus / price reduction, and when it last sent.
|
| Stages (same rules the dashboard always had):
|   waiting     - requested, request time has passed, not started
|                 (split by whether every area is authorized yet)
|   in progress - started, not finished
|   completed   - finished; the 10 most recently finished flyers
*/

use App\Models\Core\Propagent;
use App\Models\Core\Propdelivnow;
use App\Models\Core\Propflyer;
use App\Models\Core\Propflyerstat;
use Illuminate\Support\Carbon;

// Readable area names, and each area's CURRENT contact count (used when a campaign
// row never recorded its own total).
$areaLabels  = [];
$emailCounts = [];

foreach (include app_path('flyers/campaignAreas.php') as $area) {
    $key = strtolower($area['db']);

    $areaLabels[$key]  = $area['label'];
    $emailCounts[$key] = DB::connection('rememaildb')->table($area['db'])->count();
}

// Real dates only: legacy rows can hold NULL, a zero date or odd values.
$parseDate = function ($value) {
    if (!$value || str_starts_with((string) $value, '0000')) {
        return null;
    }

    try {
        $date = Carbon::parse($value);
    } catch (\Throwable $e) {
        return null;
    }

    return $date->year > 1970 ? $date : null;
};

$columns = [
    'cid', 'propflyer_id', 'propagent_id', 'emRequest', 'emStart', 'emComplete',
    'campLabel', 'free', 'admin_add', 'authorized',
    'emArea', 'emArea_display', 'emSubject', 'totalEmails',
];

$toRow = function ($c) use ($parseDate, $areaLabels, $emailCounts) {
    $areaKey   = strtolower(trim((string) $c->emArea));
    $completed = $parseDate($c->emComplete);

    // the count recorded on the campaign; failing that (waiting / running only) the area's size today
    $contacts = ($c->totalEmails !== null && $c->totalEmails !== '')
        ? (int) $c->totalEmails
        : ($completed ? null : ($emailCounts[$areaKey] ?? null));

    return [
        'cid'         => $c->cid,
        'flyer_id'    => (int) $c->propflyer_id,
        'agent_id'    => (int) $c->propagent_id,
        'area'        => $c->emArea_display ?: ($areaLabels[$areaKey] ?? ($c->emArea ?: 'Unknown area')),
        'contacts'    => $contacts,
        'subject'     => $c->emSubject,
        'authorized'  => (int) $c->authorized === 1,
        'admin_added' => $c->isAdminAdded(),
        'requested'   => $parseDate($c->emRequest),
        'started'     => $parseDate($c->emStart),
        'completed'   => $completed,
    ];
};

$now = Carbon::now();

// ---- the campaign rows, by stage ----

$unfinished = Propdelivnow::select($columns)
    ->whereNotNull('emRequest')
    ->whereNull('emComplete')
    ->orderBy('emRequest')
    ->get()
    ->map($toRow);

$waitingRows = $unfinished->filter(fn ($r) => !$r['started'] && $r['requested'] && $r['requested']->lte($now))->values();
$progressRows = $unfinished->filter(fn ($r) => (bool) $r['started'])->values();

// a flyer's areas often finish together, so read plenty of rows to get 10 flyers
$completedRows = Propdelivnow::select($columns)
    ->whereNotNull('emComplete')
    ->orderByDesc('emComplete')
    ->limit(80)
    ->get()
    ->map($toRow)
    ->filter(fn ($r) => (bool) $r['completed'])
    ->values();

// ---- everything else the cards show, fetched once for all the flyers involved ----

$allRows   = $waitingRows->concat($progressRows)->concat($completedRows);
$flyerIds  = $allRows->pluck('flyer_id')->unique()->values()->all();
$agentIds  = $allRows->pluck('agent_id')->unique()->values()->all();

// withTrashed: a flyer the agent deleted still has campaigns in the queue / history
$flyers = Propflyer::withTrashed()
    ->with([
        'thePhotos' => fn ($query) => $query->where('def', 1),
        'theMeta',
    ])
    ->whereIn('id', $flyerIds)
    ->get([
        'id', 'xFullStreet', 'xCity', 'state', 'xZip', 'deleted_at',
        'openHouseDate1', 'openHouseDate2', 'agentBonusAmount', 'reducedAmount',
    ])
    ->keyBy('id');

$agents = Propagent::whereIn('id', $agentIds)->get(['id', 'agtFullName', 'remCreds'])->keyBy('id');

$lastSent = Propflyerstat::whereIn('propflyer_id', $flyerIds)->pluck('xLastDeliveryDate', 'propflyer_id');

$today = Carbon::today();

$groupByFlyer = function ($rows) use ($flyers, $agents, $lastSent, $parseDate, $today) {
    return $rows->groupBy('flyer_id')->map(function ($items, $flyerId) use ($flyers, $agents, $lastSent, $parseDate, $today) {
        $flyer = $flyers->get($flyerId);
        $agent = $agents->get($items->first()['agent_id']);

        // thumbnail: the flyer's default photo - the 500px copy when there is one
        $photo = $flyer ? ($flyer->thePhotos->firstWhere('resized', 500) ?? $flyer->thePhotos->first()) : null;
        $meta  = $flyer?->theMeta;

        $thumb = ($photo && $meta && $meta->zipDir && $meta->mlsDir && $photo->photoName)
            ? "/hqphotos/{$meta->zipDir}/{$meta->mlsDir}/{$photo->photoName}"
            : null;

        // the next open house that hasn't happened yet
        $openHouse = collect([$flyer?->openHouseDate1, $flyer?->openHouseDate2])
            ->map($parseDate)
            ->filter(fn ($d) => $d && $d->gte($today))
            ->sort()
            ->first();

        $subjects = $items->pluck('subject')->filter()->unique()->values();
        $last     = $parseDate($lastSent->get($flyerId));

        return [
            'flyer_id'      => (int) $flyerId,
            'address'       => $flyer ? ($flyer->xFullStreet ?: 'Untitled flyer') : 'Flyer no longer exists',
            'place'         => $flyer ? trim(($flyer->xCity ?? '') . ' ' . ($flyer->state ?? '') . ' ' . ($flyer->xZip ?? '')) : '',
            'deleted'       => !$flyer || $flyer->trashed(),
            'thumb'         => $thumb,
            'agent_id'      => $agent?->id,
            'agent_name'    => $agent?->agtFullName ?: 'Unknown agent',
            'credits'       => $agent ? (int) $agent->remCreds : null,
            'subject'       => $subjects->first(),
            'subjects_vary' => $subjects->count() > 1,
            'requested'     => $items->pluck('requested')->filter()->sort()->first(),
            'started'       => $items->pluck('started')->filter()->sort()->first(),
            'completed'     => $items->pluck('completed')->filter()->sortDesc()->first(),
            'areas'         => $items->sortBy('area')->values(),
            'contacts'      => (int) $items->sum(fn ($r) => (int) $r['contacts']),
            'authorized'    => $items->where('authorized', true)->count(),
            'admin_added'   => $items->contains('admin_added', true),
            'open_house'    => $openHouse,
            'bonus'         => $flyer && !empty($flyer->agentBonusAmount),
            'reduced'       => $flyer && !empty($flyer->reducedAmount),
            'last_sent'     => $last,
        ];
    })->values();
};

// waiting: oldest request first; a flyer with ANY area still to authorize is in the
// "Unauthorized" list (that's where the admin has work to do)
$waiting = $groupByFlyer($waitingRows)->sortBy(fn ($g) => optional($g['requested'])->timestamp)->values();

$data = [
    'waitingUnauthorized' => $waiting->filter(fn ($g) => $g['authorized'] < $g['areas']->count())->values(),
    'waitingAuthorized'   => $waiting->filter(fn ($g) => $g['authorized'] >= $g['areas']->count())->values(),
    'inProgress'          => $groupByFlyer($progressRows)->sortBy(fn ($g) => optional($g['started'])->timestamp)->values(),
    'completed'           => $groupByFlyer($completedRows)->sortByDesc(fn ($g) => optional($g['completed'])->timestamp)->take(10)->values(),
];
