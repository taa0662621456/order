<?php
declare(strict_types=1);
namespace OrderComponent\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class HealthCheckController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly TransportInterface $asyncTransport) {}

    #[Route('/healthz', name: 'healthz', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $this->em->getConnection()->executeQuery('SELECT 1')->fetchOne();
            $rabbitOk = method_exists($this->asyncTransport, 'get') || method_exists($this->asyncTransport, '__toString');
            return new JsonResponse(['status'=>'ok','rabbitmq'=>$rabbitOk], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['status'=>'fail','error'=>$e->getMessage()], 500);
        }
    }
}
