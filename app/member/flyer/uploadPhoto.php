<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propphoto;

header('Content-Type: application/json');

if (!isset($_FILES['photo'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No photo received'
    ]);

    exit;

}

$flyer = Propflyer::with('theMeta')->find((int)$_POST['flyerId']);

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
        'message' => 'Missing ZIP directory or MLS directory.'
    ]);

    exit;

}

$uploadDir = public_path(
    'hqphotos/' .
    $flyer->theMeta->zipDir .
    '/' .
    $flyer->theMeta->mlsDir
);

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!is_writable($uploadDir)) {

    echo json_encode([
        'success' => false,
        'message' => 'Upload folder is not writable'
    ]);

    exit;

}

$extension = strtolower(
    pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
);

$fileName = uniqid('', true) . '.' . $extension;

$destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {

    echo json_encode([
        'success' => false,
        'message' => 'Unable to save file'
    ]);

    exit;

}

$imageInfo = getimagesize($destination);

if (!$imageInfo) {

    echo json_encode([
        'success' => false,
        'message' => 'Unable to read image size'
    ]);

    exit;

}

$width = $imageInfo[0];
$height = $imageInfo[1];
$fileSize = filesize($destination);

$nextOrd = Propphoto::where('propflyer_id', $flyer->id)
    ->max('ord');

$nextOrd = is_null($nextOrd)
    ? 1
    : $nextOrd + 1;

$photo = new Propphoto();

$photo->propflyer_id = $flyer->id;
$photo->propagent_id = auth('member')->id();
$photo->photoName    = $fileName;
$photo->photoDate    = now();
$photo->ord          = $nextOrd;
$photo->def          =($nextOrd == 1) ? 1 : 0;

try {

    $photo->save();

} catch (\Exception $e) {

    die($e->getMessage());

}

$photos = Propphoto::where('propflyer_id', $flyer->id)
    ->orderByDesc('photoDate')
    ->get([
        'photoID',
        'photoName',
        'ord',
        'def'
    ]);

echo json_encode([
    'success' => true,
    'message' => 'Saved',
    'filename' => $fileName,
    'photoID'  => $photo->photoID,
    'photos'   => $photos
]);

exit;