<?php

use App\Models\Core\Propflyer;

$flyers = Propflyer::with('theAgent')
    ->leftJoin('propflyerstats', 'propflyers.id', '=', 'propflyerstats.propflyer_id')
    ->select('propflyers.*', 'propflyerstats.xLastDeliveryDate')
    ->orderByDesc('propflyerstats.xLastDeliveryDate')
    // tiebreaker, so the order is the same every time and pages don't repeat or skip rows
    ->orderByDesc('propflyers.id')
    ->paginate(25, ['*'], 'flyers_page');

$data = ['flyers' => $flyers];
