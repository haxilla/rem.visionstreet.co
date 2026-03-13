<?php

if (!Schema::hasColumn('propflyers', 'url_slug')) {
    Schema::table('propflyers', function (Blueprint $table) {
        $table->string('url_slug', 150)->nullable()->after('flyer_code');
    });
}

<?php

try {

    dd(class_exists(\Illuminate\Support\Facades\DB::class));

} catch (\Throwable $e) {

    echo $e->getMessage();
    exit;
}

foreach ($rows as $row) {

    $street     = trim((string) ($row->xFullStreet ?? ''));
    $unitDesig  = trim((string) ($row->xUnitDesig ?? ''));
    $unitNum    = trim((string) ($row->xUnitNum ?? ''));
    $city       = trim((string) ($row->xCity ?? ''));
    $state      = trim((string) ($row->state ?? ''));
    $zip        = trim((string) ($row->xZip ?? ''));

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
        $zip,
    ]));

    $baseSlug = \Illuminate\Support\Str::slug($base);

    if ($baseSlug === '') {
        $baseSlug = (string) $row->id;
    }

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

    DB::table('propflyers')
        ->where('id', $row->id)
        ->update([
            'url_slug' => $slug,
        ]);
}