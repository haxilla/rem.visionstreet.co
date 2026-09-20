<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| php artisan flyers:backfill-slugs [--dry-run] [--limit=N]
|
| Gives every flyer that has NO url_slug one (Zillow style, e.g. 27043-N-117th-Pl-Scottsdale-AZ-85262),
| using the same builder new flyers use (App\Support\FlyerSlug). Flyers that already have a slug are
| never touched, so it is safe to run more than once. A flyer without a complete address (street,
| city, state, 5-digit zip) can't have one and is listed instead. --dry-run shows what it WOULD do
| and writes nothing.
*/
Artisan::command('flyers:backfill-slugs {--dry-run : Show what would be done, change nothing} {--limit=0 : Stop after this many flyers (0 = all)}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');

    $query = \App\Models\Core\Propflyer::withTrashed()
        ->where(function ($q) {
            $q->whereNull('url_slug')->orWhere('url_slug', '');
        })
        ->orderBy('id');

    $total = (clone $query)->count();

    $this->info(($dryRun ? '[dry run] ' : '') . "{$total} flyer(s) have no url_slug" . ($limit > 0 ? " (stopping after {$limit})" : '') . '.');

    $assigned = 0;
    $skipped  = [];
    $shown    = 0;

    $query->select('id', 'xFullStreet', 'xCity', 'xState', 'state', 'xZip', 'xxZip')
        ->chunkById(500, function ($flyers) use ($dryRun, $limit, &$assigned, &$skipped, &$shown) {
            foreach ($flyers as $flyer) {
                if ($limit > 0 && ($assigned + count($skipped)) >= $limit) {
                    return false;
                }

                if ($dryRun) {
                    $slug = \App\Support\FlyerSlug::build($flyer->getAttributes());

                    if ($slug === null) {
                        $skipped[] = $flyer->id;
                        continue;
                    }

                    $assigned++;

                    if ($shown++ < 15) {
                        $this->line("  #{$flyer->id}  ->  {$slug}");
                    }

                    continue;
                }

                if (\App\Support\FlyerSlug::ensure($flyer->id)) {
                    $assigned++;
                } else {
                    $skipped[] = $flyer->id;
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would assign ' : 'Assigned ') . number_format($assigned) . ' slug(s).');

    if ($skipped) {
        $this->warn(number_format(count($skipped)) . ' flyer(s) skipped - the address is missing its street, city, state or 5-digit zip. First ids: '
            . implode(', ', array_slice($skipped, 0, 25)) . (count($skipped) > 25 ? ' ...' : ''));
    }
})->purpose('Give every flyer without a url_slug its Zillow-style slug');
