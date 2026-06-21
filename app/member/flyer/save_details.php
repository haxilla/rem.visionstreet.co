<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([

    'flyerId'    => 'required|integer',
    'xListPrice' => 'nullable|integer',
    'xYrBuilt'   => 'nullable|digits:4',
    'xBeds'      => 'nullable|numeric',
    'xBaths'     => 'nullable|numeric',
    'xSqft'      => 'nullable|integer',
    'xPool'      => 'nullable|string|max:50',
    'xParking'   => 'nullable|string|max:50',

]);

$flyer = Propflyer::where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$flyer->xListPrice  = $validatedData['xListPrice'] ?? null;
$flyer->xYrBuilt    = $validatedData['xYrBuilt'] ?? null;
$flyer->xxYrBuilt   = $validatedData['xYrBuilt'] ?? null;
$flyer->xBeds       = $validatedData['xBeds'] ?? null;
$flyer->xxBeds      = $validatedData['xBeds'] ?? null;
$flyer->xBaths      = $validatedData['xBaths'] ?? null;
$flyer->xxBaths     = $validatedData['xBaths'] ?? null;
$flyer->xSqft       = $validatedData['xSqft'] ?? null;
$flyer->xxSqft      = $validatedData['xSqft'] ?? null;
$flyer->xPoolPvt    = $validatedData['xPool'] ?? null;
$flyer->xxPoolPvt   = $validatedData['xPool'] ?? null;
$flyer->xParking    = $validatedData['xParking'] ?? null;

// Step 2 completed
if (($flyer->wizardStep ?? 0) < 2) {
    $flyer->wizardStep = 2;
}

$flyer->save();

redirect('/member/flyer/photos?flyerId='.$flyer->id)->send();
exit();