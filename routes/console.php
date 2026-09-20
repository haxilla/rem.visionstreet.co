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
| city, state, 5-digit zip) can't have one; those are counted by what is missing, with a few examples,
| so it is clear whether the data or the rule needs attention. --dry-run shows what it WOULD do and
| writes nothing. --limit=N stops after N slugs have been ASSIGNED (skipped flyers don't count), which
| makes a small first run useful even when the oldest flyers are the incomplete ones.
*/
Artisan::command('flyers:backfill-slugs {--dry-run : Show what would be done, change nothing} {--limit=0 : Stop after assigning this many slugs (0 = all)}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');

    $query = \App\Models\Core\Propflyer::withTrashed()
        ->where(function ($q) {
            $q->whereNull('url_slug')->orWhere('url_slug', '');
        })
        ->orderBy('id');

    $total = (clone $query)->count();

    $this->info(($dryRun ? '[dry run] ' : '') . number_format($total) . ' flyer(s) have no url_slug' . ($limit > 0 ? " (stopping after {$limit} are assigned)" : '') . '.');

    $assigned = 0;
    $skipped  = 0;
    $shown    = 0;
    $reasons  = ['street' => 0, 'city' => 0, 'state' => 0, 'zip' => 0];   // a flyer can lack several
    $samples  = [];

    $query->select('id', 'xFullStreet', 'xCity', 'xState', 'state', 'xZip', 'xxZip')
        ->chunkById(500, function ($flyers) use ($dryRun, $limit, &$assigned, &$skipped, &$shown, &$reasons, &$samples) {
            foreach ($flyers as $flyer) {
                if ($limit > 0 && $assigned >= $limit) {
                    return false;
                }

                $slug = $dryRun
                    ? \App\Support\FlyerSlug::build($flyer->getAttributes())
                    : \App\Support\FlyerSlug::ensure($flyer->id);

                if ($slug !== null) {
                    $assigned++;

                    if ($dryRun && $shown++ < 15) {
                        $this->line("  #{$flyer->id}  ->  {$slug}");
                    }

                    continue;
                }

                $skipped++;

                $missing = \App\Support\FlyerSlug::missing($flyer->getAttributes());

                foreach ($missing as $part) {
                    $reasons[$part]++;
                }

                if (count($samples) < 8) {
                    $samples[] = "  #{$flyer->id}  street=\"{$flyer->xFullStreet}\"  city=\"{$flyer->xCity}\"  state=\"{$flyer->state}\" / xState=\"{$flyer->xState}\"  zip=\"{$flyer->xZip}\" / xxZip=\"{$flyer->xxZip}\"  -> missing: " . implode(', ', $missing);
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would assign ' : 'Assigned ') . number_format($assigned) . ' slug(s).');

    if ($skipped) {
        $this->warn(number_format($skipped) . ' flyer(s) skipped - the address is missing something the slug needs.');
        $this->line('  Missing (a flyer can lack more than one):  street ' . number_format($reasons['street'])
            . ',  city ' . number_format($reasons['city'])
            . ',  state ' . number_format($reasons['state'])
            . ',  zip ' . number_format($reasons['zip']));
        $this->line('  Examples:');

        foreach ($samples as $sample) {
            $this->line($sample);
        }
    }
})->purpose('Give every flyer without a url_slug its Zillow-style slug');
