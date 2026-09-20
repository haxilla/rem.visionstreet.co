<?php

namespace App\Support;

/**
 * An agent's name is ENTERED as a first name and a last name. The name shown on flyers and
 * the public pages (propagents.agtFullName) is always made FROM those two - never typed
 * separately - so the two can't drift apart.
 *
 * Older agents may have a full name but no first / last: split() gives their form a sensible
 * starting point (first word / the rest), and combine() returns null when both boxes are
 * empty so a save never wipes an existing name.
 */
class AgentNames
{
    /** "Jamie", "Rivera" -> "Jamie Rivera" (extra spaces collapsed); null when both are empty. */
    public static function combine(?string $first, ?string $last): ?string
    {
        $full = trim(preg_replace('/\s+/', ' ', trim((string) $first) . ' ' . trim((string) $last)));

        return $full === '' ? null : $full;
    }

    /**
     * "Mary Ann Smith-Jones" -> ["Mary", "Ann Smith-Jones"]. A starting point for an agent
     * who only has a full name; whoever edits the form can correct it.
     *
     * @return array{0:string, 1:string}
     */
    public static function split(?string $full): array
    {
        $full = trim(preg_replace('/\s+/', ' ', (string) $full));

        if ($full === '') {
            return ['', ''];
        }

        $parts = explode(' ', $full, 2);

        return [$parts[0], $parts[1] ?? ''];
    }

    /** The first / last name to show in a form: what is stored, or a split of the full name if none is. */
    public static function forForm($agent): array
    {
        $first = trim((string) ($agent->agtFirst ?? ''));
        $last  = trim((string) ($agent->agtLast ?? ''));

        if ($first === '' && $last === '') {
            return self::split($agent->agtFullName ?? '');
        }

        return [$first, $last];
    }
}
