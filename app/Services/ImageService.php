<?php

namespace App\Services;

final class ImageService
{
    public static function toWebpAndResize(
        string $tmpFile,
        string $mimeType,
        string $destinationPath,
        int $maxWidth = 1600,
        int $quality = 85
    ): void {
        $src = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($tmpFile),
            'image/png'  => imagecreatefrompng($tmpFile),
            'image/webp' => imagecreatefromwebp($tmpFile),
            default      => null
        };

        if (!$src) {
            throw new \RuntimeException('Unsupported image type');
        }

        $w = imagesx($src);
        $h = imagesy($src);

        if ($w <= 0 || $h <= 0) {
            imagedestroy($src);
            throw new \RuntimeException('Invalid image dimensions');
        }

        $newW = $w > $maxWidth ? $maxWidth : $w;
        $newH = (int) round($h * ($newW / $w));

        $dstImg = imagecreatetruecolor($newW, $newH);

        // keep alpha for png/webp
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);

        imagecopyresampled($dstImg, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);

        if (!imagewebp($dstImg, $destinationPath, $quality)) {
            imagedestroy($src);
            imagedestroy($dstImg);
            throw new \RuntimeException('Failed to save image');
        }

        imagedestroy($src);
        imagedestroy($dstImg);
    }
}