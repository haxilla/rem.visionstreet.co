<?php

namespace App\Support;

/**
 * The admin navigation, in ONE place. The top bar (resources/views/admin/layout/nav.blade.php)
 * draws whatever is listed here, so a new admin page is one new line:
 *
 *   ['label' => 'Name', 'href' => '/admin/thing', 'match' => ['admin/thing*']]
 *
 * An entry with 'items' is a drop-down group instead of a link. Group related tools (lists the
 * site runs on, reports, ...) under one heading so the bar doesn't grow a link per page.
 *
 *  - 'match': URL patterns (request()->is) that light the entry up as "you are here";
 *  - 'note' (optional, group items only): a one-line description shown under the name.
 */
class AdminMenu
{
    public static function items(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'href'  => '/admin/dashboard',
                'match' => ['admin/dashboard*'],
            ],
            [
                'label' => 'Agents',
                'href'  => '/admin/agents',
                'match' => ['admin/agents*', 'admin/agentView/*', 'admin/agentMerge/*', 'admin/agentCampaigns/*'],
            ],
            [
                'label' => 'Flyers',
                'href'  => '/admin/flyers',
                'match' => ['admin/flyers*', 'admin/flyerCamps/*', 'admin/flyerEdit/*'],
            ],
            [
                // the reference lists the rest of the site is built on
                'label' => 'Data',
                'items' => [
                    [
                        'label' => 'Cities & Areas',
                        'href'  => '/admin/cities',
                        'note'  => 'Which region, sub-area and MLS each city belongs to',
                        'match' => ['admin/cities*'],
                    ],
                ],
            ],
            [
                'label' => 'Settings',
                'href'  => '/admin/settings',
                'match' => ['admin/settings*'],
            ],
        ];
    }

    /** Does the current request belong to this menu entry (a link, or any item of a group)? */
    public static function isActive(array $entry): bool
    {
        foreach ($entry['items'] ?? [] as $child) {
            if (self::isActive($child)) {
                return true;
            }
        }

        foreach ((array) ($entry['match'] ?? []) as $pattern) {
            if (request()->is($pattern)) {
                return true;
            }
        }

        return false;
    }
}
