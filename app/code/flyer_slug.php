<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

set_time_limit(0);
ini_set('max_execution_time', 0);
ignore_user_abort(true);

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
    ->get();

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

    $slug = $baseSlug;

    /* unique check - if the slug already exists for another flyer, 
    append a number until we find a unique slug 
    $i = 1;

    while (
        DB::table('propflyers')
            ->where('url_slug', $slug)
            ->where('id', '!=', $row->id)
            ->exists()
    ) {
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
    */

    DB::table('propflyers')
        ->where('id', $row->id)
        ->update([
            'url_slug' => $slug
        ]);
}