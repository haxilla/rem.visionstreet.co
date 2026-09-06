<?php

use App\Models\Core\Propflyer;

// Backs the click-to-edit-in-place text fields on the flyer preview
// (screen mode only - see flyers/ajaxEdits/*.blade.php). Whitelisted
// so only these specific Propflyer columns can ever be touched here,
// regardless of what a request claims "field" is.
$editableFields = [
    'xFullStreet' => 'required|string|max:255',
    'xCity'       => 'required|string|max:100',
    'xState'      => 'required|string|max:2',
    'xListPrice'  => 'nullable|integer',
    'xMlsNum'     => 'nullable|integer|digits_between:1,15',
    'xHeadline'   => 'nullable|string|max:255',
];

$field = $request->input('field');

if (!array_key_exists($field, $editableFields)) {
    abort(422, 'Invalid field.');
}

$validated = $request->validate([
    'flyerId' => 'required|integer',
    'field'   => 'required|string|in:' . implode(',', array_keys($editableFields)),
    'value'   => $editableFields[$field],
]);

$flyer = Propflyer::where('id', $validated['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

$value = $validated['value'] ?? null;

$flyer->{$field} = $value;

// xHeadline has a shadow xxHeadline field read as a fallback
// elsewhere (app/flyers/variables.php) - keep both in sync, matching
// the same dual-write convention save_details.php already uses for
// beds/baths/sqft/etc.
if ($field === 'xHeadline') {
    $flyer->xxHeadline = $value;
}

$flyer->save();

$display = $field === 'xListPrice' && $value !== null
    ? '$' . number_format($value)
    : (string) $value;

response()->json(['display' => $display])->send();
exit();
