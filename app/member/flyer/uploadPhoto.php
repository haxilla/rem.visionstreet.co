<?php

header('Content-Type: application/json');

if (!isset($_FILES['photo'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No photo received'
    ]);

    exit;

}

echo json_encode([
    'success' => true,
    'message' => 'Photo received',
    'name'    => $_FILES['photo']['name'],
    'size'    => $_FILES['photo']['size'],
    'type'    => $_FILES['photo']['type'],
    'error'   => $_FILES['photo']['error']
]);

exit;