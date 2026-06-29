<?php

use App\Models\Core\Propphoto;

$batchSize = 1;
$data['results'] = [];

if (!session()->has('resizeTotal')) {

    session([
        'resizeTotal' => Propphoto::whereDate('photoDate','>=','2026-05-01')
            ->where('resized',0)
            ->count()
    ]);}

include app_path().'/admin/photosync/functions/smart_resize_image.php';

$photos = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized',0)
    ->take($batchSize)
    ->get();

foreach ($photos as $photo) {
    $zipDir=$photo->theMeta->zipDir;
    $mlsDir=$photo->theMeta->mlsDir;
    $source = public_path(
        "hqphotos/{$zipDir}/{$mlsDir}/{$photo->photoName}");

    if (!file_exists($source)) {
        $data['results'][] = [
            'photoDate'    => $photo->photoDate,
            'propflyer_id' => $photo->propflyer_id,
            'photoName'    => $photo->photoName,
            'status'       => 'Resize source missing',];

        continue;}

    //sets ratio, orient, height, width
    include('getDimensions.php');

    // if large enough, then resize
    if(($orient=='wide' && $width>1000)
    ||($orient=='tall' && $height>600)){
        //include resize script
        include('resize1000.php');

    }else{
        //include update only 
        include('updatePhoto.php');}

    $data['results'][] = [
        'photoDate'    => $photo->photoDate,
        'propflyer_id' => $photo->propflyer_id,
        'photoName'    => $photo->photoName,
        'status'       => 'Resized',];

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
    'results'   => $data['results'];
]);

exit;