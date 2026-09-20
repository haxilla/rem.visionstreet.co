<?php

namespace App\Support;

use App\Models\Core\Propagent;
use Illuminate\Support\Collection;

/**
 * Agents who share a NAME - the same first and last name once each is put the way a web address
 * spells it (AgentNames::pascal: capitals, spaces, hyphens and apostrophes ignored, so "Mary-Ann
 * O'Brien", "MARYANN OBRIEN" and "Maryann Obrien" are one name). It is the same match the agent web
 * address slugs use to decide who gets a number (DebraLee, DebraLee2), so this list and the
 * backfill's duplicates report always agree.
 *
 * Not the same as "duplicate logins" (one login email on several accounts): two accounts with
 * one name can be two different people. The "Duplicate Names" tab and the merge tools' by=name
 * mode use this, and only ever act with the admin's explicit say-so.
 */
class AgentNameDuplicates
{
    /** name key => account ids (ascending), only names held by two or more accounts */
    private static ?array $groups = null;

    /** The key two agents share when they have the same name, or null when there is no usable name. */
    public static function key($agent): ?string
    {
        [$first, $last] = AgentNames::forForm($agent);

        $key = strtolower(AgentNames::pascal($first) . AgentNames::pascal($last));

        return strlen($key) >= AgentSlug::MIN ? $key : null;
    }

    /**
     * Every shared name and the accounts holding it. One pass over the agents (id and names only),
     * kept for the rest of the request.
     *
     * @return array<string, int[]>
     */
    public static function groups(): array
    {
        if (self::$groups !== null) {
            return self::$groups;
        }

        $byKey = [];

        foreach (Propagent::query()->select('id', 'agtFirst', 'agtLast', 'agtFullName')->orderBy('id')->cursor() as $agent) {
            $key = self::key($agent);

            if ($key !== null) {
                $byKey[$key][] = (int) $agent->id;
            }
        }

        return self::$groups = array_filter($byKey, fn ($ids) => count($ids) > 1);
    }

    /** The ids of every account with this agent's name (just the agent's own when nobody shares it). */
    public static function idsFor($agent): array
    {
        $key = self::key($agent);

        return ($key !== null && isset(self::groups()[$key])) ? self::groups()[$key] : [(int) $agent->id];
    }

    /** The accounts (with their office) that share this agent's name, the agent included. */
    public static function accountsFor(Propagent $agent): Collection
    {
        return Propagent::with('theAgtOffice')
            ->whereIn('id', self::idsFor($agent))
            ->orderBy('id')
            ->get();
    }

    /** Where the number of shared names is kept, so the Agents page can tell whether the tab is empty without a scan. */
    public const COUNT_CACHE_KEY = 'agents.nameDuplicateCount';

    /**
     * How many names are shared by two or more accounts. Finding out reads every agent, so the answer
     * is kept for five minutes (and dropped when an account is deleted or a name is edited); opening the
     * Duplicate Names tab refreshes it exactly. A cache problem just means it is worked out each time.
     */
    public static function count(): int
    {
        try {
            return (int) \Illuminate\Support\Facades\Cache::remember(self::COUNT_CACHE_KEY, 300, fn () => count(self::groups()));
        } catch (\Throwable $e) {
            return count(self::groups());
        }
    }

    public static function rememberCount(int $count): void
    {
        try {
            \Illuminate\Support\Facades\Cache::put(self::COUNT_CACHE_KEY, $count, 300);
        } catch (\Throwable $e) {
            // not cached - it is worked out again next time
        }
    }

    public static function forgetCount(): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget(self::COUNT_CACHE_KEY);
        } catch (\Throwable $e) {
            // nothing to forget
        }
    }

    /** An id a group can be linked to on the page. */
    public static function anchor(string $key): string
    {
        return 'nm-' . substr(md5($key), 0, 10);
    }
}
