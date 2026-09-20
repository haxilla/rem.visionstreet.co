<?php

namespace App\Support;

use App\Models\Core\Propflyer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Deleting the leftover account when the same login email was registered more than once
 * (see the "Duplicate Logins" tab and the merge page). An account may only be deleted
 * once it has NO flyers - deleted flyers count too, since they still hold campaign
 * history; the merge page can move those. It also refuses when deleting would destroy
 * something that isn't a flyer: credits, order history, or campaign history.
 *
 * Works for a whole list at once so a page of many accounts is three queries, not many.
 */
class DuplicateAccounts
{
    /**
     * For each account id: how many flyers it holds (deleted ones included), how many
     * orders and campaign records it holds, and the reasons, other than flyers, that it
     * can't be deleted. (Orders and campaign records can be moved to another account -
     * see adminController::agentMoveRecords.)
     *
     * @return array<int, array{flyers:int, orders:int, campaigns:int, reasons:string[]}>
     */
    public static function blockersFor(Collection $accounts): array
    {
        $ids = $accounts->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($ids === []) {
            return [];
        }

        $flyers = Propflyer::withTrashed()
            ->whereIn('propagent_id', $ids)
            ->selectRaw('propagent_id, COUNT(*) as total')
            ->groupBy('propagent_id')
            ->pluck('total', 'propagent_id')
            ->all();

        $orders = DB::table('allorders')->whereIn('propagent_id', $ids)
            ->selectRaw('propagent_id, COUNT(*) as total')->groupBy('propagent_id')->pluck('total', 'propagent_id')->all();

        // a finished campaign can sit in both campaign tables, so this counts records, not campaigns
        $campaigns = [];

        foreach (['propdelivnow', 'propdelivs'] as $table) {
            foreach (DB::table("remuserdb.{$table}")->whereIn('propagent_id', $ids)
                ->selectRaw('propagent_id, COUNT(*) as total')->groupBy('propagent_id')->pluck('total', 'propagent_id') as $agentId => $n) {
                $campaigns[$agentId] = ($campaigns[$agentId] ?? 0) + (int) $n;
            }
        }

        $out = [];

        foreach ($accounts as $account) {
            $id      = (int) $account->id;
            $reasons = [];

            if ((int) ($account->remCreds ?? 0) > 0) {
                $reasons[] = 'credits';
            }

            if (($orders[$id] ?? 0) > 0) {
                $reasons[] = 'order history';
            }

            if (($campaigns[$id] ?? 0) > 0) {
                $reasons[] = 'campaign history';
            }

            $out[$id] = [
                'flyers'    => (int) ($flyers[$id] ?? 0),
                'orders'    => (int) ($orders[$id] ?? 0),
                'campaigns' => (int) ($campaigns[$id] ?? 0),
                'reasons'   => $reasons,
            ];
        }

        return $out;
    }

    /**
     * True when the account has no flyers and nothing else that deleting would destroy.
     * The same rule guards EVERY account delete (an agent's own page, the No Start Date
     * bulk delete and the duplicate tools).
     */
    public static function canDelete(array $blockers): bool
    {
        return $blockers['flyers'] === 0 && $blockers['reasons'] === [];
    }

    /** What is in the way, in words: "3 flyers, 2 orders, 12 campaign records". Empty when nothing is. */
    public static function describe(array $blockers): string
    {
        $parts = [];

        if (($blockers['flyers'] ?? 0) > 0) {
            $parts[] = $blockers['flyers'] . ' ' . ($blockers['flyers'] === 1 ? 'flyer' : 'flyers');
        }

        if (in_array('credits', $blockers['reasons'] ?? [], true)) {
            $parts[] = 'credits';
        }

        if (($blockers['orders'] ?? 0) > 0) {
            $parts[] = $blockers['orders'] . ' ' . ($blockers['orders'] === 1 ? 'order' : 'orders');
        }

        if (($blockers['campaigns'] ?? 0) > 0) {
            $parts[] = $blockers['campaigns'] . ' campaign ' . ($blockers['campaigns'] === 1 ? 'record' : 'records');
        }

        return implode(', ', $parts);
    }
}
