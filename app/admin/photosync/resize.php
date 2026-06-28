<?php

use App\Models\Core\Propphoto;

$batchSize = 1;

if (!session()->has('resizeTotal')) {

    session([
        'resizeTotal' => Propphoto::whereDate('photoDate','>=','2026-05-01')
            ->where('resized',0)
            ->count()
    ]);}

include app_path().'/admin/photosync/functions/smart_resize_image.php');

$photos = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized',0)
    ->take($batchSize)
    ->get();

foreach ($photos as $photo) {

    $source = public_path(
        "hqphotos/{$photo->theMeta->zipDir}/{$photo->theMeta->mlsDir}/{$photo->photoName}");

    if (!file_exists($source)) {
        $data['results'][] = [
            'photoDate'    => $photo->photoDate,
            'propflyer_id' => $photo->propflyer_id,
            'photoName'    => $photo->photoName,
            'status'       => 'Resize source missing',];

        continue;}

    include('getDimensions.php');

    include('checkResize.php');

}

$remaining = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized',0)
    ->count();

$total = session('resizeTotal');

$processed = $total - $remaining;

header('Content-Type: application/json');

echo json_encode([
    'total'     => $total,
    'processed' => $processed,
    'remaining' => $remaining,
]);

exit;