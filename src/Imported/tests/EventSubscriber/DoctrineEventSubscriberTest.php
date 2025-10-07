<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Vendor\VendorMedia;
use App\EventSubscriber\DoctrineEventSubscriber;
use App\Service\FileStorageService;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;

class DoctrineEventSubscriberTest extends TestCase
{
    private FileStorageService $fileStorage;
    private DoctrineEventSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->fileStorage = $this->createMock(FileStorageService::class);
        $this->subscriber = new DoctrineEventSubscriber($this->fileStorage);
    }

    public function testPreRemoveStoresFilePath(): void
    {
        $entity = new VendorMedia();
        $entity->setFilePath('/tmp/file.jpg');

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getObject')->willReturn($entity);

        $this->subscriber->preRemove($args);

        // postRemove должен вызвать delete()
        $this->fileStorage
            ->expects($this->once())
            ->method('delete')
            ->with('/tmp/file.jpg');

        $this->subscriber->postRemove($args);
    }

    public function testPostPersistGeneratesThumbnailForVendorMedia(): void
    {
        $entity = new VendorMedia();
        $entity->setFilePath('/tmp/media.jpg');

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getObject')->willReturn($entity);

        $this->fileStorage
            ->expects($this->once())
            ->method('generateThumbnail')
            ->with('/tmp/media.jpg');

        $this->subscriber->postPersist($args);
    }

    public function testPostUpdateUpdatesMetadata(): void
    {
        $entity = new VendorMedia();
        $entity->setFilePath('/tmp/updated.jpg');

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getObject')->willReturn($entity);

        $this->fileStorage
            ->expects($this->once())
            ->method('updateFileMetadata')
            ->with('/tmp/updated.jpg');

        $this->subscriber->postUpdate($args);
    }

    public function testIrrelevantEntityIsIgnored(): void
    {
        $entity = new \stdClass(); // не VendorMedia/Document/ProductAttachment

        $args = $this->createMock(LifecycleEventArgs::class);
        $args->method('getObject')->willReturn($entity);

        // Никакие методы FileStorageService не должны вызываться
        $this->fileStorage->expects($this->never())->method($this->anything());

        $this->subscriber->preRemove($args);
        $this->subscriber->postRemove($args);
        $this->subscriber->postPersist($args);
        $this->subscriber->postUpdate($args);
    }
}
