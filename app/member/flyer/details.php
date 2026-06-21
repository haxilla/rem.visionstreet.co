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
    dd("Error: Flyer not found or you don't have permission to view it.");
}

dd($flyerId);