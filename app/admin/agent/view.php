<?php

use App\Models\Core\Propagent;
use App\Models\Core\Propflyer;
use App\Models\Core\Propdeliv;
use App\Models\Core\Propdelivnow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

$agent = Propagent::with('theAgtOffice')->findOrFail($id);
$flyerCount = Propflyer::where('propagent_id', $id)->count();

// Campaigns live in TWO tables: propdelivnow (requested / in progress, and
// finished ones are still found there) and propdelivs (the archive). This used
// to count only propdelivs, so an agent whose campaigns are in propdelivnow
// showed 0. Both are read and a campaign present in both is counted once (same
// area + request time + start time - the same rule as the agent's own campaign
// history page). "Sent" = finished; anything else is reported as still in the queue.
$realDate = function ($value) {
    if (!$value) {
        return null;
    }

    try {
        $date = Carbon::parse($value);
    } catch (\Throwable $e) {
        return null;   // legacy rows can hold odd values
    }

    return $date->year > 1970 ? $date : null;   // and zero-dates
};

$columns = ['propflyer_id', 'emArea', 'emRequest', 'emStart', 'emComplete'];

$allCampaigns = Propdelivnow::where('propagent_id', $id)->get($columns)
    ->concat(Propdeliv::where('propagent_id', $id)->get($columns))
    ->unique(fn ($c) => $c->propflyer_id . '|' . strtolower((string) $c->emArea) . '|'
        . optional($realDate($c->emRequest))->timestamp . '|' . optional($realDate($c->emStart))->timestamp);

$campaignCount    = $allCampaigns->filter(fn ($c) => $realDate($c->emComplete))->count();
$campaignsInQueue = $allCampaigns->count() - $campaignCount;   // requested or in progress, not finished
$orders = DB::table('allorders')->where('propagent_id', $id)->orderByDesc('payment_date')->get();
