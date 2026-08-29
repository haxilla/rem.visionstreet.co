<?php

use App\Models\Core\Propphoto;
use App\Models\Core\Propflyer;

header('Content-Type: application/json');

if (!isset($_POST['photoID'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Photo ID is required.'
    ]);

    exit;

}

$photoID = (int) $_POST['photoID'];

$photo = Propphoto::find($photoID);

if (!$photo) {

    echo json_encode([
        'success' => false,
        'message' => 'Photo not found.'
    ]);

    exit;

}

$flyer = Propflyer::find($photo->propflyer_id);

if (!$flyer) {

    echo json_encode([
        'success' => false,
        'message' => 'Flyer not found.'
    ]);

    exit;

}

if (!auth('member')->check()) {

    echo json_encode([
        'success' => false,
        'message' => 'Member not logged in.'
    ]);

    exit;

}

if ($flyer->propagent_id != auth('member')->id()) {

    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized.'
    ]);

    exit;

}

// Each upload produces multiple Propphoto rows (one per resized size,
// grouped by oldFileName) - clear def on every row for this flyer,
// then set it on every row belonging to the chosen photo.
Propphoto::where('propflyer_id', $flyer->id)
    ->where('def', 1)
    ->update(['def' => 0]);

Propphoto::where('propflyer_id', $flyer->id)
    ->where('oldFileName', $photo->oldFileName)
    ->update(['def' => 1]);

$photos = Propphoto::where('propflyer_id', $flyer->id)
    ->where('resized', 500)
    ->orderByDesc('photoDate')
    ->get(['photoID', 'photoName', 'ord', 'def']);

echo json_encode([
    'success' => true,
    'photos'  => $photos,
]);

exit;
