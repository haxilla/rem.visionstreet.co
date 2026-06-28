<?php

$directory = dirname($localPath);

if (!is_dir($directory)) {
    mkdir($directory, 0775, true);
}

$image = @file_get_contents($remoteUrl);

if ($image === false) {
    return false;
}

if (@file_put_contents($localPath, $image) === false) {
    return false;
}

if (file_exists($localPath)) {
    return true;
}

return false;