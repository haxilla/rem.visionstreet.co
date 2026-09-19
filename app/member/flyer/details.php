<?php

use App\Models\Core\Propflyer;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    abort(404);
}

$flyer = Propflyer::where('id', $flyerId)
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to view it.");}

$data['flyer'] = $flyer;

// Same fully-loaded flyer the other pages hand to the flyer templates
// (photos, style, agent, remarks...) - used for the live preview panel's
// first paint. $flyerId is already set above; ownership was checked.
include app_path('queries/flyerdetails.php');
$data['propInfo'] = $propInfo;