<?php

/*
|--------------------------------------------------------------------------
| url_slug backfill (browser version)
|--------------------------------------------------------------------------
| Gives every flyer with no url_slug its Zillow-style slug (27043-N-117th-Pl-Scottsdale-AZ-85262)
| through App\Support\FlyerSlug - the SAME builder new flyers use (Propflyer's saved hook), so
| backfilled and new flyers always match. Flyers that already have a slug are never touched.
|
| Run it by un-commenting `require app_path('code/flyer_slug.php');` in guestController::index()
| and opening the home page: it does 25 flyers, reloads itself for the next 25 and says
| "url_slug backfill complete" at the end - then COMMENT THE LINE OUT AGAIN (while it is on, the
| home page shows this progress page instead of the site).
|
| The command line does the same job faster and without touching the home page:
|     php artisan flyers:backfill-slugs --dry-run
|     php artisan flyers:backfill-slugs
*/

use App\Models\Core\Propflyer;
use App\Support\FlyerSlug;
use Illuminate\Support\Facades\Schema;

$batchSize = 25;

$flyerTable = (new Propflyer())->getTable();

if (!Schema::hasColumn($flyerTable, 'url_slug')) {
    echo '<!doctype html><html><body style="font-family:Arial;padding:20px">';
    echo '<h3>The propflyers table has no url_slug column yet.</h3>';
    echo '</body></html>';

    exit;
}

// where the previous batch stopped, and running totals (carried in the address)
$after    = (int) request('after', 0);
$assigned = (int) request('assigned', 0);
$skipped  = (int) request('skipped', 0);

$rows = Propflyer::withTrashed()
    ->where('id', '>', $after)
    ->where(function ($query) {
        $query->whereNull('url_slug')->orWhere('url_slug', '');
    })
    ->orderBy('id')
    ->limit($batchSize)
    ->get(['id']);

if ($rows->isEmpty()) {

    echo '<!doctype html><html><body style="font-family:Arial;padding:20px">';
    echo '<h3>url_slug backfill complete</h3>';
    echo '<p>Assigned: ' . $assigned . '</p>';
    echo '<p>Skipped (address missing its street, city, state or 5-digit zip): ' . $skipped . '</p>';
    echo '<p>Now comment the require line out of guestController::index() again.</p>';
    echo '</body></html>';

    exit;
}

$lastId = $after;

foreach ($rows as $row) {

    if (FlyerSlug::ensure($row->id)) {
        $assigned++;
    } else {
        $skipped++;
    }

    $lastId = $row->id;
}

$next = url('/') . '?after=' . $lastId . '&assigned=' . $assigned . '&skipped=' . $skipped;

echo '<!doctype html>';
echo '<html><head>';
echo '<meta http-equiv="refresh" content="0.5;url=' . e($next) . '">';
echo '</head><body style="font-family:Arial;padding:20px">';

echo '<h3>Batch complete</h3>';
echo '<p>Last ID: ' . $lastId . '</p>';
echo '<p>Assigned so far: ' . $assigned . '</p>';
echo '<p>Skipped so far: ' . $skipped . '</p>';

echo '</body></html>';

exit;
