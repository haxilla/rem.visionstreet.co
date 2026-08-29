<?php

use App\Models\Core\Propflyer;

require_once app_path('member/photo/photoList.php');

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to manage photos.");
}

$flyer = Propflyer::where('id', $flyerId)
->where('propagent_id', auth()->id())
->first();

if (!$flyer) {
    dd("Error: Flyer not found or access denied.");
}

// Reaching Photos means Details is done. Photos has no discrete save
// action of its own (uploads happen ad hoc via AJAX), so without this
// wizardStep would never actually record that Photos was reached at
// all unless the member also continued on to Design - meaning simply
// visiting Photos and then going back to edit an earlier step would
// leave Photos looking locked/unreached again.
if (($flyer->wizardStep ?? 0) < 3) {
    $flyer->wizardStep = 3;
    $flyer->save();
}

$data['flyer'] = $flyer;
$data['initialPhotos'] = getPhotoListForFlyer($flyer->id);