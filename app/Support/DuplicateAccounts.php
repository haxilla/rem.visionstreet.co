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
     * For each account id: how many flyers it holds (deleted ones included) and the
     * reasons, other than flyers, that it can't be deleted.
     *
     * @return array<int, array{flyers:int, reasons:string[]}>
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
            ->distinct()->pluck('propagent_id')->flip()->all();

        $campaigns = DB::table('remuserdb.propdelivnow')->whereIn('propagent_id', $ids)->distinct()->pluck('propagent_id')
            ->merge(DB::table('remuserdb.propdelivs')->whereIn('propagent_id', $ids)->distinct()->pluck('propagent_id'))
            ->flip()->all();

        $out = [];

        foreach ($accounts as $account) {
            $id      = (int) $account->id;
            $reasons = [];

            if ((int) ($account->remCreds ?? 0) > 0) {
                $reasons[] = 'credits';
            }

            if (isset($orders[$id])) {
                $reasons[] = 'order history';
            }

            if (isset($campaigns[$id])) {
                $reasons[] = 'campaign history';
            }

            $out[$id] = ['flyers' => (int) ($flyers[$id] ?? 0), 'reasons' => $reasons];
        }

        return $out;
    }

    /** True when the account has no flyers and nothing else that deleting would destroy. */
    public static function canDelete(array $blockers): bool
    {
        return $blockers['flyers'] === 0 && $blockers['reasons'] === [];
    }
}
