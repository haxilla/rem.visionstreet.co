<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * A flyer can be marked as an AD (propflyers.is_ad = 1): an advertisement rather than a property, so it has no
 * real city to place in the Areas list. Ads are ignored by the Areas tools (the flyer check, Needs review, the
 * mass fix, "No state") and never add a city to postal_cities.
 *
 * The column is added with SQL, so until that has been run everything here behaves as if no flyer is an ad.
 */
class FlyerAd
{
    /** Is the is_ad column there yet? Worked out once per request. */
    public static function exists(): bool
    {
        static $exists = null;

        if ($exists === null) {
            try {
                $exists = Schema::hasColumn('remuserdb.propflyers', 'is_ad');
            } catch (\Throwable $e) {
                $exists = false;
            }
        }

        return $exists;
    }

    /** How many live flyers are marked as ads - for the tab. 0 when the column isn't there yet. */
    public static function count(): int
    {
        static $count = null;

        if ($count === null) {
            try {
                $count = self::exists() ? (int) \Illuminate\Support\Facades\DB::table('remuserdb.propflyers')->whereNull('deleted_at')->where('is_ad', 1)->count() : 0;
            } catch (\Throwable $e) {
                $count = 0;
            }
        }

        return $count;
    }

    /** SQL condition "this flyer is not an ad" for a flyer table alias ('' for none) - always true before the column exists. */
    public static function notAdSql(string $alias = ''): string
    {
        if (! self::exists()) {
            return '1 = 1';
        }

        return 'COALESCE(' . ($alias !== '' ? $alias . '.' : '') . 'is_ad, 0) = 0';
    }
}
