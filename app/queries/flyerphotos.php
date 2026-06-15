<?php

use App\Models\Core\Propflyer;

if (!$flyerId) {
    dd('error-line7-queries/photos.php');
}

$propInfo = Propflyer::select(
    'id',
    'xFullStreet',
    'xCity',
    'xState',
    'xZip',
    'xxZip',
    'xMlsNum'
)
->where('id', '=', $flyerId)

->with([
    'thePhotos' => function ($q) {
        $q->select(
            'propflyer_id',
            'photoName',
            'photoID',
            'def',
            'ord',
            'orient',
            'resized'
        )
        ->where('resized', '=', '500')
        ->orderBy('def', 'desc')
        ->orderBy('ord');
    }
])

->with([
    'theMeta' => function ($q) {
        $q->select(
            'propflyer_id',
            'zipDir',
            'mlsDir'
        );
    }
])

->first();

if (!$propInfo) {
    dd('error-line43-photos.php');
}