<?php
declare(strict_types=1);

namespace App\Service\Handler;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

final class ResourceDeleteHandler implements ResourceDeleteHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ?ResourceDeleteHandlerInterface $decoratedHandler = null
    ) {}

    public function handle(object $resource): void
    {
        $this->entityManager->beginTransaction();
        try {
            if ($this->decoratedHandler) {
                $this->decoratedHandler->handle($resource);
            }
            $this->entityManager->remove($resource);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->entityManager->rollback();
            throw new DeleteHandlingException(
                'Resource has related data and cannot be deleted.',
                'foreign_key_violation',
                409,
                0,
                $exception
            );
        } catch (ORMException $exception) {
            $this->entityManager->rollback();
            throw new DeleteHandlingException(
                'Something went wrong during deleting a resource.',
                'orm_error',
                500,
                0,
                $exception
            );
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw new DeleteHandlingException(
                'Unexpected error during deleting a resource.',
                'unexpected_error',
                500,
                0,
                $exception
            );
        }
    }
}
