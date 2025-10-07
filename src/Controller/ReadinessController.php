<?php
declare(strict_types=1);
namespace OrderComponent\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class ReadinessController
{
    public function __construct(private readonly TransportInterface $asyncTransport) {}

    #[Route('/readiness', name: 'readiness', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $ok = method_exists($this->asyncTransport, 'get') || method_exists($this->asyncTransport, '__toString');
        return new JsonResponse(['status' => $ok ? 'ready' : 'degraded'], $ok ? 200 : 503);
    }
}
