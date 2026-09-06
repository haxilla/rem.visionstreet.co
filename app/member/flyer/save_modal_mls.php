<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId' => 'required|integer',
    'xMlsNum' => 'nullable|integer|digits_between:1,15',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->xMlsNum = $validatedData['xMlsNum'] ?? null;
$flyer->save();

response('OK')->send();
exit();
