<?php

use App\Models\Core\Propphoto;

function resizeData(
    Propphoto $photo,
    array $resize
)
{

    $newPhoto = new Propphoto();

    $newPhoto->propflyer_id = $photo->propflyer_id;
    $newPhoto->propagent_id = $photo->propagent_id;

    $newPhoto->photoDate    = $photo->photoDate;

    $newPhoto->photoName    = $resize['photoName'];
    $newPhoto->oldFileName  = $photo->oldFileName;

    $newPhoto->ord          = $photo->ord;
    $newPhoto->def          = $photo->def;

    $newPhoto->width        = $resize['width'];
    $newPhoto->height       = $resize['height'];
    $newPhoto->ratio        = $resize['ratio'];
    $newPhoto->orient       = $resize['orient'];

    $newPhoto->resize_w     = $resize['resize_w'];
    $newPhoto->resize_h     = $resize['resize_h'];

    $newPhoto->originalWidth  = $resize['originalWidth'];
    $newPhoto->originalHeight = $resize['originalHeight'];

    $newPhoto->oldFileSize  = $resize['oldFileSize'];
    $newPhoto->newFileSize  = $resize['newFileSize'];

    $newPhoto->resized      = $resize['resized'];

    $newPhoto->save();

    return $newPhoto;

}