<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class HealthController implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    public function __construct(private Connection $db, private ?TransportInterface $orderTransport = null) {}

    #[Route('/_health/order', name: 'order_health', methods: ['GET'])]
    public function liveness(): JsonResponse
    {
        try {
            $this->db->executeQuery('SELECT 1')->fetchOne();
            return new JsonResponse(['status' => 'ok', 'db' => true], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'fail', 'db' => false, 'error' => $e->getMessage()], 500);
        }
    }

    #[Route('/_ready/order', name: 'order_ready', methods: ['GET'])]
    public function readiness(): JsonResponse
    {
        $dbOk = false; $mqOk = true;
        try { $this->db->executeQuery('SELECT 1')->fetchOne(); $dbOk = true; } catch (\Throwable $e) {}
        // messenger transport optional probe
        if ($this->orderTransport) {
            try { $this->orderTransport->get(); $mqOk = true; } catch (\Throwable $e) { $mqOk = false; }
        }
        $ok = ($dbOk && $mqOk);
        return new JsonResponse(['status' => $ok ? 'ok' : 'fail', 'db' => $dbOk, 'mq' => $mqOk], $ok ? 200 : 503);
    }
}
