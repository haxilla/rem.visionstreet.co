<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

try {

    $response = Http::timeout(30)->get($remoteUrl);

    if ($response->successful()) {

        // Make sure the destination folder exists
        File::ensureDirectoryExists(dirname($localPath));

        // Save the file
        File::put($localPath, $response->body());

        // Verify the file was actually written
        if (File::exists($localPath)) {

            echo "✅ Downloaded {$photo->photoName}<br>";
            return true;

        } else {

            echo "❌ File write failed: {$photo->photoName}<br>";
            return false;

        }

    } else {

        echo "❌ HTTP {$response->status()} downloading {$photo->photoName}<br>";
        return false;

    }

} catch (\Exception $e) {

    echo "❌ Exception downloading {$photo->photoName}: {$e->getMessage()}<br>";
    return false;

}