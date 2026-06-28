<?php

use App\Models\Core\Propphoto;

$batchSize = 1;
$uploaded = 0;
$downloaded = 0;
$missing = 0;
$ok = 0;

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
        "hqphotos/$zipDir/$mlsDir/$photo->photoName");

    $remoteUrl =
        "https://realtyemails.com/hqphotos/$zipDir/$mlsDir/$photo->photoName";

    // Local check
    $localFound  = file_exists($localPath);
    $remoteFound = true;

    /*
    // Remote check
    $header = @get_headers($remoteUrl, 1);
    $remoteFound = false;
    if ($header && isset($header[0])) {
        if (strpos($header[0], "404") === false) {
            $remoteFound = true;}}
    */
            
    echo "{$photo->photoDate} - ";
    echo "{$photo->propflyer_id} - ";
    echo "{$photo->photoName} : ";

    if ($localFound && $remoteFound) {

        include('exist_check.php');
        echo "OK<br>";
        $ok++;

    }
    elseif ($localFound && !$remoteFound) {

        echo "Uploading {$photo->photoName}...<br>";
        $result=include('upload.php');
        if($result){
            include('exist_check.php');
            $uploaded++;
        } else {
            echo "Upload failed for {$photo->photoName}<br>";
        }   

    }
    elseif (!$localFound && $remoteFound) {

        echo "Downloading {$photo->photoName}...<br>";
        $result=include('download.php');
        if($result){
            include('exist_check.php');
            $downloaded++;
        } else {
            echo "Download failed for {$photo->photoName}<br>";
        }

    }
    else {

        echo "Missing on BOTH<br>";

    }

}

echo "<hr>";

echo "OK: $ok<br>";
echo "Uploaded: $uploaded<br>";
echo "Downloaded: $downloaded<br>";
echo "Missing: $missing<br>";

$total = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where('resized','!=',1000)
    ->count();

$remaining = Propphoto::whereDate('photoDate','>=','2026-05-01')
    ->where(function($q){
        $q->whereNull('existCheck')
          ->orWhereDate('existCheck','<','2026-06-27');
    })
    ->where('resized','!=',1000)
    ->count();

$completed = $total - $remaining;

echo "<hr>";

echo "<b>Processed:</b> $completed of $total<br>";
echo "<b>Remaining:</b> $remaining<br>";

if($remaining > 0){

    echo "<br>Refreshing...<br>";

    echo '<script>
        setTimeout(function(){
            location.reload();
        },1000);
    </script>';

}else{

    echo "<h2>✔ Photo Synchronization Complete</h2>";

}