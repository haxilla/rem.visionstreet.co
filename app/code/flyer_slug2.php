<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

$batchSize = 25;

if (!Schema::hasColumn('propflyers', 'url_slug')) {
    Schema::table('propflyers', function (Blueprint $table) {
        $table->string('url_slug', 150)->nullable()->after('flyer_code');
    });
}

$rows = DB::table('propflyers')
    ->select([
        'id',
        'xFullStreet',
        'xUnitDesig',
        'xUnitNum',
        'xCity',
        'state',
        'xxZip'
    ])
    ->whereNull('url_slug')
    ->whereNotNull('xFullStreet')
    ->where('xFullStreet', '!=', '')
    ->orderBy('id')
    ->limit($batchSize)
    ->get();

if ($rows->isEmpty()) {

    echo '<!doctype html><html><body style="font-family:Arial;padding:20px">';
    echo '<h3>url_slug backfill complete</h3>';
    echo '</body></html>';

    exit;
}

$lastId = null;

foreach ($rows as $row) {

    $street    = trim((string) ($row->xFullStreet ?? ''));
    $unitDesig = trim((string) ($row->xUnitDesig ?? ''));
    $unitNum   = trim((string) ($row->xUnitNum ?? ''));
    $city      = trim((string) ($row->xCity ?? ''));
    $state     = trim((string) ($row->state ?? ''));
    $zip       = trim((string) ($row->xxZip ?? ''));

    $unit = trim($unitDesig . ' ' . $unitNum);

    $streetWithUnit = $street;

    if ($unit !== '') {

        $streetLower = strtolower($street);
        $unitLower   = strtolower($unit);

        if (strpos($streetLower, $unitLower) === false) {
            $streetWithUnit = trim($street . ' ' . $unit);
        }
    }

    $base = implode(' ', array_filter([
        $streetWithUnit,
        $city,
        $state,
        $zip
    ]));

    $baseSlug = Str::slug($base);

    if ($baseSlug === '') {
        $baseSlug = (string) $row->id;
    }

    DB::table('propflyers')
        ->where('id', $row->id)
        ->update([
            'url_slug' => $baseSlug
        ]);

    $lastId = $row->id;
}

$remaining = DB::table('propflyers')
    ->whereNull('url_slug')
    ->count();

echo '<!doctype html>';
echo '<html><head>';
echo '<meta http-equiv="refresh" content="0.5">';
echo '</head><body style="font-family:Arial;padding:20px">';

echo '<h3>Batch complete</h3>';
echo '<p>Last ID: ' . $lastId . '</p>';
echo '<p>Processed: ' . count($rows) . '</p>';
echo '<p>Remaining: ' . $remaining . '</p>';

echo '</body></html>';

exit;