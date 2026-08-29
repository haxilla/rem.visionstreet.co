<?php

use App\Models\Core\Propflyer;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to design the flyer.");
}

$flyer = Propflyer::select(
    'id', 'propagent_id', 'officeID', 'xFullStreet',
    'xListPrice', 'xCity', 'xState', 'xZip', 'xxZip', 'xHeadline',
    'xMlsNum', 'xBeds', 'xxBeds', 'xBaths', 'xxBaths',
    'xSqft', 'xxSqft', 'xYrBuilt', 'xxYrBuilt', 'xVirtualTour',
    'xMlsLink', 'xxHeadline', 'wizardStep'
)
->where('id', $flyerId)
->where('propagent_id', auth()->id())
->with(['theRemarks' => function ($query) {
    $query->select(
        'propflyer_id', 'xb1', 'xb2', 'xb3', 'xb4',
        'xb5', 'xb6', 'xb7', 'xb8', 'xPubRemarks'
    );
}])
->with(['thePhotos' => function ($query) {
    $query->select(
        'propflyer_id', 'photoName', 'photoID',
        'def', 'ord', 'orient', 'resized'
    )->where('resized', 500);
}])
->with(['theStyle' => function ($query) {
    $query->select(
        'propflyer_id', 'graphic_words', 'graphic_textcolor',
        'graphic_style', 'colors_chosen', 'flyer_background',
        'template', 'headline_text', 'headline_chosen',
        'headline_bar_bg', 'accentbars', 'headline_bar_text',
        'virtualTour_chosen', 'mlsLink_chosen'
    );
}])
->with(['theAgent' => function ($query) {
    $query->select(
        'id', 'agtPhoto', 'agtLogo', 'officeID',
        'agtFullName', 'agtDesigs', 'agtMainPhone'
    );
}])
->with(['theOffice' => function ($query) {
    $query->select(
        'officeName', 'officeAddress1', 'propagent_id',
        'officeCity', 'officeState', 'officeZip', 'officeID'
    );
}])
->with(['theMap' => function ($query) {
    $query->select('propflyer_id', 'xIntersection');
}])
->with(['theMeta' => function ($query) {
    $query->select('propflyer_id', 'zipDir', 'mlsDir');
}])
->first();

if (!$flyer) {
    dd("Error: Flyer not found or access denied.");
}

// Arriving at Design marks Design itself as reached (same pattern as
// photos.php marking Photos as reached on arrival) - without this,
// going back to Photos and returning to Design would show Design as
// locked again, since only *saving* Design used to advance wizardStep
// this far, not just visiting it.
if (($flyer->wizardStep ?? 0) < 4) {
    $flyer->wizardStep = 4;
    $flyer->save();
}

$data['flyer'] = $flyer;
