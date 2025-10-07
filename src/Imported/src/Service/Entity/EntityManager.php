<?php
declare(strict_types=1);

namespace App\Service\Entity;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class EntityManager
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly LoggerInterface $logger,
    ) {}

    public function getRepository(string $entityClass): ObjectRepository
    {
        return $this->managerRegistry->getRepository($entityClass);
    }

    public function create(string $entityClass, array $data = []): object
    {
        $entity = new $entityClass();
        $this->assign($entity, $data);
        $em = $this->managerRegistry->getManagerForClass($entityClass);
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function update(object $entity, array $data = []): object
    {
        $this->assign($entity, $data);
        $em = $this->managerRegistry->getManagerForClass($entity::class);
        $em->flush();
        return $entity;
    }

    public function delete(object $entity): void
    {
        $em = $this->managerRegistry->getManagerForClass($entity::class);
        $em->remove($entity);
        $em->flush();
    }

    private function assign(object $entity, array $data): void
    {
        $pa = PropertyAccess::createPropertyAccessor();
        foreach ($data as $field => $value) {
            try {
                $pa->setValue($entity, $field, $value);
            } catch (\Throwable $e) {
                $this->logger->warning('Failed to set field', ['field' => $field, 'error' => $e->getMessage()]);
            }
        }
    }
}
