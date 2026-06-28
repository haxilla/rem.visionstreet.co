<?php

Use App\Models\Core\Propphoto;

$photos = Propphoto::with([
    'theMeta' => function ($query) {
        $query->select('propflyer_id', 'zipDir', 'mlsDir');
    }
])
->whereDate('photoDate', '>=', '2026-05-01')
->take(10)
->get();

dd($photos);