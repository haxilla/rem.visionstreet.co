<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propremark;

$validatedData = $request->validate([
    'flyerId' => 'required|integer',
    'xb1'     => 'nullable|string|max:42',
    'xb2'     => 'nullable|string|max:42',
    'xb3'     => 'nullable|string|max:42',
    'xb4'     => 'nullable|string|max:42',
    'xb5'     => 'nullable|string|max:42',
    'xb6'     => 'nullable|string|max:42',
    'xb7'     => 'nullable|string|max:42',
    'xb8'     => 'nullable|string|max:42',
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
$remarks->xb1 = $validatedData['xb1'] ?? null;
$remarks->xb2 = $validatedData['xb2'] ?? null;
$remarks->xb3 = $validatedData['xb3'] ?? null;
$remarks->xb4 = $validatedData['xb4'] ?? null;
$remarks->xb5 = $validatedData['xb5'] ?? null;
$remarks->xb6 = $validatedData['xb6'] ?? null;
$remarks->xb7 = $validatedData['xb7'] ?? null;
$remarks->xb8 = $validatedData['xb8'] ?? null;
$remarks->save();

response('OK')->send();
exit();
