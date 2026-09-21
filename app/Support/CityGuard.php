<?php

namespace App\Support;

use App\Models\Core\PostalCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Keeps misspelled cities off flyers, and puts right the ones already there.
 *
 * "Known" cities are the rows in postal_cities that have a REGION (an admin set them up). A bare row - a
 * city a flyer added, still waiting for a region - is only somebody's spelling, so it is not trusted.
 *
 *  - clean():        "Phoenix," / "PHOENIX" / "Phoenix, AZ" -> "Phoenix" (stray punctuation, capitals, a state tacked on)
 *  - suggest():      the known city in the same state that a city is probably a misspelling of ("Pheonix" -> "Phoenix")
 *  - guardFlyer():   used when a flyer's address is saved - fixes the easy cases itself, and stops once on a likely
 *                    misspelling with "did you mean ...?" (saving again keeps it as typed, so a genuinely new city works)
 *  - relabelFlyers(): the mass fix - every flyer with a wrong city + state gets the right city
 */
class CityGuard
{
    /** state => [lower-case city => city as it is spelled in the table], known cities only. */
    private static array $known = [];

    /** A city as it should be stored: no stray punctuation at either end, no ", AZ" tacked on, sensible capitals. */
    public static function clean(?string $city, ?string $state = null): string
    {
        $city = trim(preg_replace('/\s+/u', ' ', (string) $city));

        // a state written after the city: "Phoenix, AZ" / "Phoenix Arizona"
        $code = strtoupper(trim((string) $state));
        $tails = array_filter([$code, strtoupper((string) (config('usstates', [])[$code] ?? ''))]);

        for ($i = 0; $i < 3; $i++) {
            $before = $city;

            $city = preg_replace('/^[\s,.;:\/\\\\|\-–—_*#]+|[\s,.;:\/\\\\|\-–—_*#]+$/u', '', $city);

            foreach ($tails as $tail) {
                $city = preg_replace('/[\s,.]+' . preg_quote($tail, '/') . '\.?$/iu', '', $city);
            }

            if ($city === $before) {
                break;
            }
        }

        return PostalCityRegistrar::tidyCity($city);
    }

    /** Known cities for a state: lower-case city => the city as the table spells it. */
    public static function knownCities(string $state): array
    {
        $state = strtoupper(trim($state));

        if (! isset(self::$known[$state])) {
            try {
                $rows = PostalCity::where('state', $state)->whereNotNull('region')->orderBy('city')->pluck('city');
            } catch (\Throwable $e) {
                $rows = collect();
            }

            self::$known[$state] = $rows->mapWithKeys(fn ($c) => [mb_strtolower(trim($c)) => $c])->all();
        }

        return self::$known[$state];
    }

    /**
     * What a city should probably be. Null when it is fine as it is (or nothing is close enough).
     *
     *   ['city' => 'Phoenix', 'how' => 'spelling']  only punctuation / capitals were wrong - safe to fix without asking
     *   ['city' => 'Phoenix', 'how' => 'close']     a near miss - a person should look
     *
     * @return array{city:string, how:string}|null
     */
    public static function suggest(?string $city, ?string $state): ?array
    {
        $state = strtoupper(trim((string) $state));
        $raw   = trim((string) $city);
        $known = self::knownCities($state);

        if ($raw === '' || $state === '' || ! $known) {
            return null;
        }

        $clean = self::clean($raw, $state);
        $key   = mb_strtolower($clean);

        if (isset($known[$key])) {
            // the right city, maybe written differently
            return $known[$key] === $raw ? null : ['city' => $known[$key], 'how' => 'spelling'];
        }

        $best = self::closest($clean, $known);

        return $best ? ['city' => $best, 'how' => 'close'] : null;
    }

    /** The one known city this is a near miss of - or null when none is close, or two are equally close. */
    private static function closest(string $city, array $known): ?string
    {
        $a = self::squash($city);

        if (strlen($a) < 3) {
            return null;
        }

        $limit = max(1, intdiv(strlen($a), 4));
        $sound = metaphone($a);
        $best  = null;
        $score = PHP_INT_MAX;
        $tied  = false;

        foreach ($known as $canonical) {
            $b = self::squash($canonical);
            $d = levenshtein($a, $b);
            $same = $sound !== '' && $sound === metaphone($b);

            if ($d > $limit && ! ($same && $d <= 3)) {
                continue;
            }

            $s = $d * 2 - ($same ? 1 : 0);

            if ($s < $score) {
                [$best, $score, $tied] = [$canonical, $s, false];
            } elseif ($s === $score) {
                $tied = true;
            }
        }

        return $tied ? null : $best;
    }

    /** Lower-case letters only, no accents - what two spellings are compared on. */
    private static function squash(string $city): string
    {
        return preg_replace('/[^a-z]/', '', strtolower(Str::ascii($city)));
    }

    /**
     * For saving a flyer's address: the city to store. Easy fixes (punctuation, capitals, a known city typed
     * differently) are made silently. A likely misspelling stops the save ONCE with a "did you mean" message;
     * sending the same thing again keeps it as typed, so a new city that isn't in the list yet still works.
     *
     * @throws ValidationException
     */
    public static function guardFlyer(Request $request, ?string $city, ?string $state, string $field = 'xCity'): string
    {
        $clean = self::clean($city, $state);
        $state = strtoupper(trim((string) $state));

        if ($clean === '' || $state === '') {
            return $clean;
        }

        $known = self::knownCities($state);

        if (isset($known[mb_strtolower($clean)])) {
            return $known[mb_strtolower($clean)];
        }

        $suggestion = self::suggest($clean, $state);

        if (! $suggestion) {
            return $clean;
        }

        $warned = mb_strtolower($clean) . '|' . $state;

        if ($request->session()->get('city_warned') === $warned) {
            return $clean;
        }

        $request->session()->put('city_warned', $warned);

        throw ValidationException::withMessages([
            $field => 'We don\'t have "' . $clean . '" in ' . $state . ' - did you mean "' . $suggestion['city'] . '"? Change the city, or save again to keep it as typed.',
        ]);
    }

    /**
     * The mass fix: give every flyer whose city is $from (in $state) the city $to. Matches ignoring capitals
     * and spaces, and counts a flyer whose state is only written in xState (the state column is "N0"/blank)
     * or written out ("Arizona"). Deleted flyers are fixed too, so restoring one doesn't bring the mistake back.
     * Returns how many flyers changed.
     */
    public static function relabelFlyers(string $from, string $state, string $to): int
    {
        $ids = self::flyerIds($from, $state);

        foreach (array_chunk($ids, 500) as $chunk) {
            DB::table('remuserdb.propflyers')->whereIn('id', $chunk)->update(['xCity' => $to]);
        }

        return count($ids);
    }

    /** Ids of the flyers (deleted ones too) that have this city in this state. */
    public static function flyerIds(string $city, string $state): array
    {
        $state = strtoupper(trim($state));
        $names = array_unique([$state, strtoupper((string) (config('usstates', [])[$state] ?? $state))]);

        return DB::table('remuserdb.propflyers')
            ->whereRaw("UPPER(TRIM(CASE WHEN TRIM(COALESCE(state, '')) IN ('', 'N0') THEN TRIM(COALESCE(xState, '')) ELSE TRIM(state) END)) IN (" . implode(',', array_fill(0, count($names), '?')) . ')', $names)
            ->whereRaw("REPLACE(TRIM(xCity), ' ', '') = ?", [str_replace(' ', '', trim($city))])
            ->whereRaw(FlyerAd::notAdSql())
            ->pluck('id')
            ->all();
    }
}
