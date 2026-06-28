<?php

Use App\Models\Core\Propphoto;
use Illuminate\Support\Facades\Http;

$photos = Propphoto::with([
    'theMeta' => function ($query) {
        $query->select('propflyer_id', 'zipDir', 'mlsDir');
    }
])
->whereDate('photoDate', '>=', '2026-05-01')
->where(function ($q) {
    $q->whereNull('existCheck')
      ->orWhereDate('existCheck', '<=', '2026-06-26');
})
->take(10)
->get();

$uploaded = 0;
$downloaded = 0;
$missing = 0;
$ok = 0;

foreach ($photos as $photo) {

    // Build local path
    $localPath = public_path(
        "hqphotos/{$photo->theMeta->zipDir}/{$photo->theMeta->mlsDir}/{$photo->photoName}"
    );

    // Build remote URL
    $remoteUrl = "https://realtyemails.com/hqphotos/{$photo->theMeta->zipDir}/{$photo->theMeta->mlsDir}/{$photo->photoName}";

    $localFound = file_exists($localPath);
    try {

        $remoteFound = Http::timeout(15)
            ->head($remoteUrl)
            ->successful();

    } catch (\Exception $e) {

        echo "HEAD failed for {$photo->photoName}<br>";
        $remoteFound = false;

    }

    echo "{$photo->photoDate} - {$photo->propflyer_id} - {$photo->photoName} : ";

    if ($localFound && $remoteFound) {

        include('exist_check.php');
        echo "OK<br>";
        $ok++;

    } elseif ($localFound && !$remoteFound) {

        echo "Uploading {$photo->photoName}...<br>";
        $result=include('upload.php');
        if($result){
            include('exist_check.php');
            $uploaded++;
        } else {
            echo "Upload failed for {$photo->photoName}<br>";
        }   

    } elseif (!$localFound && $remoteFound) {

        echo "Downloading...<br>";
        $result=include('download.php');
        if($result){
            include('exist_check.php');
            $downloaded++;
        } else {
            echo "Download failed for {$photo->photoName}<br>";
        }

    } else {

        echo "Missing on both<br>";
        $missing++;

    }

}

echo "OK: $ok<br>";
echo "Uploaded: $uploaded<br>";
echo "Downloaded: $downloaded<br>";
echo "Missing: $missing<br>";