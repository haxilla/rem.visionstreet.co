<?php

header('Content-Type: application/json');

echo json_encode([
    'success' => true,
    'message' => 'Upload endpoint reached'
]);

exit;