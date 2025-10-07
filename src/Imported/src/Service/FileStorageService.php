<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;

class FileStorageService
{
    public function __construct(
        private readonly FilesystemOperator $defaultStorage,
        private readonly ?FilesystemOperator $s3Storage = null,
        private readonly LoggerInterface $logger,
    ) {}

    public function delete(string $path): void
    {
        try {
            if ($this->defaultStorage->fileExists($path)) {
                $this->defaultStorage->delete($path);
            }
            if ($this->s3Storage && $this->s3Storage->fileExists($path)) {
                $this->s3Storage->delete($path);
            }
        } catch (\Throwable $e) {
            $this->logger->error("File delete error", ['path' => $path, 'e' => $e->getMessage()]);
        }
    }

    public function generateThumbnail(string $path): void
    {
        try {
            $source = $this->defaultStorage->read($path);
            $image = imagecreatefromstring($source);
            if (!$image) {
                return;
            }

            $thumb = imagescale($image, 200, 200);
            ob_start();
            imagejpeg($thumb, null, 85);
            $thumbData = ob_get_clean();

            $thumbPath = preg_replace('/(\.\w+)$/', '_thumb$1', $path);
            $this->defaultStorage->write($thumbPath, $thumbData);

            if ($this->s3Storage) {
                $this->s3Storage->write($thumbPath, $thumbData);
            }
        } catch (\Throwable $e) {
            $this->logger->error("Thumbnail generation error", ['path' => $path, 'e' => $e->getMessage()]);
        }
    }

    public function updateFileMetadata(string $path): void
    {
        try {
            if ($this->defaultStorage->fileExists($path)) {
                $size = $this->defaultStorage->fileSize($path);
                $mime = $this->defaultStorage->mimeType($path);
                $this->logger->info("File metadata updated", [
                    'path' => $path,
                    'size' => $size,
                    'mime' => $mime,
                ]);
            }
        } catch (\Throwable $e) {
            $this->logger->error("Metadata update error", ['path' => $path, 'e' => $e->getMessage()]);
        }
    }
}
