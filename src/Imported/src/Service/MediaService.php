<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

final class MediaService
{
    public function __construct(private readonly LoggerInterface $logger) {}

    /**
     * Resize image using GD. Returns binary contents of resized image (JPEG).
     */
    public function resize(string $binary, int $width, int $height, int $quality = 85): string
    {
        $src = imagecreatefromstring($binary);
        if (!$src) {
            throw new \RuntimeException('Invalid image data.');
        }
        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $srcW, $srcH);

        ob_start();
        imagejpeg($dst, null, $quality);
        $out = (string) ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);
        return $out;
    }
}
