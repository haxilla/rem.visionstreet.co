<?php

Use App\Models\Core\Propphoto;

$photos = Propphoto::with([
    'theMeta' => function ($query) {
        $query->select('propflyer_id', 'zipDir', 'mlsDir');
    }
])
->whereDate('photoDate', '>=', '2026-05-01')
->take(10)
->get();

foreach ($photos as $photo) {

    $localPath = public_path(
        "hqphotos/{$photo->theMeta->zipDir}/{$photo->theMeta->mlsDir}/{$photo->photoName}"
    );

    $remoteUrl = "https://realtyemails.com/hqphotos/{$photo->theMeta->zipDir}/{$photo->theMeta->mlsDir}/{$photo->photoName}";

    $localFound = file_exists($localPath);

    $remoteFound = Http::head($remoteUrl)->successful();

    echo "Photo ID: {$photo->photoID}<br>";
    echo "Photo Date: {$photo->photoDate}<br>";
    echo "Photo Name: {$photo->photoName}<br>";
    echo "Flyer ID: {$photo->propflyer_id}<br>";
    echo "Local: " . ($localFound ? 'Yes' : 'No') . "<br>";
    echo "Remote: " . ($remoteFound ? 'Yes' : 'No') . "<hr>";
}