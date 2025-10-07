<?php

namespace App\EventSubscriber;

use App\Entity\Vendor\VendorMedia;
use App\Entity\Vendor\VendorDocument;
use App\Entity\Product\ProductAttachment;
use App\Service\FileStorageService;
use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class DoctrineEventSubscriber implements EventSubscriber
{
    private array $filesToDelete = [];

    public function __construct(
        private readonly FileStorageService $fileStorage,
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($path = $this->extractFilePath($entity)) {
            $this->filesToDelete[spl_object_id($entity)] = $path;
        }
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $id = spl_object_id($entity);

        if (isset($this->filesToDelete[$id])) {
            $this->fileStorage->delete($this->filesToDelete[$id]);
            unset($this->filesToDelete[$id]);
        }
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof VendorMedia) {
            $this->fileStorage->generateThumbnail($entity->getFilePath());
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($path = $this->extractFilePath($entity)) {
            $this->fileStorage->updateFileMetadata($path);
        }
    }

    private function extractFilePath(object $entity): ?string
    {
        return match (true) {
            $entity instanceof VendorMedia       => $entity->getFilePath(),
            $entity instanceof VendorDocument    => $entity->getFilePath(),
            $entity instanceof ProductAttachment => $entity->getFilePath(),
            default => null,
        };
    }
}
