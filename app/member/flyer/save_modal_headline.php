<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId'       => 'required|integer',
    'graphic_words' => 'required|string|max:50',
    'graphic_style' => 'required|string|max:20',
]);

$flyer = Propflyer::with('theStyle')
    ->where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer || !$flyer->theStyle) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->theStyle->graphic_words   = $validatedData['graphic_words'];
$flyer->theStyle->graphic_style   = $validatedData['graphic_style'];

// Keep Design's progressive-unlock state consistent - editing the
// headline graphic from here should count the same as confirming it
// on the Design page, so Design doesn't show it as still un-chosen.
$flyer->theStyle->headline_chosen = true;
$flyer->theStyle->save();

response('OK')->send();
exit();
