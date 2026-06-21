<?php

use App\Models\Core\Propflyer;

$flyerId = (int) request('flyerId');

if (!$flyerId) {
    dd("Error: Flyer ID is required to resume.");
}

$flyer = Propflyer::where('id', $flyerId)
    ->where('propagent_id', auth()->id())
    ->first();

if (!$flyer) {
    dd("Error: Flyer not found or you don't have permission to edit it.");
}

switch ((int)$flyer->wizardStep) {

    case 0:
    case 1:
        redirect('/member/flyer/details?flyerId='.$flyer->id)->send();
        break;

    case 2:
        redirect('/member/flyer/photos?flyerId='.$flyer->id)->send();
        break;

    case 3:
        redirect('/member/flyer/text?flyerId='.$flyer->id)->send();
        break;

    case 4:
        redirect('/member/flyer/design?flyerId='.$flyer->id)->send();
        break;

    default:
        redirect('/member/flyer/review?flyerId='.$flyer->id)->send();
        break;
}

exit();