<?php

namespace App\Support;

use App\Models\Core\PostalCity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Cities that still need to be set up in the Areas list (postal_cities).
 *
 * The rule is simple: a row with NO REGION is "needs review". A city that a flyer uses but the table
 * doesn't have is added as a bare row - city + state only - so it shows up as needing review until an
 * admin chooses its region (that is the minimum needed to clear the flag).
 *
 * Two things add those bare rows:
 *  - PostalCityRegistrar, whenever a flyer is created or its city / state changes;
 *  - syncFromFlyers(), which compares every live flyer's city + state with the table and adds the ones
 *    that are missing (for flyers that already existed, or came from somewhere else).
 *
 * City + state are always compared TOGETHER (Phoenix, AZ and Phoenix, TX are different), ignoring
 * capitals and extra spaces. A state may be stored as "AZ" or written out ("Arizona"); both mean AZ.
 * Deleted flyers are ignored.
 */
class AreaReview
{
    /**
     * Every live flyer's city + state as stored (only grouped, counted). The output names are deliberately
     * NOT the names of columns in the flyers table (MySQL would group by the column instead of the alias).
     *
     * The state is flyers.state, except that the old clean-up script wrote "N0" there for every flyer whose
     * xState it couldn't read - "N0" means "no state", so the raw xState is used instead when there is one.
     */
    private const FLYER_CITIES = "
        SELECT CONVERT(TRIM(f.xCity) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS pair_city,
               CONVERT(UPPER(CASE WHEN TRIM(COALESCE(f.state, '')) IN ('', 'N0') THEN TRIM(COALESCE(f.xState, '')) ELSE TRIM(f.state) END) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS pair_state,
               COUNT(*) AS flyers,
               MAX(f.id) AS last_flyer
          FROM remuserdb.propflyers f
         WHERE f.deleted_at IS NULL
           AND TRIM(COALESCE(f.xCity, '')) <> ''
         GROUP BY pair_city, pair_state";

    /** How many rows have no region yet - the number shown beside Areas. Never breaks a page: 0 if it can't be worked out. */
    public static function pendingCount(): int
    {
        // the menu asks several times per page: work it out once per request
        static $count = null;

        if ($count !== null) {
            return $count;
        }

        try {
            return $count = (int) DB::table('remuserdb.postal_cities')->whereNull('region')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * The rows with no region, with how many live flyers use each and the newest of them - most flyers first.
     *
     * @return Collection<int, object{id:int, city:string, state:string, flyers:int, last_flyer:?int}>
     */
    public static function pending(): Collection
    {
        return collect(DB::select("
            SELECT c.id, c.city, c.state, COALESCE(g.flyers, 0) AS flyers, g.last_flyer
              FROM remuserdb.postal_cities c
              LEFT JOIN (" . self::FLYER_CITIES . ") g ON g.pair_city = c.city AND g.pair_state = c.state
             WHERE c.region IS NULL
             ORDER BY flyers DESC, c.city"));
    }

    /**
     * Compare every live flyer's city + state with the table and add the missing ones as bare
     * (region-less) rows. Returns exactly what it looked at, so a "found nothing" can be trusted (or not):
     *
     *   flyers      live flyers with a city
     *   pairs       different city + state combinations among them
     *   known       pairs already in the table
     *   added       pairs that were missing and have now been added
     *   unusable    pairs that could not be added, with why (no usable state, city too long) and, for a missing
     *               state, the state the city is probably in (when only one state has it in the table)
     *   states      how the flyers' states are stored, most common first (state => flyers)
     *
     * @return array{flyers:int, pairs:int, known:int, added:int, unusable:array<int, array{city:string, state:string, flyers:int, why:string}>, states:array<string,int>}
     */
    public static function syncFromFlyers(): array
    {
        $pairs = collect(DB::select(self::FLYER_CITIES));

        $known  = [];
        $byCity = [];   // city => the states that have it in the table (to guess a missing state)

        foreach (PostalCity::query()->get(['city', 'state']) as $row) {
            $known[self::key($row->city, $row->state)] = true;
            $byCity[mb_strtolower(trim($row->city))][$row->state] = true;
        }

        $result = ['flyers' => (int) $pairs->sum('flyers'), 'pairs' => $pairs->count(), 'known' => 0, 'added' => 0, 'unusable' => [], 'states' => []];

        foreach ($pairs as $pair) {
            $state = PostalCityRegistrar::stateCode($pair->pair_state);
            $city  = PostalCityRegistrar::tidyCity($pair->pair_city);

            // "Mexico" in the city box is the country, not a city (unless a US state has one): never listed, never added
            if (PostalCityRegistrar::isCountryName($city, $state)) {
                continue;
            }

            $shown = $state ?? (trim((string) $pair->pair_state) === '' ? '(blank)' : trim((string) $pair->pair_state));
            $result['states'][$shown] = ($result['states'][$shown] ?? 0) + (int) $pair->flyers;

            if ($state === null) {
                // no usable state: if only ONE state has this city in the table, that is the likely answer
                $states = array_keys($byCity[mb_strtolower($city)] ?? []);

                $result['unusable'][] = [
                    'city'   => $city,
                    'state'  => $shown,
                    'flyers' => (int) $pair->flyers,
                    'why'    => 'the flyer has no usable state',
                    'guess'  => count($states) === 1 ? $states[0] : null,
                ];
                continue;
            }

            if ($city === '' || mb_strlen($city) > 100) {
                $result['unusable'][] = ['city' => $city, 'state' => $state, 'flyers' => (int) $pair->flyers, 'why' => 'the city is empty or too long'];
                continue;
            }

            if (isset($known[self::key($city, $state)])) {
                $result['known']++;
                continue;
            }

            if (PostalCityRegistrar::note($city, $state)) {
                $result['added']++;
                $known[self::key($city, $state)] = true;
            } else {
                $result['unusable'][] = ['city' => $city, 'state' => $state, 'flyers' => (int) $pair->flyers, 'why' => 'it could not be saved'];
            }
        }

        arsort($result['states']);

        // the biggest problems first
        usort($result['unusable'], fn ($x, $y) => $y['flyers'] <=> $x['flyers']);

        return $result;
    }

    /** One key for "this city in this state", ignoring capitals and spacing. */
    private static function key(?string $city, ?string $state): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $city))) . '|' . strtoupper(trim((string) $state));
    }
}
