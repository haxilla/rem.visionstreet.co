<?php

use App\Models\Core\Propphoto;

$batchSize = 1;

$photos = Propphoto::with([
    'theMeta' => function ($query) {
        $query->select('propflyer_id','zipDir','mlsDir');
    }
])
->whereDate('photoDate','>=','2026-05-01')
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
        "hqphotos/$zipDir/$mlsDir/$photo->photoName"
    );

    $remoteUrl =
        "https://realtyemails.com/hqphotos/$zipDir/$mlsDir/$photo->photoName";

    // Local check
    $localFound = file_exists($localPath);

    // Remote check
    $header = @get_headers($remoteUrl, 1);

    $remoteFound = false;

    if ($header && isset($header[0])) {

        if (strpos($header[0], "404") === false) {
            $remoteFound = true;
        }

    }

    echo "{$photo->photoDate} - ";
    echo "{$photo->propflyer_id} - ";
    echo "{$photo->photoName} : ";

    if ($localFound && $remoteFound) {

        echo "OK (both exist)<br>";

    }
    elseif ($localFound && !$remoteFound) {

        echo "Would UPLOAD<br>";

    }
    elseif (!$localFound && $remoteFound) {

        echo "Would DOWNLOAD<br>";

    }
    else {

        echo "Missing on BOTH<br>";

    }

    @ob_flush();
    @flush();

}