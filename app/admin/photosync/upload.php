<?php

if (!file_exists($localPath)) {
    return false;
}

$curl = curl_init();

curl_setopt_array($curl, [

    CURLOPT_URL => "https://realtyemails.com/photosync/upload.cfm",
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,

    CURLOPT_POSTFIELDS => [

        "secret" => "5db2d7b8f4c74d5abcc42fd3e9183c44",

        "zipDir" => $photo->theMeta->zipDir,

        "mlsDir" => $photo->theMeta->mlsDir,

        "photo" => new CURLFile(
            $localPath,
            mime_content_type($localPath),
            $photo->photoName
        )

    ]

]);

curl_exec($curl);

$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

return ($httpCode == 200);