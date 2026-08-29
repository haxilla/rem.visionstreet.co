<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propmapping;
use App\Models\Core\Propremark;

$validatedData = $request->validate([

    'flyerId'      => 'required|integer',
    'xListPrice'   => 'nullable|integer',
    'xPropType'    => 'nullable|string|max:50',
    'xListingType' => 'nullable|in:Sale,Rental',
    'xYrBuilt'     => 'nullable|digits:4',
    'xBeds'        => 'nullable|numeric',
    'xBaths'       => 'nullable|numeric',
    'xSqft'        => 'nullable|integer',
    'xPool'        => 'nullable|string|max:50',
    'xParking'     => 'nullable|string|max:50',
    'xIntersection' => 'nullable|string|max:255',
    'xb1'          => 'nullable|string|max:255',
    'xb2'          => 'nullable|string|max:255',
    'xb3'          => 'nullable|string|max:255',
    'xb4'          => 'nullable|string|max:255',
    'xb5'          => 'nullable|string|max:255',
    'xb6'          => 'nullable|string|max:255',
    'xb7'          => 'nullable|string|max:255',
    'xb8'          => 'nullable|string|max:255',
    'xVirtualTour' => 'nullable|string|max:255',
    'xMlsLink'     => 'nullable|string|max:255',
    'xPubRemarks'  => 'nullable|string',

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
$flyer->xVirtualTour = $validatedData['xVirtualTour'] ?? null;
$flyer->xMlsLink    = $validatedData['xMlsLink'] ?? null;

// Step 2 completed
if (($flyer->wizardStep ?? 0) < 2) {
    $flyer->wizardStep = 2;
}

$flyer->save();

// theMeta is always created alongside the flyer in save.php
$flyer->theMeta->xPropType    = $validatedData['xPropType'] ?? null;
$flyer->theMeta->xListingType = $validatedData['xListingType'] ?? null;
$flyer->theMeta->save();

// theMap/theRemarks aren't guaranteed to exist yet - create on first save
$map = Propmapping::firstOrCreate(
    ['propflyer_id' => $flyer->id],
    ['propagent_id' => auth()->id()]
);
$map->xIntersection = $validatedData['xIntersection'] ?? null;
$map->save();

$remarks = Propremark::firstOrCreate(
    ['propflyer_id' => $flyer->id],
    ['propagent_id' => auth()->id()]
);
$remarks->xPubRemarks = $validatedData['xPubRemarks'] ?? null;
$remarks->xb1 = $validatedData['xb1'] ?? null;
$remarks->xb2 = $validatedData['xb2'] ?? null;
$remarks->xb3 = $validatedData['xb3'] ?? null;
$remarks->xb4 = $validatedData['xb4'] ?? null;
$remarks->xb5 = $validatedData['xb5'] ?? null;
$remarks->xb6 = $validatedData['xb6'] ?? null;
$remarks->xb7 = $validatedData['xb7'] ?? null;
$remarks->xb8 = $validatedData['xb8'] ?? null;
$remarks->save();

// Return to wherever the member came from (e.g. Design/Preview) if
// they were editing Details after already progressing further -
// otherwise move forward to Photos as normal.
$allowedReturns = ['create', 'details', 'photos', 'design', 'preview'];
$return = $request->input('return');

if ($return && in_array($return, $allowedReturns, true)) {
    redirect('/member/flyer/' . $return . '?flyerId=' . $flyer->id)->send();
} else {
    redirect('/member/flyer/photos?flyerId='.$flyer->id)->send();
}

exit();
