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
    'confirmedStep'      => 'nullable|in:style,colors,headline',

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

// Each step-card's "Choose this..." button saves immediately via a
// background request, passing which single step was just confirmed -
// only that one flag flips here, so confirming Style doesn't also
// mark Colors/Headline as chosen before the member has touched them.
// The final "Save & Continue" submit (a normal navigation, no
// confirmedStep) sets all three true as a safety fallback - by the
// time that button is even clickable, all three should already be
// true from the per-step saves anyway.
$confirmedStep = $validatedData['confirmedStep'] ?? null;

if ($confirmedStep === 'style') {
    $flyer->theStyle->template_chosen = true;
} elseif ($confirmedStep === 'colors') {
    $flyer->theStyle->colors_chosen = true;
} elseif ($confirmedStep === 'headline') {
    $flyer->theStyle->headline_chosen = true;
} else {
    $flyer->theStyle->template_chosen = true;
    $flyer->theStyle->colors_chosen   = true;
    $flyer->theStyle->headline_chosen = true;
}

$flyer->theStyle->save();

// A per-step confirm just needs to persist - it isn't leaving the
// page, so there's nothing to redirect.
if ($confirmedStep) {
    response('OK')->send();
    exit();
}

// Step 4 completed
if (($flyer->wizardStep ?? 0) < 4) {
    $flyer->wizardStep = 4;
    $flyer->save();
}

redirect('/member/flyer/preview?flyerId=' . $flyer->id)->send();
exit();
