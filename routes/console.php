<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| php artisan flyers:backfill-slugs [--dry-run] [--limit=N]
|
| Gives the flyers that have NO url_slug one (Zillow style, e.g. 27043-N-117th-Pl-Scottsdale-AZ-85262),
| using the same builder new flyers use (App\Support\FlyerSlug). Only flyers with a last-sent date or an
| email request are looked at (FlyerSlug::backfillCandidates) - every other flyer is left out of the run
| completely. Flyers that already have a slug are
| never touched, so it is safe to run more than once. A flyer without a complete address (street,
| city, state, 5-digit zip) can't have one; those are counted by what is missing, with a few examples,
| so it is clear whether the data or the rule needs attention. --dry-run shows what it WOULD do and
| writes nothing. --limit=N stops after N slugs have been ASSIGNED (skipped flyers don't count), which
| makes a small first run useful even when the oldest flyers are the incomplete ones.
*/
Artisan::command('flyers:backfill-slugs {--dry-run : Show what would be done, change nothing} {--limit=0 : Stop after assigning this many slugs (0 = all)}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');

    // only flyers that have a last-sent date or an email request; the rest aren't part of the run
    $query = \App\Support\FlyerSlug::backfillCandidates()->orderBy('id');

    $total = (clone $query)->count();

    $this->info(($dryRun ? '[dry run] ' : '') . number_format($total) . ' flyer(s) with a last-sent date or an email request have no url_slug' . ($limit > 0 ? " (stopping after {$limit} are assigned)" : '') . '.');

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

                    if ($shown++ < 15) {
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

/*
| php artisan agents:backfill-slugs [--dry-run] [--limit=N]
|
| Gives agents their web address slug (first and last name joined, lowercase: debralee), using the same
| builder new agents use (App\Support\AgentSlug). Only agents who have SENT something - an email request
| or a flyer with a last-sent date - are looked at; everyone else is left out of the run. An agent who
| already has a slug is never touched, so it is safe to run more than once. --dry-run shows what it
| WOULD do (including which agents would get a number for a duplicate name, e.g. mikesmith2) and writes
| nothing. --limit=N stops after N slugs have been ASSIGNED.
|
| The agent_slug column must exist first; if it doesn't, this prints the SQL that adds it.
*/
Artisan::command('agents:backfill-slugs {--dry-run : Show what would be done, change nothing} {--limit=0 : Stop after assigning this many slugs (0 = all)}', function () {
    if (!\App\Support\AgentSlug::columnExists()) {
        $this->error('propagents has no agent_slug column yet. Run this SQL first, then this command again:');
        $this->line('');
        $this->line('  ALTER TABLE remuserdb.propagents');
        $this->line('    ADD COLUMN agent_slug VARCHAR(40) NULL,');
        $this->line('    ADD UNIQUE INDEX propagents_agent_slug_unique (agent_slug);');

        return 1;
    }

    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');

    $query = \App\Support\AgentSlug::backfillCandidates()->orderBy('id');
    $total = (clone $query)->count();

    $this->info(($dryRun ? '[dry run] ' : '') . number_format($total) . ' agent(s) who have sent something have no agent_slug' . ($limit > 0 ? " (stopping after {$limit} are assigned)" : '') . '.');

    $assigned  = 0;
    $numbered  = 0;     // got a number because the name was already taken
    $skipped   = 0;
    $shown     = 0;
    $samples   = [];
    $pending   = [];    // dry run: slugs handed out in THIS run, so duplicates within it are seen
    $processed = 0;

    $query->select('id', 'agtFirst', 'agtLast', 'agtFullName')
        ->chunkById(500, function ($agents) use ($dryRun, $limit, &$assigned, &$numbered, &$skipped, &$shown, &$samples, &$pending, &$processed) {
            foreach ($agents as $agent) {
                if ($limit > 0 && $assigned >= $limit) {
                    return false;
                }

                $base = \App\Support\AgentSlug::build($agent->getAttributes());

                if ($base === null) {
                    $skipped++;

                    if (count($samples) < 10) {
                        $samples[] = "  #{$agent->id}  first=\"{$agent->agtFirst}\"  last=\"{$agent->agtLast}\"  full=\"{$agent->agtFullName}\"";
                    }

                    continue;
                }

                if ($dryRun) {
                    $slug      = \App\Support\AgentSlug::unique($base, $agent->id, $pending);
                    $pending[] = $slug;
                } else {
                    $slug = \App\Support\AgentSlug::ensure($agent->id);

                    if ($slug === null) {
                        $skipped++;

                        continue;
                    }
                }

                $assigned++;

                if ($slug !== $base) {
                    $numbered++;
                }

                if ($shown++ < 20 || $slug !== $base && $shown < 60) {
                    $this->line("  #{$agent->id}  ->  {$slug}" . ($slug !== $base ? "   (\"{$base}\" was taken)" : ''));
                }

                if (++$processed % 500 === 0) {
                    $this->comment('  ... ' . number_format($processed) . ' done');
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would assign ' : 'Assigned ') . number_format($assigned) . ' slug(s), ' . number_format($numbered) . ' of them numbered for a duplicate name.');

    if ($skipped) {
        $this->warn(number_format($skipped) . ' agent(s) skipped - no usable name (or fewer than ' . \App\Support\AgentSlug::MIN . ' letters). Examples:');

        foreach ($samples as $sample) {
            $this->line($sample);
        }
    }
})->purpose('Give agents who have sent something their web address slug');
