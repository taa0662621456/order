<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthCheckController
{
    public function __construct(private Connection $db) {}

    #[Route('/_health/order', name: 'order_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $this->db->executeQuery('SELECT 1')->fetchOne();
            return new JsonResponse(['status' => 'ok', 'db' => true], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'fail', 'db' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
