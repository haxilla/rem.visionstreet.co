<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Everything the system records about an agent's activity is recorded in
 * THAT AGENT'S timezone, worked out from the state they live in
 * (propagents.agtState) - not the server's or the admin's.
 *
 * Two ways in:
 *   AgentTime::apply($agent)  - makes now(), Eloquent's created_at/updated_at
 *                               and every other "current time" for the rest of
 *                               this request use the agent's timezone.
 *   AgentTime::now($agent)    - the current wall-clock time in the agent's
 *                               timezone as 'Y-m-d H:i:s', for a value that is
 *                               stored explicitly (e.g. a campaign's emRequest).
 *
 * The values are stored as plain wall-clock times ("3:18 PM where the agent
 * is"), which is how the legacy data is stored too.
 */
class AgentTime
{
    /** Used when an agent has no state, or one that isn't recognised. */
    public const DEFAULT_TIMEZONE = 'America/Phoenix';

    // One zone per state (its predominant one; a few states span two).
    private const STATE_TIMEZONES = [
        // Eastern
        'CT' => 'America/New_York', 'DE' => 'America/New_York', 'DC' => 'America/New_York',
        'FL' => 'America/New_York', 'GA' => 'America/New_York', 'IN' => 'America/New_York',
        'KY' => 'America/New_York', 'ME' => 'America/New_York', 'MD' => 'America/New_York',
        'MA' => 'America/New_York', 'MI' => 'America/New_York', 'NH' => 'America/New_York',
        'NJ' => 'America/New_York', 'NY' => 'America/New_York', 'NC' => 'America/New_York',
        'OH' => 'America/New_York', 'PA' => 'America/New_York', 'RI' => 'America/New_York',
        'SC' => 'America/New_York', 'VT' => 'America/New_York', 'VA' => 'America/New_York',
        'WV' => 'America/New_York',

        // Central
        'AL' => 'America/Chicago', 'AR' => 'America/Chicago', 'IL' => 'America/Chicago',
        'IA' => 'America/Chicago', 'KS' => 'America/Chicago', 'LA' => 'America/Chicago',
        'MN' => 'America/Chicago', 'MS' => 'America/Chicago', 'MO' => 'America/Chicago',
        'NE' => 'America/Chicago', 'ND' => 'America/Chicago', 'OK' => 'America/Chicago',
        'SD' => 'America/Chicago', 'TN' => 'America/Chicago', 'TX' => 'America/Chicago',
        'WI' => 'America/Chicago',

        // Mountain
        'CO' => 'America/Denver', 'ID' => 'America/Denver', 'MT' => 'America/Denver',
        'NM' => 'America/Denver', 'UT' => 'America/Denver', 'WY' => 'America/Denver',

        // Arizona: Mountain time, but no daylight saving
        'AZ' => 'America/Phoenix',

        // Pacific
        'CA' => 'America/Los_Angeles', 'NV' => 'America/Los_Angeles',
        'OR' => 'America/Los_Angeles', 'WA' => 'America/Los_Angeles',

        'AK' => 'America/Anchorage',
        'HI' => 'Pacific/Honolulu',
    ];

    /** IANA timezone name for an agent (a propagents row / model), or the default. */
    public static function timezoneFor($agent): string
    {
        $state = static::stateCode($agent->agtState ?? null);

        return self::STATE_TIMEZONES[$state] ?? self::DEFAULT_TIMEZONE;
    }

    /** Current wall-clock time in the agent's timezone, as 'Y-m-d H:i:s'. */
    public static function now($agent): string
    {
        return Carbon::now(static::timezoneFor($agent))->format('Y-m-d H:i:s');
    }

    /**
     * Use the agent's timezone for the rest of this request: now(), Carbon,
     * and the created_at / updated_at Laravel stamps on every save.
     */
    public static function apply($agent): void
    {
        $timezone = static::timezoneFor($agent);

        date_default_timezone_set($timezone);
        config(['app.timezone' => $timezone]);
    }

    // "AZ", "az" and "Arizona" all mean AZ.
    private static function stateCode($state): ?string
    {
        $state = trim((string) $state);

        if ($state === '') {
            return null;
        }

        if (strlen($state) === 2) {
            return strtoupper($state);
        }

        foreach (config('usstates', []) as $code => $name) {
            if (strcasecmp($name, $state) === 0) {
                return $code;
            }
        }

        return null;
    }
}
