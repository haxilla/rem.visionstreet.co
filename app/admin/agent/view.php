<?php

use App\Models\Core\Propagent;
use App\Models\Core\Propflyer;
use App\Support\AgentCampaigns;
use Illuminate\Support\Facades\DB;

$agent = Propagent::with('theAgtOffice')->findOrFail($id);
$flyerCount = Propflyer::where('propagent_id', $id)->count();

// Campaigns live in TWO tables (propdelivnow and the propdelivs archive);
// AgentCampaigns reads both and counts a campaign present in both once. The
// same class feeds the campaign list this count links to, so they always agree.
// "Sent" = finished; anything else is reported as still in the queue.
$allCampaigns     = AgentCampaigns::forAgent((int) $id);
$campaignCount    = $allCampaigns->where('status', 'completed')->count();
$campaignsInQueue = $allCampaigns->count() - $campaignCount;   // requested or in progress, not finished

$orders = DB::table('allorders')->where('propagent_id', $id)->orderByDesc('payment_date')->get();

// Other accounts that use the same login email (the same email was registered more than
// once). They get their password together and pick an account at sign-in.
$sameEmailAccounts = \App\Support\AgentPasswords::accountsForEmail($agent->xxAgtUname)
    ->where('id', '!=', $agent->id)
    ->values();
