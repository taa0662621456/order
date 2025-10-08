<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

use OrderComponent\Entity\Order\IdempotencyKey;
use OrderComponent\Interface\RepositoryInterface\Order\IdempotencyKeyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class WebhookIdempotencyService
{
    public function __construct(
        private IdempotencyKeyRepositoryInterface $repo,
        private EntityManagerInterface            $em
    ) {}

    /** @return bool true if accepted (first time), false if duplicate */
    public function acceptOnce(string $key): bool
    {
        if ($this->repo->exists($key)) {
            return false;
        }
        $this->repo->add(new IdempotencyKey($key));
        $this->em->flush();
        return true;
    }
}
