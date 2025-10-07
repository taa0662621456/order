<?php

namespace App\Service\Admin;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

final class AdminSoftDeleteService
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public function softDelete(object $entity): void { if (method_exists($entity, 'delete')) { $entity->delete(); $this->em->flush(); return; } throw new InvalidArgumentException('Entity is not soft-deletable.'); }
    public function restore(object $entity): void { if (method_exists($entity, 'restore')) { $entity->restore(); $this->em->flush(); return; } throw new InvalidArgumentException('Entity is not soft-deletable.'); }
}
