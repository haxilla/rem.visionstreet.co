<?php

namespace App\Support;

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
 * capitals and extra spaces. Deleted flyers are ignored.
 */
class AreaReview
{
    /**
     * Every live flyer's city + state, counted (converted to one character set / collation so it can be
     * compared with postal_cities whatever the flyers table uses).
     */
    private const FLYER_CITIES = "
        SELECT CONVERT(TRIM(f.xCity) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS city,
               CONVERT(UPPER(COALESCE(NULLIF(TRIM(f.state), ''), TRIM(f.xState))) USING utf8mb4) COLLATE utf8mb4_unicode_ci AS state,
               COUNT(*) AS flyers,
               MAX(f.id) AS last_flyer
          FROM remuserdb.propflyers f
         WHERE f.deleted_at IS NULL
           AND TRIM(f.xCity) <> ''
           AND COALESCE(NULLIF(TRIM(f.state), ''), TRIM(f.xState)) <> ''
         GROUP BY city, state";

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
              LEFT JOIN (" . self::FLYER_CITIES . ") g ON g.city = c.city AND g.state = c.state
             WHERE c.region IS NULL
             ORDER BY flyers DESC, c.city"));
    }

    /**
     * Compare every live flyer's city + state with the table and add the ones that are missing as bare
     * (region-less) rows. Returns how many were added.
     */
    public static function syncFromFlyers(): int
    {
        $missing = collect(DB::select("
            SELECT g.city, g.state
              FROM (" . self::FLYER_CITIES . ") g
              LEFT JOIN remuserdb.postal_cities c ON c.city = g.city AND c.state = g.state
             WHERE c.id IS NULL"));

        $added = 0;

        foreach ($missing as $row) {
            $added += PostalCityRegistrar::note($row->city, $row->state) ? 1 : 0;
        }

        return $added;
    }
}
