<?php

namespace App\Support;

use App\Models\Core\Propdeliv;
use App\Models\Core\Propdelivnow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Every campaign one agent has, from BOTH places campaigns are kept:
 *   propdelivnow - requested / in progress (finished ones are found here too)
 *   propdelivs   - the archive of finished campaigns
 * A campaign present in both is listed once (same flyer + area + request time +
 * start time). Used by the admin agent page's "Campaigns Sent" count AND the
 * campaign list it links to, so the two can never disagree.
 *
 * Each row is a plain array:
 *   flyer_id, area, subject, emails, status, requested, started, completed
 *   (Carbon|null; legacy zero-dates and unparseable values count as none),
 *   admin_added (bool), sort (int timestamp of its latest date, newest = biggest)
 *
 * status: 'completed' (finished) | 'delivering' (started, not finished)
 *       | 'approved' (approved, not started) | 'pending' (not approved yet)
 */
class AgentCampaigns
{
    public static function forAgent(int $agentId): Collection
    {
        $areaLabels = [];

        foreach (include app_path('flyers/campaignAreas.php') as $area) {
            $areaLabels[$area['db']] = $area['label'];
        }

        return Propdelivnow::where('propagent_id', $agentId)->get()
            ->concat(Propdeliv::where('propagent_id', $agentId)->get())
            ->map(function ($c) use ($areaLabels) {
                $requested = self::date($c->emRequest);
                $started   = self::date($c->emStart);
                $completed = self::date($c->emComplete);

                if ($completed) {
                    $status = 'completed';
                } elseif ($started) {
                    $status = 'delivering';
                } elseif ((int) ($c->authorized ?? 0) === 1) {
                    $status = 'approved';
                } else {
                    $status = 'pending';
                }

                return [
                    'flyer_id'    => (int) $c->propflyer_id,
                    'area'        => ($c->emArea_display ?? null)
                                        ?: ($areaLabels[strtolower((string) ($c->emArea ?? ''))] ?? ($c->emArea ?? 'Unknown area')),
                    'area_key'    => strtolower((string) ($c->emArea ?? '')),
                    'subject'     => $c->emSubject ?? null,
                    'emails'      => $c->totalEmails ?? null,
                    'status'      => $status,
                    'requested'   => $requested,
                    'started'     => $started,
                    'completed'   => $completed,
                    'admin_added' => $c->isAdminAdded(),
                    'sort'        => (int) optional($completed ?? $started ?? $requested)->timestamp,
                ];
            })
            ->unique(fn ($r) => $r['flyer_id'] . '|' . $r['area_key'] . '|'
                . optional($r['requested'])->timestamp . '|' . optional($r['started'])->timestamp)
            ->sortByDesc('sort')
            ->values();
    }

    private static function date($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            $date = Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;   // legacy rows can hold odd values
        }

        return $date->year > 1970 ? $date : null;   // and zero-dates
    }
}
