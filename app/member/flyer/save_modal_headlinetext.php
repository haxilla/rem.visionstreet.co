<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId'   => 'required|integer',
    'xHeadline' => 'nullable|string|max:255',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

// Dual-write both columns, same as save_details.php - xHeadline is the
// primary field, xxHeadline is the legacy fallback the display reads
// when xHeadline is empty.
$flyer->xHeadline  = $validatedData['xHeadline'] ?? null;
$flyer->xxHeadline = $validatedData['xHeadline'] ?? null;
$flyer->save();

response('OK')->send();
exit();
