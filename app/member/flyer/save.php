<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propmeta;
use App\Models\Core\Propstyle;

// Validate the request data
$validatedData = $request->validate([
    'xFullStreet' => 'required|string|max:255',
    'xCity'       => 'required|string|max:100',
    'xState'      => 'required|string|max:2',
    'xZip'        => 'required|digits:5',
    'xMlsNum'     => 'nullable|integer|digits_between:1,15',
]);

$flyerId = (int) request('flyerId');

//set to false by default, if flyerId is not provided, it will be treated as a new flyer
$isNewFlyer = false;

if ($flyerId) {

    // Load existing flyer and verify ownership
    $flyer = Propflyer::where('id', $flyerId)
        ->where('propagent_id', auth()->id())
        ->first();

    if (!$flyer) {
        dd("Error: Flyer not found or you don't have permission to edit it.");
    }

} else {

    // Create new flyer
    $flyer = new Propflyer();
    $flyer->propagent_id = auth()->id();
    $isNewFlyer = true;
    
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

//must populate meta with zipDir and mlsDir for new flyers
if ($isNewFlyer) {
    Propmeta::create([
        'propflyer_id' => $flyer->id,
        'propagent_id' => auth()->id(),
        'zipDir'       => $validatedData['xZip'],
        'mlsDir'       => !empty($validatedData['xMlsNum'])
            ? $validatedData['xMlsNum']
            : 'U' . $flyer->id,
    ]);

    Propstyle::create([
        'propflyer_id' => $flyer->id,
        'propagent_id' => auth()->id(),
        'flyer_background' => 'cccccc',
        'headline_bar_bg' => '333333',
        'headline_bar_text' => 'ffffff',
        'headline_text' => '333333',
        'graphic_words' => 'greatbuy',
        'graphic_textcolor' => 'ffffff',
        'graphic_style' => 'ul',
        'roundedtop' => 'roundedtop-600px_cccccc.gif',
        'accentbars' => '333333',   
    ]);
}

redirect('/member/flyer/details?flyerId='.$flyer->id)->send();
exit();