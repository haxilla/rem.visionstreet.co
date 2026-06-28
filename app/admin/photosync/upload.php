<?php

use Illuminate\Support\Facades\Http;

try {

    if (!file_exists($localPath)) {
        echo "❌ Local file disappeared: {$photo->photoName}<br>";
        return false;}

    $response = Http::attach(...)
    ->post(...);

    echo "HTTP Status: " . $response->status() . "<br><br>";

    echo "<pre>";
    echo htmlentities($response->body());
    echo "</pre>";

    die();

    $response = Http::timeout(30)
        ->attach(
            'photo',
            fopen($localPath, 'r'),
            $photo->photoName
        )
        ->post('https://realtyemails.com/photosync/upload.cfm', [

            'secret' => '5db2d7b8f4c74d5abcc42fd3e9183c44',
            'zipDir' => $photo->theMeta->zipDir,
            'mlsDir' => $photo->theMeta->mlsDir,

        ]);

    if ($response->successful()) {

        // Optional: verify the remote file now exists
        $verify = Http::timeout(15)->head($remoteUrl);

        if ($verify->successful()) {

            echo "✅ Uploaded {$photo->photoName}<br>";
            return true;

        } else {

            echo "❌ Upload verification failed: {$photo->photoName}<br>";
            return false;

        }

    } else {

        echo "❌ HTTP {$response->status()} uploading {$photo->photoName}<br>";
        return false;

    }

} catch (\Exception $e) {

    echo "❌ Exception uploading {$photo->photoName}: {$e->getMessage()}<br>";
    return false;

}