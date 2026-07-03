<?php

header('Content-Type: application/json');

if (!isset($_FILES['photo'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No photo received'
    ]);

    exit;

}

$uploadDir = storage_path('app/tempPhotos');

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$fileName = uniqid() . '_' . basename($_FILES['photo']['name']);

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