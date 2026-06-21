<?php

// Validate the request data
$validatedData = $request->validate([
    'xFullStreet' => 'required|string|max:255',
    'xCity' => 'required|string|max:100',
    'xState' => 'required|string|max:2',
    'xZip' => 'required|digits:5',
    // Add other fields as necessary
]);

$flyer = new Propflyer();

$flyer->propagent_id = auth()->id();
$flyer->xFullStreet  = $validatedData['xFullStreet'];
$flyer->xCity        = $validatedData['xCity'];
$flyer->xState       = $validatedData['xState'];
$flyer->state        = $validatedData['xState'];
$flyer->xZip         = $validatedData['xZip'];
$flyer->xxZip        = $validatedData['xZip'];

$flyer->save();

$flyerId = $flyer->id;

redirect("/member/flyer/details/$flyerId")->send();
exit();