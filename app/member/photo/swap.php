<?php

use App\Models\Core\Propphoto;
use App\Models\Core\Propflyer;

header('Content-Type: application/json');

if (!isset($_POST['flyerId'], $_POST['slotPhotoID'], $_POST['newPhotoID'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields.'
    ]);

    exit;

}

$flyer = Propflyer::find((int) $_POST['flyerId']);

if (!$flyer) {

    echo json_encode([
        'success' => false,
        'message' => 'Flyer not found.'
    ]);

    exit;

}

if (!auth('member')->check() || $flyer->propagent_id != auth('member')->id()) {

    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized.'
    ]);

    exit;

}

$slotPhoto = Propphoto::where('photoID', (int) $_POST['slotPhotoID'])
    ->where('propflyer_id', $flyer->id)
    ->first();

$newPhoto = Propphoto::where('photoID', (int) $_POST['newPhotoID'])
    ->where('propflyer_id', $flyer->id)
    ->first();

if (!$slotPhoto || !$newPhoto) {

    echo json_encode([
        'success' => false,
        'message' => 'Photo not found.'
    ]);

    exit;

}

require_once app_path('member/photo/photoList.php');

// Picking the photo that's already in this slot is a no-op.
if ($slotPhoto->oldFileName !== $newPhoto->oldFileName) {

    // Each upload produces multiple Propphoto rows (one per resized
    // size, grouped by oldFileName) sharing the same ord - swap ord
    // across every row for both logical photos, same grouping
    // convention setdefault.php/delete.php already use for `def`.
    $slotOrd = $slotPhoto->ord;
    $newOrd  = $newPhoto->ord;

    Propphoto::where('propflyer_id', $flyer->id)
        ->where('oldFileName', $slotPhoto->oldFileName)
        ->update(['ord' => $newOrd]);

    Propphoto::where('propflyer_id', $flyer->id)
        ->where('oldFileName', $newPhoto->oldFileName)
        ->update(['ord' => $slotOrd]);

}

echo json_encode([
    'success' => true,
    'photos'  => getPhotoListForFlyer($flyer->id),
]);

exit;
