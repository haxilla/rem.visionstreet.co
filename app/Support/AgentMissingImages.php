<?php

namespace App\Support;

use App\Models\Core\Propagent;
use Illuminate\Support\Facades\Cache;

/**
 * Agents (with a start date) whose photo or logo is NAMED in the database but whose file is not on
 * the server - the ones to fix first, because the agent believes they have one and their flyers
 * show none. (An agent with NOTHING named is a different, quieter list: the "No Photo" / "No Logo"
 * tabs split the two.)
 *
 * "Not on the server" is decided by AgentImages - the same places the flyers look, including the old
 * photo folder - so this never disagrees with what a flyer actually shows. Finding them means
 * checking a file for every agent that has one named, so the COUNT is kept for ten minutes (the Agents
 * page needs it on every load to decide whether a tab shows); opening a list always checks fresh and
 * refreshes the count.
 */
class AgentMissingImages
{
    private const TTL = 600;

    /** 'photo' or 'logo' => the column that names the file. */
    private static function column(string $kind): string
    {
        return $kind === 'logo' ? 'agtLogo' : 'agtPhoto';
    }

    /**
     * The ids of agents whose named file is missing, checked now.
     *
     * @return int[]
     */
    public static function ids(string $kind): array
    {
        $column = self::column($kind);
        $ids    = [];

        Propagent::query()
            ->select('id', 'agtPhoto', 'agtLogo', 'startDate')
            // photos may sit in the old folder (found through the cleanup record); a logo needs the agent's office
            ->with($kind === 'logo' ? ['theAgtOffice'] : ['theAgentCleanup'])
            ->whereNotNull('startDate')
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->chunkById(500, function ($agents) use (&$ids, $kind) {
                foreach ($agents as $agent) {
                    $found = $kind === 'logo' ? AgentImages::logo($agent)['found'] : AgentImages::photo($agent)['found'];

                    if (!$found) {
                        $ids[] = (int) $agent->id;
                    }
                }
            });

        return $ids;
    }

    /** How many are missing, from the cache when it is under ten minutes old. */
    public static function count(string $kind): int
    {
        try {
            return (int) Cache::remember("agents.missing.{$kind}", self::TTL, fn () => count(self::ids($kind)));
        } catch (\Throwable $e) {
            return count(self::ids($kind));
        }
    }

    /** Keep an exact count found by opening the list. */
    public static function remember(string $kind, int $count): void
    {
        try {
            Cache::put("agents.missing.{$kind}", $count, self::TTL);
        } catch (\Throwable $e) {
            // it is worked out again next time
        }
    }
}
