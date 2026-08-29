<?php

use App\Models\Core\Propphoto;

/**
 * One entry per logical uploaded photo (each upload produces multiple
 * Propphoto rows grouped by oldFileName, one per resized size).
 * photoName is the 500px version (used for thumbnails), photoName1000
 * is the 1000px version (used for the larger cover-photo display) -
 * falls back to the 500px version if a 1000px row is somehow missing.
 */
function getPhotoListForFlyer(int $flyerId): array
{
    $rows = Propphoto::where('propflyer_id', $flyerId)
        ->whereIn('resized', [500, 1000])
        ->orderByDesc('photoDate')
        ->get(['photoID', 'photoName', 'oldFileName', 'resized', 'ord', 'def']);

    $order = $rows->pluck('oldFileName')->unique()->values();
    $grouped = $rows->groupBy('oldFileName');

    return $order->map(function ($oldFileName) use ($grouped) {

        $variants = $grouped[$oldFileName];
        $r500 = $variants->firstWhere('resized', 500);
        $r1000 = $variants->firstWhere('resized', 1000);

        if (!$r500) {
            return null;
        }

        return [
            'photoID'       => $r500->photoID,
            'photoName'     => $r500->photoName,
            'photoName1000' => $r1000->photoName ?? $r500->photoName,
            'ord'           => $r500->ord,
            'def'           => $r500->def,
        ];

    })->filter()->values()->all();
}
