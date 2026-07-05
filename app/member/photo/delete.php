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

$oldFileName = $photo->oldFileName;
$flyer = Propflyer::with('theMeta')->find($photo->propflyer_id);

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

if (
    !$flyer->theMeta ||
    empty($flyer->theMeta->zipDir) ||
    empty($flyer->theMeta->mlsDir)
) {

    echo json_encode([
        'success' => false,
        'message' => 'Missing photo directory.'
    ]);

    exit;

}

$photos = Propphoto::where('oldFileName', $oldFileName)->get();

foreach ($photos as $photo) {

    $photoPath = public_path(
        'hqphotos/' .
        $flyer->theMeta->zipDir .
        '/' .
        $flyer->theMeta->mlsDir .
        '/' .
        $photo->photoName
    );

    if (file_exists($photoPath)) {
        unlink($photoPath);
    }

    $photo->delete();

}

echo json_encode([
    'success' => true
]);

exit;