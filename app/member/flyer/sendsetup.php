<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propdelivnow;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to set up sending.");
}

$flyer = Propflyer::select(
    'id', 'propagent_id', 'xFullStreet', 'xCity', 'xState', 'xZip'
)
->where('id', $flyerId)
->where('propagent_id', auth()->id())
->with(['thePhotos' => function ($query) {
    $query->select('propflyer_id', 'photoName', 'photoID', 'def', 'resized')
        ->where('resized', 500)
        ->where('def', 1);
}])
->with(['theMeta' => function ($query) {
    $query->select('propflyer_id', 'zipDir', 'mlsDir');
}])
->first();

if (!$flyer) {
    dd("Error: Flyer not found or access denied.");
}

// Prefill the subject with the most recent campaign subject for this
// flyer, if one already exists (read-only lookup, same table the admin
// campaigns page already reads).
$lastSubject = Propdelivnow::where('propflyer_id', $flyer->id)
    ->orderByDesc('emRequest')
    ->value('emSubject');

$data['flyer'] = $flyer;
$data['lastSubject'] = $lastSubject;
