<?php

use App\Models\Core\Propflyer;

$flyers = Propflyer::with('theAgent')
    ->leftJoin('propflyerstats', 'propflyers.id', '=', 'propflyerstats.propflyer_id')
    ->select('propflyers.*', 'propflyerstats.xLastDeliveryDate')
    ->orderByDesc('propflyerstats.xLastDeliveryDate')
    ->paginate(25, ['*'], 'flyers_page');

$data = ['flyers' => $flyers];
