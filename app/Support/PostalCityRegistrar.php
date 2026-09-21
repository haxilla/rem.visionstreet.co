<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Makes sure a flyer's city + state is in the Areas list (postal_cities). If it isn't, it is added as a
 * bare row - city and state ONLY, no region - which is what marks it "needs review" (see AreaReview) until
 * an admin sets its region.
 *
 * Called whenever a flyer is created or its city / state changes (Propflyer's saved event), and by
 * AreaReview::syncFromFlyers() for flyers that already exist. It never throws: a problem here must not
 * stop a flyer from saving.
 */
class PostalCityRegistrar
{
    /** The flyer's city + state, added if new. */
    public static function noteFlyer($flyer): bool
    {
        return self::note($flyer->xCity ?? null, ($flyer->state ?? '') ?: ($flyer->xState ?? ''));
    }

    /**
     * A state as its two-letter code: "AZ", "az", "AZ." and "Arizona" all give "AZ". Null when it isn't one
     * of the states in config/usstates.
     */
    public static function stateCode(?string $state): ?string
    {
        $state = strtoupper(trim(rtrim(trim((string) $state), '.')));

        if ($state === '') {
            return null;
        }

        $states = config('usstates', []);

        if (isset($states[$state])) {
            return $state;
        }

        foreach ($states as $code => $name) {
            if (strcasecmp($name, $state) === 0) {
                return $code;
            }
        }

        return null;
    }

    /** Add city + state if the table doesn't have them yet. Returns true only when a new row was added. */
    public static function note(?string $city, ?string $state): bool
    {
        $city  = self::tidyCity($city);
        $state = self::stateCode($state);

        // a real US state and a city we can store
        if ($city === '' || mb_strlen($city) > 100 || $state === null) {
            return false;
        }

        try {
            // the table's own unique key (city + state, ignoring capitals) makes this a no-op when it exists
            return DB::table('remuserdb.postal_cities')->insertOrIgnore(['city' => $city, 'state' => $state]) > 0;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not note a city for the Areas list: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * A tidy city: spaces squeezed, and ALL CAPS / all lowercase turned into Title Case ("PHOENIX" ->
     * "Phoenix", "sun city west" -> "Sun City West"). A city already in mixed case is left as typed.
     */
    public static function tidyCity(?string $city): string
    {
        $city = trim(preg_replace('/\s+/', ' ', (string) $city));
        $letters = preg_replace('/[^A-Za-z]/', '', $city);

        if (strlen($letters) >= 2 && ($letters === strtoupper($letters) || $letters === strtolower($letters))) {
            $city = mb_convert_case(mb_strtolower($city), MB_CASE_TITLE, 'UTF-8');
        }

        return $city;
    }
}
