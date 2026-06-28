<?php

header('Content-Type: application/json');

echo json_encode([
    'total'     => 100,
    'processed' => 0,
    'remaining' => 100,
    'resized'   => 0,
]);

exit;