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
| php artisan agents:backfill-slugs [--dry-run] [--limit=N] [--all]
|
| Gives agents their web address slug (first and last name as one PascalCase word: DebraLee), using the
| same builder new agents use (App\Support\AgentSlug). By default only agents who have SENT something -
| an email request or a flyer with a last-sent date - are looked at; --all takes every agent that has no
| slug. An agent who already has a slug is never touched, so it is safe to run more than once.
| --dry-run shows what it WOULD do (including which agents would get a number for a duplicate name,
| e.g. MikeSmith2) and writes nothing. --limit=N stops after N slugs have been ASSIGNED.
|
| Run agents:tidy-names first so an ALL-CAPS name doesn't become the slug's spelling, and the
| agent_slug column must exist (the old agtURL renamed - see the SQL this prints when it doesn't).
*/
Artisan::command('agents:backfill-slugs {--dry-run : Show what would be done, change nothing} {--limit=0 : Stop after assigning this many slugs (0 = all)} {--all : Every agent without a slug, not only those who have sent something} {--duplicates : List every name shared by more than one agent, on screen}', function () {
    if (!\App\Support\AgentSlug::columnExists()) {
        $this->error('propagents has no agent_slug column yet. Run this SQL first (it renames the old agtURL, which never worked, and clears it), then this command again:');
        $this->line('');
        $this->line('  UPDATE remuserdb.propagents SET agtURL = NULL;');
        $this->line('  ALTER TABLE remuserdb.propagents');
        $this->line('    CHANGE COLUMN agtURL agent_slug VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL;');

        return 1;
    }

    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');
    $all    = (bool) $this->option('all');
    $duplicates = (bool) $this->option('duplicates');

    $query = \App\Support\AgentSlug::backfillCandidates($all)->orderBy('id');
    $total = (clone $query)->count();

    $this->info(($dryRun ? '[dry run] ' : '') . number_format($total) . ' agent(s) ' . ($all ? '' : 'who have sent something ') . 'have no slug' . ($limit > 0 ? " (stopping after {$limit} are assigned)" : '') . '.');

    $assigned  = 0;
    $numbered  = 0;     // got a number because the name was already taken
    $skipped   = 0;
    $shown     = 0;
    $samples   = [];
    $pending   = [];    // dry run: slugs handed out in THIS run, so duplicates within it are seen
    $groups    = [];    // base name => the agents that share it (id, slug, name, email, phone)
    $processed = 0;

    $query->select('id', 'agtFirst', 'agtLast', 'agtFullName', 'agtEmail', 'xxAgtUname', 'agtMainPhone')
        ->chunkById(500, function ($agents) use ($dryRun, $limit, &$assigned, &$numbered, &$skipped, &$shown, &$samples, &$pending, &$processed, &$groups) {
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

                // everyone who shares this name, first (lowest id) to last, for --duplicates
                $groups[strtolower($base)][] = [
                    'id'    => $agent->id,
                    'slug'  => $slug,
                    'name'  => $agent->agtFullName ?: trim($agent->agtFirst . ' ' . $agent->agtLast),
                    'email' => trim((string) ($agent->agtEmail ?: $agent->xxAgtUname)),
                    'phone' => trim((string) $agent->agtMainPhone),
                ];

                if ($shown++ < 20) {
                    $this->line("  #{$agent->id}  ->  {$slug}" . ($slug !== $base ? "   (\"{$base}\" was taken)" : ''));
                }

                if (++$processed % 500 === 0) {
                    $this->comment('  ... ' . number_format($processed) . ' done');
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would assign ' : 'Assigned ') . number_format($assigned) . ' slug(s), ' . number_format($numbered) . ' of them numbered for a duplicate name.');

    // names shared by more than one agent
    $shared = array_filter($groups, fn ($members) => count($members) > 1);

    // a group where two agents have the same email is almost certainly one person with two accounts
    $sameEmail = array_filter($shared, function ($members) {
        $emails = array_filter(array_map(fn ($m) => strtolower($m['email']), $members));

        return count($emails) !== count(array_unique($emails));
    });

    // those groups first (they are the ones to merge), then the biggest
    uksort($shared, function ($a, $b) use ($shared, $sameEmail) {
        return [isset($sameEmail[$b]), count($shared[$b])] <=> [isset($sameEmail[$a]), count($shared[$a])];
    });

    if ($shared) {
        $this->comment(number_format(count($shared)) . ' name(s) are shared by more than one agent ('
            . number_format(count($sameEmail)) . ' of them include two accounts with the SAME email - likely one person twice).'
            . ($duplicates ? '' : ' Run again with --duplicates to list them.'));
    }

    if ($duplicates && $shared) {
        foreach ($shared as $base => $members) {
            $same = isset($sameEmail[$base]);

            $this->newLine();
            $this->line('<options=bold>' . $members[0]['slug'] . '</>  (' . count($members) . ' agents' . ($same ? ' - SAME EMAIL on at least two, likely one person' : '') . ')');

            foreach ($members as $member) {
                $this->line(sprintf('    #%-6d %-24s %-28s %-34s %s', $member['id'], $member['slug'], mb_substr($member['name'], 0, 28), mb_substr($member['email'], 0, 34), $member['phone']));
            }
        }

        $this->newLine();
    }

    if ($skipped) {
        $this->warn(number_format($skipped) . ' agent(s) skipped - no usable name (or fewer than ' . \App\Support\AgentSlug::MIN . ' letters). Examples:');

        foreach ($samples as $sample) {
            $this->line($sample);
        }
    }
})->purpose('Give agents who have sent something their web address slug');

/*
| php artisan agents:tidy-names [--dry-run] [--limit=N]
|
| Puts ALL-CAPS and all-lowercase agent names in proper form (MARY BEANS -> Mary Beans, MCKENNA -> McKenna,
| O'MALLEY -> O'Malley, SMITH-JONES -> Smith-Jones), so the flyers, the emails and the agent's web address
| all spell the name right. It changes CAPITALS ONLY - never the letters - and only fields that are entirely
| capitals or entirely lowercase: agtFirst, agtLast and agtFullName are each looked at on their own, and a
| name already in mixed case (McKenna, MaryAnn, "Mary Ann Beans") is left exactly as it is. --dry-run shows
| what it WOULD change and writes nothing. Run it before agents:backfill-slugs. The same tidying is applied
| whenever an agent saves their name (AgentProfile::saveContact).
*/
Artisan::command('agents:tidy-names {--dry-run : Show what would be changed, change nothing} {--limit=0 : Stop after changing this many agents (0 = all)}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $limit  = (int) $this->option('limit');

    $changed = 0;
    $fields  = ['agtFirst' => 0, 'agtLast' => 0, 'agtFullName' => 0];
    $shown   = 0;

    \App\Models\Core\Propagent::query()
        ->select('id', 'agtFirst', 'agtLast', 'agtFullName')
        ->chunkById(1000, function ($agents) use ($dryRun, $limit, &$changed, &$fields, &$shown) {
            foreach ($agents as $agent) {
                if ($limit > 0 && $changed >= $limit) {
                    return false;
                }

                $update = [];

                foreach (array_keys($fields) as $field) {
                    $value = (string) $agent->{$field};

                    if ($value !== '' && \App\Support\AgentNames::needsTidy($value)) {
                        $tidy = \App\Support\AgentNames::tidy($value);

                        if ($tidy !== $value) {
                            $update[$field] = $tidy;
                        }
                    }
                }

                if (!$update) {
                    continue;
                }

                $changed++;

                foreach (array_keys($update) as $field) {
                    $fields[$field]++;
                }

                if ($shown++ < 40) {
                    $this->line("  #{$agent->id}  " . implode('   |   ', array_map(
                        fn ($field) => "{$field}: \"{$agent->{$field}}\" -> \"{$update[$field]}\"",
                        array_keys($update)
                    )));
                }

                if (!$dryRun) {
                    // straight to the table: no updated_at change, no model events
                    \App\Models\Core\Propagent::whereKey($agent->id)->toBase()->update($update);
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would change ' : 'Changed ') . number_format($changed) . ' agent(s): '
        . number_format($fields['agtFirst']) . ' first names, '
        . number_format($fields['agtLast']) . ' last names, '
        . number_format($fields['agtFullName']) . ' full names.');
})->purpose('Put ALL-CAPS and all-lowercase agent names in proper capitals');

/*
| php artisan agents:fix-initials [--dry-run]
|
| A one-off repair. The first run of agents:tidy-names turned INITIALS into ordinary words (AJ -> Aj,
| DJ KHAMIS -> Dj Khamis). This puts two-letter words that look like initials (AJ, DJ, TJ, KC - see
| AgentNames::looksLikeInitials) back to capitals in agtFirst, agtLast and agtFullName. It touches
| ONLY those words, and only ones in the form "Xx". --dry-run shows what it would change first.
*/
Artisan::command('agents:fix-initials {--dry-run : Show what would be changed, change nothing}', function () {
    $dryRun  = (bool) $this->option('dry-run');
    $changed = 0;
    $shown   = 0;

    \App\Models\Core\Propagent::query()
        ->select('id', 'agtFirst', 'agtLast', 'agtFullName')
        ->chunkById(1000, function ($agents) use ($dryRun, &$changed, &$shown) {
            foreach ($agents as $agent) {
                $update = [];

                foreach (['agtFirst', 'agtLast', 'agtFullName'] as $field) {
                    $value = (string) $agent->{$field};

                    if ($value === '') {
                        continue;
                    }

                    $fixed = \App\Support\AgentNames::restoreInitials($value);

                    if ($fixed !== $value) {
                        $update[$field] = $fixed;
                    }
                }

                if (!$update) {
                    continue;
                }

                $changed++;

                if ($shown++ < 80) {
                    $this->line("  #{$agent->id}  " . implode('   |   ', array_map(
                        fn ($field) => "{$field}: \"{$agent->{$field}}\" -> \"{$update[$field]}\"",
                        array_keys($update)
                    )));
                }

                if (!$dryRun) {
                    // straight to the table: no updated_at change, no model events
                    \App\Models\Core\Propagent::whereKey($agent->id)->toBase()->update($update);
                }
            }
        });

    $this->newLine();
    $this->info(($dryRun ? 'Would fix ' : 'Fixed ') . number_format($changed) . ' agent(s).');
})->purpose('Put initials (AJ, DJ) that agents:tidy-names lowercased back to capitals');
