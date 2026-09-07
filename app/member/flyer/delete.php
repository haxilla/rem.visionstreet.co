<?php

use App\Models\Core\Propflyer;

//make sure it belongs to user or error
$flyer = Propflyer::where('id', request('flyerId'))
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to delete it.");}

// Soft delete only (Propflyer uses SoftDeletes) - nothing is removed from
// disk or from any related table (photos, styles, remarks, campaign
// history, etc.). The flyer just stops appearing in normal member
// queries since they all go through this model, which is enough to
// hide everything hanging off it too.
$flyer->delete();

redirect('/member/dashboard')->send();
exit();
