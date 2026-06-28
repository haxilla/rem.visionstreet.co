<?php

use App\Models\Core\Propphoto;

$batchSize = 1;

$total = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized',0)
    ->count();

$remaining = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized',0)
    ->count();

$processed = $total - $remaining;

header('Content-Type: application/json');

echo json_encode([
    'total'     => $total,
    'processed' => $processed,
    'remaining' => $remaining,
]);

exit;