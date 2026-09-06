<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId'     => 'required|integer',
    'xFullStreet' => 'required|string|max:255',
    'xCity'       => 'required|string|max:100',
    'xState'      => 'required|string|max:2',
    'xZip'        => 'required|digits:5',
]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->xFullStreet = $validatedData['xFullStreet'];
$flyer->xCity       = $validatedData['xCity'];
$flyer->xState      = $validatedData['xState'];
$flyer->xZip        = $validatedData['xZip'];
$flyer->xxZip       = $validatedData['xZip'];
$flyer->save();

response('OK')->send();
exit();
