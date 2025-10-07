<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Billing;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Billing\PaymentWebhookLog;

final class IdempotencyGuard
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function checkAndPersist(string $provider, string $eventId, string $payload): bool
    {
        $hash = hash('sha256', $payload);
        $repo = $this->em->getRepository(PaymentWebhookLog::class);
        $exists = $repo->findOneBy(['provider' => $provider, 'eventId' => $eventId]);
        if ($exists) {
            return false;
        }
        $existsHash = $repo->findOneBy(['payloadHash' => $hash]);
        if ($existsHash) {
            return false;
        }
        $log = new PaymentWebhookLog($provider, $eventId, $hash);
        $this->em->persist($log);
        $this->em->flush();
        return true;
    }
}
