<?php

namespace App\Tests\Service;

use App\Service\FileStorageService;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FileStorageServiceTest extends TestCase
{
    private FilesystemOperator $defaultStorage;
    private FilesystemOperator $s3Storage;
    private LoggerInterface $logger;
    private FileStorageService $service;

    protected function setUp(): void
    {
        $this->defaultStorage = $this->createMock(FilesystemOperator::class);
        $this->s3Storage = $this->createMock(FilesystemOperator::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new FileStorageService(
            $this->defaultStorage,
            $this->s3Storage,
            $this->logger
        );
    }

    public function testDeleteRemovesFileFromBothStorages(): void
    {
        $path = '/tmp/file.jpg';

        $this->defaultStorage->method('fileExists')->with($path)->willReturn(true);
        $this->s3Storage->method('fileExists')->with($path)->willReturn(true);

        $this->defaultStorage->expects($this->once())->method('delete')->with($path);
        $this->s3Storage->expects($this->once())->method('delete')->with($path);

        $this->service->delete($path);
    }

    public function testUpdateFileMetadataLogsInfo(): void
    {
        $path = '/tmp/file.jpg';

        $this->defaultStorage->method('fileExists')->with($path)->willReturn(true);
        $this->defaultStorage->method('fileSize')->with($path)->willReturn(1234);
        $this->defaultStorage->method('mimeType')->with($path)->willReturn('image/jpeg');

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with('File metadata updated', [
                'path' => $path,
                'size' => 1234,
                'mime' => 'image/jpeg',
            ]);

        $this->service->updateFileMetadata($path);
    }

    public function testGenerateThumbnailHandlesInvalidImage(): void
    {
        $path = '/tmp/invalid.jpg';

        $this->defaultStorage->method('read')->with($path)->willReturn('not-an-image');

        // Логер должен поймать ошибку (но мы не жёстко проверяем сообщение, лишь вызов)
        $this->logger->expects($this->once())->method('error');

        $this->service->generateThumbnail($path);
    }
}
