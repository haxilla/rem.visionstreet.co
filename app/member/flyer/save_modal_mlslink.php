<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId'  => 'required|integer',
    'xMlsLink' => 'nullable|string|max:255',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->xMlsLink = $validatedData['xMlsLink'] ?? null;
$flyer->save();

response('OK')->send();
exit();
