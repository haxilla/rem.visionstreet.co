<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Create url_slug column if missing
|--------------------------------------------------------------------------
*/

if (!Schema::hasColumn('propflyers', 'url_slug')) {

    Schema::table('propflyers', function (Blueprint $table) {
        $table->string('url_slug', 150)->nullable()->after('flyer_code');
    });

}


/*
|--------------------------------------------------------------------------
| Backfill url_slug where missing
|--------------------------------------------------------------------------
*/

$rows = DB::table('propflyers')
    ->whereNull('url_slug')
    ->get();


foreach ($rows as $row) {

    $street     = trim((string) ($row->xFullStreet ?? ''));
    $unitDesig  = trim((string) ($row->xUnitDesig ?? ''));
    $unitNum    = trim((string) ($row->xUnitNum ?? ''));
    $city       = trim((string) ($row->xCity ?? ''));
    $state      = trim((string) ($row->state ?? ''));
    $zip        = trim((string) ($row->xZip ?? ''));


    /*
    |--------------------------------------------------------------------------
    | Build unit text
    |--------------------------------------------------------------------------
    */

    $unit = trim($unitDesig . ' ' . $unitNum);


    /*
    |--------------------------------------------------------------------------
    | Append unit only if not already inside street
    |--------------------------------------------------------------------------
    */

    $streetWithUnit = $street;

    if ($unit !== '') {

        $streetLower = Str::lower($street);
        $unitLower   = Str::lower($unit);

        if (!Str::contains($streetLower, $unitLower)) {
            $streetWithUnit = trim($street . ' ' . $unit);
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Build base string
    |--------------------------------------------------------------------------
    */

    $base = implode(' ', array_filter([
        $streetWithUnit,
        $city,
        $state,
        $zip,
    ]));


    $baseSlug = Str::slug($base);


    /*
    |--------------------------------------------------------------------------
    | Fallback if blank
    |--------------------------------------------------------------------------
    */

    if ($baseSlug === '') {
        $baseSlug = (string) $row->id;
    }


    /*
    |--------------------------------------------------------------------------
    | Ensure unique slug
    |--------------------------------------------------------------------------
    */

    $slug = $baseSlug;
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


    /*
    |--------------------------------------------------------------------------
    | Save slug
    |--------------------------------------------------------------------------
    */

    DB::table('propflyers')
        ->where('id', $row->id)
        ->update([
            'url_slug' => $slug,
        ]);

}
