<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propmapping;

$validatedData = $request->validate([
    'flyerId'       => 'required|integer',
    'xIntersection' => 'nullable|string|max:255',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

// theMap isn't guaranteed to exist yet - create on first save, same
// as save_details.php does for this same model.
$map = Propmapping::firstOrCreate(
    ['propflyer_id' => $flyer->id],
    ['propagent_id' => auth()->id()]
);
$map->xIntersection = $validatedData['xIntersection'] ?? null;
$map->save();

response('OK')->send();
exit();
