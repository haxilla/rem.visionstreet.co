<?php

use App\Models\Core\Propagent;
use Illuminate\Support\Carbon;

// Live agent search on the Agents page (called as /admin/agent/search?q=...).
//
// Matches ANY agent account - with or without a start date - so the same person
// having two accounts shows both. Searches first name, last name, full name,
// legacy username, email and the LOGIN username (xxAgtUname), and every word
// typed must match somewhere: "john smith" finds an account whose first name is
// John and last name Smith even if its full-name field is empty. An all-digit
// query also matches that exact agent id.

header('Content-Type: application/json');

$q = trim((string) request('q', ''));

if (mb_strlen($q) < 2) {
    echo json_encode(['agents' => [], 'more' => false]);
    exit;
}

$limit  = 25;
$words  = array_slice(preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY), 0, 5);
$fields = ['agtFirst', 'agtLast', 'agtFullName', 'agtUname', 'agtEmail', 'xxAgtUname'];
$isId   = ctype_digit($q);

$found = Propagent::query()
    ->select('id', 'agtFirst', 'agtLast', 'agtFullName', 'agtUname', 'agtEmail', 'xxAgtUname', 'startDate')
    ->where(function ($outer) use ($q, $words, $fields, $isId) {
        if ($isId) {
            $outer->orWhere('id', (int) $q);
        }

        $outer->orWhere(function ($all) use ($words, $fields) {
            foreach ($words as $word) {
                // % and _ typed by the admin are literal characters, not wildcards
                $like = '%' . addcslashes($word, '\\%_') . '%';

                $all->where(function ($one) use ($like, $fields) {
                    foreach ($fields as $field) {
                        $one->orWhere($field, 'like', $like);
                    }
                });
            }
        });
    })
    // an exact id match first, then alphabetical so one person's accounts sit together
    ->orderByRaw('id = ? desc', [$isId ? (int) $q : 0])
    ->orderBy('agtLast')
    ->orderBy('agtFirst')
    ->orderBy('id')
    ->limit($limit + 1)   // one extra, only to know whether there are more
    ->get();

$more = $found->count() > $limit;

$agents = $found->take($limit)->map(function ($agent) {
    $name = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

    if ($name === '') {
        $name = $agent->agtFullName
            ?: $agent->agtUname
            ?: $agent->agtEmail
            ?: 'No Name';
    }

    return [
        'id'        => $agent->id,
        'name'      => $name,
        'email'     => $agent->agtEmail,
        'username'  => $agent->xxAgtUname,
        // shown in the results so two accounts for one person can be told apart
        'startDate' => $agent->startDate ? Carbon::parse($agent->startDate)->format('m/d/Y') : null,
    ];
})->values();

echo json_encode(['agents' => $agents, 'more' => $more]);
exit;
