<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propdelivnow;

$validatedData = $request->validate([

    'flyerId'           => 'required|integer',
    'emSubject'         => 'nullable|string|max:255',
    'areas'             => 'nullable|array|max:2',
    'areas.*'           => 'string|in:phoenix_metro,northeast_valley,southeast_valley,west_valley,northern_az,southern_az',
    'openHouseDate1'    => 'nullable|date',
    'openHouseTime1'    => 'nullable|date_format:H:i',
    'openHouseEndTime1' => 'nullable|date_format:H:i',
    'openHouseDate2'    => 'nullable|date',
    'openHouseTime2'    => 'nullable|date_format:H:i',
    'openHouseEndTime2' => 'nullable|date_format:H:i',
    'agentBonusAmount'  => 'nullable|string|max:255',
    'agentBonusComment' => 'nullable|string|max:255',
    'reducedAmount'     => 'nullable|integer|min:0',
    'reducedDate'       => 'nullable|date',

]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->openHouseDate1    = $validatedData['openHouseDate1'] ?? null;
$flyer->openHouseTime1    = $validatedData['openHouseTime1'] ?? null;
$flyer->openHouseEndTime1 = $validatedData['openHouseEndTime1'] ?? null;
$flyer->openHouseDate2    = $validatedData['openHouseDate2'] ?? null;
$flyer->openHouseTime2    = $validatedData['openHouseTime2'] ?? null;
$flyer->openHouseEndTime2 = $validatedData['openHouseEndTime2'] ?? null;
$flyer->agentBonusAmount  = $validatedData['agentBonusAmount'] ?? null;
$flyer->agentBonusComment = $validatedData['agentBonusComment'] ?? null;

// Manual override - lets an agent set this directly when the flyer was
// created after the reduction already happened, so there's no earlier
// price on record for save_details.php's auto-calc to compare against.
$flyer->reducedAmount = $validatedData['reducedAmount'] ?? null;
$flyer->reducedDate   = $validatedData['reducedDate'] ?? null;

$flyer->save();

// Turn selected areas into pending (unauthorized) campaign requests -
// one propdelivnow row per area, picked up later by the admin approval
// queue and the separate mail-sending system. Costs 1 credit for the
// whole submission (covers up to 2 areas), charged only if this
// submission actually creates at least one new request - re-saving an
// area that's already pending just refreshes its subject, free.
$agent = auth()->user();
$selectedAreas = $validatedData['areas'] ?? [];

if (!empty($selectedAreas) && ($agent->remCreds ?? 0) >= 1) {
    $campaignAreaMap = include app_path('flyers/campaignAreas.php');
    $createdAny = false;

    foreach ($selectedAreas as $memberAreaKey) {
        $areaInfo = $campaignAreaMap[$memberAreaKey] ?? null;

        if (!$areaInfo) {
            continue;
        }

        $existing = Propdelivnow::where('propflyer_id', $flyer->id)
            ->where('emArea', $areaInfo['db'])
            ->whereNull('emStart')
            ->whereNull('emComplete')
            ->first();

        if ($existing) {
            $existing->emSubject = $validatedData['emSubject'] ?? null;
            $existing->save();
            continue;
        }

        $campaign = new Propdelivnow();
        $campaign->propflyer_id   = $flyer->id;
        $campaign->propagent_id   = $flyer->propagent_id;
        $campaign->emArea         = $areaInfo['db'];
        $campaign->emArea_display = $areaInfo['label'];
        $campaign->emSubject      = $validatedData['emSubject'] ?? null;
        $campaign->totalEmails    = DB::connection('rememaildb')->table($areaInfo['db'])->count();
        $campaign->emRequest      = now();
        $campaign->authorized     = 0;
        $campaign->save();

        $createdAny = true;
    }

    if ($createdAny) {
        $agent->remCreds = $agent->remCreds - 1;
        $agent->save();
    }
}

redirect('/member/flyer/sendsetup?flyerId=' . $flyer->id)->send();

exit();
