<?php

use App\Models\Core\Propphoto;

$batchSize = 1;
$uploaded = 0;
$downloaded = 0;
$missing = 0;
$ok = 0;
$data['results'] = [];

$photos = Propphoto::with([
    'theMeta' => function ($query) {
        $query->select('propflyer_id','zipDir','mlsDir');
    }
])
->whereDate('photoDate','>=','2026-03-01')
->where(function($q){
    $q->whereNull('existCheck')
      ->orWhereDate('existCheck','<','2026-06-27');
})
->where('resized','!=',1000)
->take($batchSize)
->get();

foreach($photos as $photo){

    $zipDir = $photo->theMeta->zipDir;
    $mlsDir = $photo->theMeta->mlsDir;

    $localPath = public_path(
        "hqphotos/$zipDir/$mlsDir/$photo->photoName");

    $remoteUrl =
        "https://realtyemails.com/hqphotos/$zipDir/$mlsDir/$photo->photoName";

    // Local check
    $localFound  = file_exists($localPath);

    // Remote check
    $header = @get_headers($remoteUrl, 1);
    $remoteFound = false;
    if ($header && isset($header[0])) {
        if (strpos($header[0], "404") === false) {
            $remoteFound = true;}}

    if ($localFound && $remoteFound) {

        include('exist_check.php');
        $ok++;
        $data['results'][] = [
            'photoDate'   => $photo->photoDate,
            'propflyer_id'  => $photo->propflyer_id,
            'photoName'  => $photo->photoName,
            'status' => 'OK',
        ];

    }
    elseif ($localFound && !$remoteFound) {

        $result=include('upload.php');
        if($result){
            include('exist_check.php');
            $uploaded++;
            $data['results'][] = [
                'photoDate'   => $photo->photoDate,
                'propflyer_id'  => $photo->propflyer_id,
                'photoName'  => $photo->photoName,
                'status' => 'Uploaded',
            ];
        } else {
            echo "Upload failed for {$photo->photoName}<br>";
        }   

    }
    elseif (!$localFound && $remoteFound) {

        $result=include('download.php');
        if($result){
            include('exist_check.php');
            $downloaded++;
            $data['results'][] = [
                'photoDate'   => $photo->photoDate,
                'propflyer_id'  => $photo->propflyer_id,
                'photoName'  => $photo->photoName,
                'status' => 'Downloaded',
            ];
        } else {
            echo "Download failed for {$photo->photoName}<br>";
        }

    }
    else {

        echo "Missing on BOTH<br>";

    }

}


$total = Propphoto::whereDate('photoDate','>=','2026-03-01')
    ->where('resized','!=',1000)
    ->count();

$remaining = Propphoto::whereDate('photoDate','>=','2026-03-01')
    ->where(function($q){
        $q->whereNull('existCheck')
          ->orWhereDate('existCheck','<','2026-06-27');
    })
    ->where('resized','!=',1000)
    ->count();

$completed = $total - $remaining;

header('Content-Type: application/json');

echo json_encode([
    'completed'  => $completed,
    'remaining'  => $remaining,
    'ok'         => $ok,
    'uploaded'   => $uploaded,
    'downloaded' => $downloaded,
    'missing'    => $missing,
    'results'    => $data['results'],
]);

exit;