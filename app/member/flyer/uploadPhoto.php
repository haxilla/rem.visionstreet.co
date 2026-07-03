<?php

use App\Models\Core\Propflyer;

header('Content-Type: application/json');

if (!isset($_FILES['photo'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No photo received'
    ]);

    exit;

}

$flyer = Propflyer::with('theMeta')->find((int)$_POST['flyerId']);

if (!$flyer || !$flyer->theMeta) {

    echo json_encode([
        'success' => false,
        'message' => 'Flyer not found'
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

echo json_encode([
    'success' => true,
    'message' => 'Saved',
    'filename' => $fileName
]);

exit;