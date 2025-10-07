<?php
declare(strict_types=1);

namespace App\Service\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectManager;

final class ResourceUpdateHandler implements ResourceUpdateHandlerInterface
{
    public function __construct(
        private readonly ResourceUpdateHandlerInterface $decoratedHandler,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function handle(
        object $resource,
        object $requestConfiguration,
        ObjectManager $manager,
    ): void {
        $this->entityManager->beginTransaction();

        try {
            $this->decoratedHandler->handle($resource, $requestConfiguration, $manager);

            $this->entityManager->commit();
        } catch (OptimisticLockException $exception) {
            $this->entityManager->rollback();

            throw new RaceConditionException($exception->getMessage(), (int)$exception->getCode(), $exception);
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
