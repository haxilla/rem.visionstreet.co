<?php

use App\Models\Core\Propflyer;

// Each step-card's "Choose this..." button saves immediately via a
// background request naming which single step was confirmed. Only
// that step's own fields get validated/written here - a "headline"
// confirm never touches template/colors, and vice versa. Previously
// every confirm sent (and wrote) the whole form snapshot, so any
// later click on an earlier step's "Choose this..." - even just
// re-opening and re-confirming Style after Headline was already set -
// would silently drag along and overwrite whatever the other fields
// happened to be at that moment.
$fieldRulesByStep = [

    'style' => [
        'template' => 'required|string|max:20',
    ],

    'colors' => [
        'flyer_background'  => 'required|string|max:6',
        'accentbars'        => 'required|string|max:6',
        'headline_bar_bg'   => 'required|string|max:6',
        'headline_bar_text' => 'required|string|max:6',
        'headline_text'     => 'required|string|max:6',
        // graphic_textcolor is included here too, not just under
        // "headline" - it's the headline graphic's embedded color,
        // but the only thing that ever actually changes it is
        // clicking an accent swatch here in Colors (headline.js
        // preserves whatever color is already there when just the
        // headline word/style change). Without this, confirming
        // Colors after picking a new accent never persisted the
        // color change unless Headline also happened to get
        // re-confirmed afterward.
        'graphic_textcolor' => 'required|string|max:6',
    ],

    'headline' => [
        'graphic_words'     => 'required|string|max:50',
        'graphic_style'     => 'required|string|max:20',
        'graphic_textcolor' => 'required|string|max:6',
    ],

];

$confirmedStep = $request->input('confirmedStep');

$rules = [
    'flyerId'       => 'required|integer',
    'confirmedStep' => 'nullable|in:style,colors,headline',
];

if ($confirmedStep && isset($fieldRulesByStep[$confirmedStep])) {
    $rules += $fieldRulesByStep[$confirmedStep];
} else {
    foreach ($fieldRulesByStep as $stepRules) {
        $rules += $stepRules;
    }
}

$validatedData = $request->validate($rules);

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

if ($confirmedStep === 'style') {

    $flyer->theStyle->template          = $validatedData['template'];
    $flyer->theStyle->template_chosen   = true;

} elseif ($confirmedStep === 'colors') {

    $flyer->theStyle->flyer_background   = $validatedData['flyer_background'];
    $flyer->theStyle->accentbars         = $validatedData['accentbars'];
    $flyer->theStyle->headline_bar_bg    = $validatedData['headline_bar_bg'];
    $flyer->theStyle->headline_bar_text  = $validatedData['headline_bar_text'];
    $flyer->theStyle->headline_text      = $validatedData['headline_text'];
    $flyer->theStyle->graphic_textcolor  = $validatedData['graphic_textcolor'];
    $flyer->theStyle->colors_chosen      = true;

} elseif ($confirmedStep === 'headline') {

    $flyer->theStyle->graphic_words      = $validatedData['graphic_words'];
    $flyer->theStyle->graphic_style      = $validatedData['graphic_style'];
    $flyer->theStyle->graphic_textcolor  = $validatedData['graphic_textcolor'];
    $flyer->theStyle->headline_chosen    = true;

} else {

    // Final "Save & Continue" - a normal navigation, no confirmedStep.
    // By the time that button is clickable all three should already
    // be true from the per-step saves, but write everything as a
    // safety fallback.
    $flyer->theStyle->template           = $validatedData['template'];
    $flyer->theStyle->flyer_background   = $validatedData['flyer_background'];
    $flyer->theStyle->accentbars         = $validatedData['accentbars'];
    $flyer->theStyle->headline_bar_bg    = $validatedData['headline_bar_bg'];
    $flyer->theStyle->headline_bar_text  = $validatedData['headline_bar_text'];
    $flyer->theStyle->headline_text      = $validatedData['headline_text'];
    $flyer->theStyle->graphic_words      = $validatedData['graphic_words'];
    $flyer->theStyle->graphic_style      = $validatedData['graphic_style'];
    $flyer->theStyle->graphic_textcolor  = $validatedData['graphic_textcolor'];
    $flyer->theStyle->template_chosen    = true;
    $flyer->theStyle->colors_chosen      = true;
    $flyer->theStyle->headline_chosen    = true;

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
