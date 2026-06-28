<?php

$image = @file_get_contents($remoteUrl);

if ($image === false) {
    return false;
}

$directory = dirname($localPath);

if (!is_dir($directory)) {
    mkdir($directory, 0775, true);
}

if (@file_put_contents($localPath, $image) === false) {
    return false;
}

return file_exists($localPath);