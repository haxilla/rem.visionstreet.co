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

$wasDefault = Propphoto::where('oldFileName', $oldFileName)
    ->where('def', 1)
    ->exists();

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

// If the deleted photo was the cover photo, promote the most recently
// uploaded remaining photo so there's always a cover photo whenever
// any photos exist.
if ($wasDefault) {

    $newest = Propphoto::where('propflyer_id', $flyer->id)
        ->where('resized', 500)
        ->orderByDesc('photoDate')
        ->first();

    if ($newest) {

        Propphoto::where('propflyer_id', $flyer->id)
            ->where('oldFileName', $newest->oldFileName)
            ->update(['def' => 1]);

    }

}

require_once app_path('member/photo/photoList.php');

echo json_encode([
    'success' => true,
    'photos'  => getPhotoListForFlyer($flyer->id),
]);

exit;