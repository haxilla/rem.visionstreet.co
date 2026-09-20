<?php

namespace App\Support;

/**
 * What an agent's account type (propagents.accountType) means for the Account Info page.
 *
 *  - types 2 and 3 have an EXPIRE DATE and PRIORITY credits (pCreds);
 *  - type 5 has plain CREDITS (remCreds);
 *  - any other type shows only its number and start date.
 *
 * The type numbers are all the database gives us, so they are shown as "Type N" with a
 * plain description of what the account gets. If the types have real names (e.g. "Monthly"),
 * put them in NAMES below and they show instead.
 */
class AccountTypes
{
    /** Optional friendly names: type number => name. Empty until the real names are known. */
    private const NAMES = [];

    public static function hasExpiry(int $type): bool
    {
        return in_array($type, [2, 3], true);
    }

    public static function hasPriorityCredits(int $type): bool
    {
        return in_array($type, [2, 3], true);
    }

    public static function hasCredits(int $type): bool
    {
        return $type === 5;
    }

    public static function label(int $type): string
    {
        return self::NAMES[$type] ?? ($type > 0 ? 'Type ' . $type : 'Not set');
    }

    /** One line saying what this kind of account gets. */
    public static function description(int $type): string
    {
        return match (true) {
            self::hasExpiry($type)  => 'Includes priority credits, and runs until its expire date.',
            self::hasCredits($type) => 'Each flyer you send uses one credit.',
            default                 => '',
        };
    }
}
