<?php

namespace App\Support;

/**
 * Shrinks and re-encodes an uploaded agent photo / logo before it is stored, so
 * the flyer (which is emailed - every recipient downloads these) never points at
 * a multi-megabyte original.
 *
 * Uses PHP's built-in GD (the same functions app/member/photo/smart_resize_image.php
 * already relies on for property photos), so it adds no dependency.
 *
 * What it does
 *  - scales DOWN to fit a maximum box, keeping the proportions; never enlarges;
 *  - fixes phone-camera orientation (EXIF) so the picture isn't stored sideways;
 *  - re-encodes, which drops the metadata (camera details, GPS location);
 *  - outputs JPEG, or PNG for a logo that may be transparent. WebP and GIF are
 *    converted, because many email programs can't show WebP.
 *
 * Boxes are about twice what the flyer displays (the photo is shown at roughly
 * 125px tall), so they stay sharp on high-resolution screens.
 */
class ImageOptimizer
{
    public const PHOTO_BOX = [400, 400];
    public const LOGO_BOX  = [400, 200];

    // Decoding needs memory proportional to the pixel count, not the file size:
    // a small 5 MB JPEG can be 50 megapixels. Refuse those with a clear message
    // instead of crashing PHP out of memory.
    private const MAX_PIXELS = 25000000;

    /**
     * @return array{0:string, 1:string}  [encoded image bytes, file extension ('jpg' or 'png')]
     * @throws \RuntimeException with a message that is safe to show to the admin/agent
     */
    public static function optimize(string $path, array $box, bool $mayBeTransparent = false): array
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagecreatefromjpeg')) {
            throw new \RuntimeException('Image processing (GD) isn\'t available on this server, so the image can\'t be resized.');
        }

        $info = @getimagesize($path);

        if ($info === false) {
            throw new \RuntimeException('That file isn\'t a readable image.');
        }

        [$width, $height, $type] = $info;

        if ($width < 1 || $height < 1 || $width * $height > self::MAX_PIXELS || !self::fitsInMemory($width, $height)) {
            throw new \RuntimeException(
                "That image is too large to process ({$width} x {$height} pixels). "
                . 'Please resize it to about 5000 pixels or less on the long side and try again.'
            );
        }

        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => @imagecreatefrompng($path),
            IMAGETYPE_GIF  => @imagecreatefromgif($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default        => false,
        };

        if ($image === false) {
            throw new \RuntimeException('That image couldn\'t be read. Please use a JPG, PNG, GIF or WebP file.');
        }

        // Phone photos are often stored sideways with an "orientation" note.
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
            $angle       = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

            if ($angle !== 0 && ($rotated = imagerotate($image, $angle, 0)) !== false) {
                $image  = $rotated;
                $width  = imagesx($image);
                $height = imagesy($image);
            }
        }

        // Only ever shrink.
        $scale     = min($box[0] / $width, $box[1] / $height, 1);
        $newWidth  = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        // A logo that could have transparency stays a PNG; everything else is JPEG.
        $asPng = $mayBeTransparent && in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true);

        $out = imagecreatetruecolor($newWidth, $newHeight);

        if ($asPng) {
            imagealphablending($out, false);
            imagesavealpha($out, true);
            imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        } else {
            // JPEG has no transparency: flatten onto white rather than black.
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
        }

        imagecopyresampled($out, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();

        if ($asPng) {
            imagepng($out, null, 9);
        } else {
            imageinterlace($out, true);   // progressive: shows a rough picture sooner while loading
            imagejpeg($out, null, 82);
        }

        $bytes = ob_get_clean();

        if ($bytes === false || $bytes === '') {
            throw new \RuntimeException('The image couldn\'t be processed.');
        }

        return [$bytes, $asPng ? 'png' : 'jpg'];
    }

    /** "2.4 MB", "38 KB" */
    public static function humanSize(int $bytes): string
    {
        return $bytes >= 1048576
            ? number_format($bytes / 1048576, 1) . ' MB'
            : number_format(max(1, round($bytes / 1024))) . ' KB';
    }

    // The decoded bitmap (4 bytes a pixel) plus the copy being drawn, with headroom.
    private static function fitsInMemory(int $width, int $height): bool
    {
        $limit = self::memoryLimitBytes();

        return $limit <= 0 || ($width * $height * 6) < ($limit * 0.7);
    }

    private static function memoryLimitBytes(): int
    {
        $value = trim((string) ini_get('memory_limit'));

        if ($value === '' || $value === '-1') {
            return -1;
        }

        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g'     => $number * 1073741824,
            'm'     => $number * 1048576,
            'k'     => $number * 1024,
            default => $number,
        };
    }
}
