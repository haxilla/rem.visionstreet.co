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

    /**
     * Whether a name is ALL CAPITALS or all lowercase (MARY BEANS, mary beans) and so needs
     * tidying. A name already in mixed case (McKenna, MaryAnn) is left exactly as entered, and
     * so is anything with fewer than two letters.
     */
    public static function needsTidy(?string $name): bool
    {
        $letters = preg_replace('/[^A-Za-z]/', '', (string) $name);

        return strlen($letters) >= 2 && ($letters === strtoupper($letters) || $letters === strtolower($letters));
    }

    /**
     * An all-caps or all-lowercase name in proper form: "MARY ANN" -> "Mary Ann", "SMITH-JONES" ->
     * "Smith-Jones", "O'MALLEY" -> "O'Malley", "MCKENNA" -> "McKenna" (a Mc before three or more
     * letters), "JOHN SMITH III" keeps its numeral. A name that is not all-caps / all-lowercase
     * is returned as entered (spaces squeezed) - it is already someone's choice of capitals.
     */
    public static function tidy(?string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', (string) $name));

        if (!self::needsTidy($name)) {
            return $name;
        }

        $name = mb_strtolower($name);

        // a capital at the start and after a space, hyphen, apostrophe or full stop
        $name = preg_replace_callback("/(^|[\s\-'’.])(\p{Ll})/u", fn ($m) => $m[1] . mb_strtoupper($m[2]), $name);

        // Mc + a capital (McKenna), when there are enough letters for it to be a surname
        $name = preg_replace_callback('/\bMc(\p{Ll}{3,})/u', fn ($m) => 'Mc' . mb_strtoupper(mb_substr($m[1], 0, 1)) . mb_substr($m[1], 1), $name);

        // roman numerals stay capitals (Smith III)
        return preg_replace_callback('/\b(Ii|Iii|Iv|Vi|Vii|Viii|Ix)\b/', fn ($m) => strtoupper($m[1]), $name);
    }

    /**
     * A name as one PascalCase word for a web address: "Mary Ann" -> "MaryAnn", "O'Brien" -> "OBrien",
     * "Smith-Jones" -> "SmithJones", "McKenna" -> "McKenna". Every word starts with a capital; a word
     * typed in mixed case keeps its own capitals, and one typed all-caps / all-lowercase does not
     * keep them ("MCKENNA" -> "Mckenna"). Letters and digits only.
     */
    public static function pascal(?string $name): string
    {
        $words = preg_split('/[^A-Za-z0-9]+/', \Illuminate\Support\Str::ascii((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return implode('', array_map(function ($word) {
            return ($word === strtoupper($word) || $word === strtolower($word))
                ? ucfirst(strtolower($word))
                : ucfirst($word);
        }, $words));
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
