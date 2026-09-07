<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([
    'flyerId'     => 'required|integer',
    'xFullStreet' => 'required|string|max:255',
    'xCity'       => 'required|string|max:100',
    'xState'      => 'required|string|in:' . implode(',', array_keys(config('usstates'))),
    'xZip'        => 'required|digits:5',
    'xListPrice'  => 'nullable|integer',
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
$flyer->xListPrice  = $validatedData['xListPrice'] ?? null;

// Same price-reduction tracking as save_details.php, since this is
// another place xListPrice can change - without this, editing price
// here would silently skip updating reducedAmount/reducedDate.
$newPrice = $validatedData['xListPrice'] ?? null;

if ($newPrice !== null) {
    if ($flyer->xInitialListPrice === null) {
        $flyer->xInitialListPrice = $newPrice;
    } else {
        $newReducedAmount = max(0, $flyer->xInitialListPrice - $newPrice);

        if ($newReducedAmount > ($flyer->reducedAmount ?? 0)) {
            $flyer->reducedDate = now()->toDateString();
        }

        $flyer->reducedAmount = $newReducedAmount > 0 ? $newReducedAmount : null;

        if ($newReducedAmount <= 0) {
            $flyer->reducedDate = null;
        }
    }
}

$flyer->save();

response('OK')->send();
exit();
