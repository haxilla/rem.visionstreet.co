<?php

Use App\Models\Core\Propphoto;

$photos = Propphoto::with('theMeta')
    ->whereDate('photoDate', '>=', '2026-05-01')
    ->orderBy('propflyer_id')
    ->orderBy('photoOrder')
    ->get();

dd($photos);