<?php

use App\Models\Core\Propflyer;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to manage photos.");
}

$flyer = Propflyer::with('thePhotos')
    ->where('id', $flyerId)
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or access denied.");
}

$data['flyer'] = $flyer;