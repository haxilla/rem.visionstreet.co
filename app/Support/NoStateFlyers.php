<?php

namespace App\Support;

use App\Models\Core\Propflyer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Flyers that have NO USABLE STATE: flyers.state is blank or "N0". The old system's clean-up script wrote
 * "N0" there for every flyer whose xState it couldn't read, so these flyers can't be looked up by city +
 * state (Admin > Areas). This gives an admin the visibility to decide, flyer by flyer, whether to fix or
 * delete them: the list (Areas > No state), a summary, a guess at the state each one is probably in, and a
 * CSV of the lot.
 */
class NoStateFlyers
{
    /** The flyers (live ones - soft-deleted are excluded by the model) with no usable state. */
    public static function query(): Builder
    {
        return Propflyer::query()
            ->leftJoin('propflyerstats as s', 's.propflyer_id', '=', 'propflyers.id')
            ->leftJoin('remuserdb.propagents as a', 'a.id', '=', 'propflyers.propagent_id')
            ->where(function ($q) {
                $q->whereNull('propflyers.state')->orWhereRaw("TRIM(propflyers.state) IN ('', 'N0')");
            })
            ->whereRaw(FlyerAd::notAdSql('propflyers'))
            ->select(
                'propflyers.id', 'propflyers.propagent_id', 'propflyers.xFullStreet', 'propflyers.xCity',
                'propflyers.xState', 'propflyers.state', 'propflyers.xZip', 'propflyers.created_at',
                'propflyers.creationDate', 's.xLastDeliveryDate', 's.xWebViews', 'a.agtFullName as agent_name'
            );
    }

    /** How many - for the tab. Never breaks a page: 0 if it can't be worked out. */
    public static function count(): int
    {
        static $count = null;

        if ($count !== null) {
            return $count;
        }

        try {
            return $count = (int) Propflyer::query()
                ->where(function ($q) {
                    $q->whereNull('state')->orWhereRaw("TRIM(state) IN ('', 'N0')");
                })->whereRaw(FlyerAd::notAdSql())->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * What is in the pile, so the decision can start from numbers.
     *
     * @return array{total:int, sent:int, never_sent:int, no_city:int, raw_state:int, no_agent:int}
     */
    public static function summary(): array
    {
        $row = DB::selectOne("
            SELECT COUNT(*) AS total,
                   COALESCE(SUM(s.xLastDeliveryDate IS NOT NULL), 0) AS sent,
                   COALESCE(SUM(TRIM(COALESCE(f.xCity, '')) = ''), 0) AS no_city,
                   COALESCE(SUM(TRIM(COALESCE(f.xState, '')) <> ''), 0) AS raw_state,
                   COALESCE(SUM(a.id IS NULL), 0) AS no_agent
              FROM remuserdb.propflyers f
              LEFT JOIN propflyerstats s ON s.propflyer_id = f.id
              LEFT JOIN remuserdb.propagents a ON a.id = f.propagent_id
             WHERE f.deleted_at IS NULL
               AND (f.state IS NULL OR TRIM(f.state) IN ('', 'N0'))
               AND " . FlyerAd::notAdSql('f'));

        $total = (int) ($row->total ?? 0);

        return [
            'total'      => $total,
            'sent'       => (int) ($row->sent ?? 0),
            'never_sent' => $total - (int) ($row->sent ?? 0),
            'no_city'    => (int) ($row->no_city ?? 0),
            'raw_state'  => (int) ($row->raw_state ?? 0),
            'no_agent'   => (int) ($row->no_agent ?? 0),
        ];
    }

    /**
     * City (lower case) => the ONE state that has it in postal_cities, for cities only one state has. Used to
     * guess a stateless flyer's state.
     *
     * @return array<string, string>
     */
    public static function cityStates(): array
    {
        try {
            return collect(DB::select("
                SELECT LOWER(city) AS c, MIN(state) AS st
                  FROM remuserdb.postal_cities
                 GROUP BY LOWER(city)
                HAVING COUNT(DISTINCT state) = 1"))
                ->pluck('st', 'c')->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * The state a stateless flyer is probably in, and how we know: [state, "city"|"zip"] - or null. The city
     * is trusted when only one state has it in the table; an Arizona ZIP (85xxx or 86xxx) says AZ.
     *
     * @return array{0:string, 1:string}|null
     */
    public static function guess($flyer, array $cityStates): ?array
    {
        $city = mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $flyer->xCity)));

        if ($city !== '' && isset($cityStates[$city])) {
            return [$cityStates[$city], 'city'];
        }

        if (preg_match('/^(85|86)\d{3}/', trim((string) $flyer->xZip))) {
            return ['AZ', 'zip'];
        }

        return null;
    }
}
