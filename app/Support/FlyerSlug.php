<?php

namespace App\Support;

use App\Models\Core\Propflyer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * A flyer's URL slug (propflyers.url_slug), the last part of its public page:
 *
 *     https://rem.visionstreet.co/homedetails/27043-N-117th-Pl-Scottsdale-AZ-85262
 *
 * The same pattern Zillow uses: street, city, state and zip joined by hyphens, letters
 * keeping their capitals. A slug is created ONCE, as soon as the flyer has all four parts
 * (a new flyer has them from the first wizard step), and is never changed by this class
 * afterwards - links already sent in emails must keep working when someone later fixes a
 * typo in the address. Two flyers for the same address get -2, -3 ... so every slug is unique.
 *
 * One place builds slugs (the flyer model calls ensure() on every save, and the backfill
 * command and script call it too), so new flyers and backfilled ones always match.
 */
class FlyerSlug
{
    /** Columns a slug is built from (the unit ones only exist on flyers imported from the old system). */
    private const COLUMNS = ['xFullStreet', 'xCity', 'xState', 'state', 'xZip', 'xxZip'];

    /**
     * Give the flyer a slug if it has none and its address is complete. Returns the slug the
     * flyer has afterwards, or null when it has none (address incomplete). Never throws: a
     * slug problem must not stop a flyer from saving.
     */
    public static function ensure($flyerId): ?string
    {
        try {
            $flyer = Propflyer::withTrashed()->find($flyerId, self::columns());

            if (!$flyer) {
                return null;
            }

            if (filled($flyer->url_slug)) {
                return $flyer->url_slug;
            }

            $slug = self::build($flyer->getAttributes());

            if ($slug === null) {
                return null;
            }

            $slug = self::unique($slug, $flyer->getKey());

            // straight to the table: no updated_at change, no model events
            Propflyer::withTrashed()->whereKey($flyer->getKey())->toBase()->update(['url_slug' => $slug]);

            return $slug;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * The flyers a BACKFILL looks at: no slug yet AND either a real last-sent date
     * (propflyerstats.xLastDeliveryDate) or at least one email request (emRequest in
     * propdelivnow or the propdelivs archive). Every other flyer - blank drafts and flyers
     * that never went anywhere - is left out of the run entirely, not even counted. (New flyers
     * are unaffected: Propflyer's saved hook gives any flyer a slug once its address is complete.)
     * Legacy zero-dates count as none.
     */
    public static function backfillCandidates()
    {
        $since = '1971-01-01';

        return Propflyer::withTrashed()
            ->where(function ($query) {
                $query->whereNull('url_slug')->orWhere('url_slug', '');
            })
            ->where(function ($query) use ($since) {
                $query->whereIn('id', \App\Models\Core\Propflyerstat::query()
                        ->select('propflyer_id')
                        ->whereNotNull('xLastDeliveryDate')
                        ->where('xLastDeliveryDate', '>=', $since))
                    ->orWhereIn('id', \App\Models\Core\Propdelivnow::query()
                        ->select('propflyer_id')
                        ->whereNotNull('emRequest')
                        ->where('emRequest', '>=', $since))
                    ->orWhereIn('id', \App\Models\Core\Propdeliv::query()
                        ->select('propflyer_id')
                        ->whereNotNull('emRequest')
                        ->where('emRequest', '>=', $since));
            });
    }

    /** The four parts of an address as the slug will use them (each '' when missing or unusable). */
    private static function parts(array $parts): array
    {
        $zip = preg_replace('/\D/', '', (string) (($parts['xZip'] ?? '') ?: ($parts['xxZip'] ?? '')));

        return [
            'street' => self::text($parts['xFullStreet'] ?? ''),
            'city'   => self::text($parts['xCity'] ?? ''),
            'state'  => self::stateCode($parts['state'] ?? '') ?: self::stateCode($parts['xState'] ?? ''),
            'zip'    => strlen($zip) >= 5 ? substr($zip, 0, 5) : '',
        ];
    }

    /** Which of 'street', 'city', 'state', 'zip' this address is missing (or has an unusable value for). */
    public static function missing(array $parts): array
    {
        return array_keys(array_filter(self::parts($parts), fn ($value) => $value === ''));
    }

    /** What the slug would be for these address parts (an array with the column names as keys), or null if incomplete. */
    public static function build(array $parts): ?string
    {
        ['street' => $street, 'city' => $city, 'state' => $state, 'zip' => $zip] = self::parts($parts);

        if (self::missing($parts)) {
            return null;
        }

        // a unit kept in its own columns (imported flyers) that the street line doesn't already carry
        $unit = self::text(trim(($parts['xUnitDesig'] ?? '') . ' ' . ($parts['xUnitNum'] ?? '')));

        if ($unit !== '') {
            $unitNumber = self::text($parts['xUnitNum'] ?? '');

            if ($unitNumber === '' || stripos($street, $unitNumber) === false) {
                $street .= ' ' . $unit;
            }
        }

        $slug = preg_replace('/[^A-Za-z0-9]+/', '-', self::tidyCase($street) . ' ' . self::tidyCase($city) . ' ' . $state . ' ' . $zip);

        return trim($slug, '-') ?: null;
    }

    /** Slug, or slug-2, slug-3 ... - the first not already used by ANOTHER flyer (deleted ones count too). */
    public static function unique(string $slug, $exceptFlyerId = null): string
    {
        $candidate = $slug;

        for ($n = 2; $n < 100; $n++) {
            $taken = Propflyer::withTrashed()
                ->where('url_slug', $candidate)
                ->when($exceptFlyerId, fn ($query) => $query->whereKeyNot($exceptFlyerId))
                ->exists();

            if (!$taken) {
                return $candidate;
            }

            $candidate = $slug . '-' . $n;
        }

        return $slug . '-' . ($exceptFlyerId ?: uniqid());
    }

    /** The columns to read (the optional unit ones only when the table has them). */
    private static function columns(): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = array_merge(['id', 'url_slug'], self::COLUMNS);

            $flyerModel = new Propflyer();

            foreach (['xUnitDesig', 'xUnitNum'] as $unitColumn) {
                if (Schema::connection($flyerModel->getConnectionName())->hasColumn($flyerModel->getTable(), $unitColumn)) {
                    $columns[] = $unitColumn;
                }
            }
        }

        return $columns;
    }

    /** Plain ASCII, apostrophes dropped (O'Connor -> OConnor), spaces squeezed. */
    private static function text($value): string
    {
        $value = Str::ascii((string) $value);
        $value = str_replace(["'", '`'], '', $value);

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * "27043 N 117TH PL" and "27043 n 117th pl" both become "27043 N 117th Pl"; anything the agent
     * typed in mixed case is left as typed. Compass abbreviations (NE, NW, SE, SW) stay capitals.
     */
    private static function tidyCase(string $text): string
    {
        if ($text === strtoupper($text) || $text === strtolower($text)) {
            $text = ucwords(strtolower($text));
            $text = preg_replace_callback('/\b(Ne|Nw|Se|Sw)\b/', fn ($m) => strtoupper($m[1]), $text);
        }

        return $text;
    }

    /** "AZ", "az" and "Arizona" all give "AZ"; anything that isn't a state (a stray "N0", say) gives ''. */
    private static function stateCode($state): string
    {
        $state = trim((string) $state);
        $states = config('usstates', []);

        if (strlen($state) === 2) {
            return isset($states[strtoupper($state)]) ? strtoupper($state) : '';
        }

        foreach ($states as $code => $name) {
            if (strcasecmp($name, $state) === 0) {
                return $code;
            }
        }

        return '';
    }
}
