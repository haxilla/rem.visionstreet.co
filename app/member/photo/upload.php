<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propphoto;

require app_path('member/photo/resize.php');
require app_path('member/photo/resizeData.php');

header('Content-Type: application/json');

if (!isset($_FILES['photo'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No photo received'
    ]);

    exit;

}

// PHP itself reports an upload that failed part-way or was over the server's size limit.
if (
    is_array($_FILES['photo']['tmp_name'])
    || ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
    || !is_uploaded_file($_FILES['photo']['tmp_name'])
) {

    echo json_encode([
        'success' => false,
        'message' => 'The photo did not upload. It may be too large - please try again.'
    ]);

    exit;

}

$flyer = Propflyer::with('theMeta')->find((int)($_POST['flyerId'] ?? 0));

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

// Decide what the file IS by reading it, BEFORE it is put anywhere in the public folder.
// The name the browser sends is never trusted: a script renamed "photo.jpg", or a
// "photo.php", must not be saved. Only a real JPEG / PNG / GIF is accepted (the
// resizer below can't read anything else), and the saved file's extension comes
// from what it really is.
$imageInfo = @getimagesize($_FILES['photo']['tmp_name']);

$allowedTypes = [
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG  => 'png',
    IMAGETYPE_GIF  => 'gif',
];

if (!$imageInfo || !isset($allowedTypes[$imageInfo[2]])) {

    echo json_encode([
        'success' => false,
        'message' => 'That file is not a JPG, PNG or GIF photo.'
    ]);

    exit;

}

$extension = $allowedTypes[$imageInfo[2]];

$fileName = uniqid('', true) . '.' . $extension;

$destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {

    echo json_encode([
        'success' => false,
        'message' => 'Unable to save file'
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

$photo->propflyer_id    = $flyer->id;
$photo->propagent_id    = auth('member')->id();
$photo->photoName       = $fileName;
$photo->oldFileName     = $_FILES['photo']['name'];
$photo->width           = $width;
$photo->height          = $height;
$photo->ratio           = round($width / $height, 4);
$photo->orient          = ($height > $width)
    ? 'tall'
    : 'wide';
$photo->originalWidth   = $width;
$photo->originalHeight  = $height;
$photo->oldFileSize     = $fileSize;
$photo->newFileSize     = $fileSize;
$photo->photoDate       = now();
$photo->ord             = $nextOrd;
$photo->def             =($nextOrd == 1) ? 1 : 0;

try {
    $photo->save();
} catch (\Exception $e) {
    throw $e;}

try{
    // create the 500 record
    $resize500 = resizePhoto(
        $photo,
        $flyer,
        500
    );
    //save to database
    resizeData(
        $photo,
        $resize500
    );

    // create the 1000 record
    $resize1000 = resizePhoto(
        $photo,
        $flyer,
        1000
    );
    //save to database
    resizeData(
        $photo,
        $resize1000
    );

    //mark orignal as successful.
    $photo->resized = 1;
    $photo->save();

//exception
}catch(\Exception $e) {
    //mark original as unsuccessful.
    //throw error
    $photo->resized = 2;
    $photo->save();

    throw $e;}   

require_once app_path('member/photo/photoList.php');
$photos = getPhotoListForFlyer($flyer->id);

echo json_encode([
    'success' => true,
    'message' => 'Saved',
    'filename' => $fileName,
    'photoID'  => $photo->photoID,
    'photos'   => $photos
]);

exit;