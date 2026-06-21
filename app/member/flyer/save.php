<?php

use App\Models\Core\Propflyer;

// Validate the request data
$validatedData = $request->validate([
    'xFullStreet' => 'required|string|max:255',
    'xCity'       => 'required|string|max:100',
    'xState'      => 'required|string|max:2',
    'xZip'        => 'required|digits:5',
    'xMlsNum'     => 'nullable|integer|digits_between:1,15',
]);

$flyerId = (int) request('flyerId');

if ($flyerId) {

    // Load existing flyer and verify ownership
    $flyer = Propflyer::where('id', $flyerId)
        ->where('propagent_id', auth()->id())
        ->first();

    if (!$flyer) {
        abort(403);
    }

} else {

    // Create new flyer
    $flyer = new Propflyer();

    $flyer->propagent_id = auth()->id();
}

$flyer->xFullStreet = $validatedData['xFullStreet'];
$flyer->xCity       = $validatedData['xCity'];
$flyer->xState      = $validatedData['xState'];
$flyer->state       = $validatedData['xState'];
$flyer->xZip        = $validatedData['xZip'];
$flyer->xxZip       = $validatedData['xZip'];
$flyer->xMlsNum     = $validatedData['xMlsNum'] ?? null;

// Step 1 completed
if (($flyer->wizardStep ?? 0) < 1) {
    $flyer->wizardStep = 1;
}

$flyer->save();

redirect('/member/flyer/details?flyerId='.$flyer->id)->send();
exit();