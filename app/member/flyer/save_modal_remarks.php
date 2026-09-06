<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propremark;

$validatedData = $request->validate([
    'flyerId'     => 'required|integer',
    'xPubRemarks' => 'nullable|string',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

// theRemarks isn't guaranteed to exist yet - create on first save,
// same as save_details.php does for this same model.
$remarks = Propremark::firstOrCreate(
    ['propflyer_id' => $flyer->id],
    ['propagent_id' => auth()->id()]
);
$remarks->xPubRemarks = $validatedData['xPubRemarks'] ?? null;
$remarks->save();

response('OK')->send();
exit();
