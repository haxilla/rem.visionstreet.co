<?php

use App\Models\Core\Propflyer;
use App\Models\Core\Propphoto;

function resizePhoto(
    Propphoto $photo,
    Propflyer $flyer,
    int $targetSize
)
{

    if (!$photo->photoName) {
        throw new Exception('Photo name is missing.');
    }

    if (
        !$flyer->theMeta ||
        empty($flyer->theMeta->zipDir) ||
        empty($flyer->theMeta->mlsDir)
    ) {
        throw new Exception('Photo directory is missing.');
    }

    if (!in_array($targetSize, [500, 1000])) {
        throw new Exception('Invalid resize size.');
    }

    $photoPath = public_path(
        'hqphotos/' .
        $flyer->theMeta->zipDir .
        '/' .
        $flyer->theMeta->mlsDir .
        '/' .
        $photo->photoName
    );

    if (!file_exists($photoPath)) {
        throw new Exception('Original photo not found.');
    }

    list($width, $height) = getimagesize($photoPath);

    $ratio = round($width / $height, 4);

    $orient = ($height > $width)
        ? 'tall'
        : 'wide';

    if ($targetSize == 500) {

        $maxWide = 500;
        $maxTall = 500;
        $resized = 500;

    } else {

        $maxWide = 1000;
        $maxTall = 600;
        $resized = 1000;

    }

    if ($orient == 'wide') {

        $needsResize = ($width > $maxWide);

        if ($needsResize) {

            $resize_w = $maxWide;
            $resize_h = round($maxWide / $ratio);

        } else {

            $resize_w = $width;
            $resize_h = $height;

        }

        $filePrefix = 'w' . $targetSize;

    } else {

        $needsResize = ($height > $maxTall);

        if ($needsResize) {

            $resize_h = $maxTall;
            $resize_w = round($maxTall * $ratio);

        } else {

            $resize_w = $width;
            $resize_h = $height;

        }

        $filePrefix = 'h' . $maxTall;

    }

    $resizedName = $filePrefix . '-' . $photo->photoName;
    $resizedLoc = public_path(
    'hqphotos/' .
    $flyer->theMeta->zipDir .
    '/' .
    $flyer->theMeta->mlsDir .
    '/' .
    $resizedName
);
    
    return['step'=>1];

    //resize or copy the image
    if ($needsResize) {

        smart_resize_image(
            $photoPath,
            null,
            $resize_w,
            $resize_h,
            false,
            $resizedLoc,
            false,
            false,
            90
        );

    } else {

        if (!copy($photoPath, $resizedLoc)) {
            throw new Exception('Unable to create resized image.');
        }

    }

    if (!file_exists($resizedLoc)) {
        throw new Exception('Resized image was not created.');
    }

    $newFileSize = filesize($resizedLoc);

    list($newWidth, $newHeight) = getimagesize($resizedLoc);

    $newRatio = round($newWidth / $newHeight, 4);

    return [
        'photoName' => $resizedName,
        'width'     => $newWidth,
        'height'    => $newHeight,
        'ratio'     => $newRatio,
        'fileSize'  => $newFileSize,
        'resized'   => $resized,
    ];

}

