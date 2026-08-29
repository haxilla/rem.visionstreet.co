<?php

use App\Models\Core\Propflyer;

$validatedData = $request->validate([

    'flyerId'           => 'required|integer',
    'template'           => 'required|string|max:20',
    'flyer_background'   => 'required|string|max:6',
    'accentbars'         => 'required|string|max:6',
    'headline_bar_bg'    => 'required|string|max:6',
    'headline_bar_text'  => 'required|string|max:6',
    'headline_text'      => 'required|string|max:6',
    'graphic_words'      => 'required|string|max:50',
    'graphic_style'      => 'required|string|max:20',
    'graphic_textcolor'  => 'required|string|max:6',

]);

$flyer = Propflyer::with('theStyle')
    ->where('id', $validatedData['flyerId'])
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

if (!$flyer->theStyle) {
    dd("Error: Flyer has no style record.");
}

$flyer->theStyle->template           = $validatedData['template'];
$flyer->theStyle->flyer_background   = $validatedData['flyer_background'];
$flyer->theStyle->accentbars         = $validatedData['accentbars'];
$flyer->theStyle->headline_bar_bg    = $validatedData['headline_bar_bg'];
$flyer->theStyle->headline_bar_text  = $validatedData['headline_bar_text'];
$flyer->theStyle->headline_text      = $validatedData['headline_text'];
$flyer->theStyle->graphic_words      = $validatedData['graphic_words'];
$flyer->theStyle->graphic_style      = $validatedData['graphic_style'];
$flyer->theStyle->graphic_textcolor  = $validatedData['graphic_textcolor'];
$flyer->theStyle->save();

// Step 4 completed
if (($flyer->wizardStep ?? 0) < 4) {
    $flyer->wizardStep = 4;
    $flyer->save();
}

redirect('/member/flyer/preview?flyerId=' . $flyer->id)->send();
exit();
