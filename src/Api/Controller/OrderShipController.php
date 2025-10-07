<?php
declare(strict_types=1);
namespace OrderComponent\Api\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Order\OrderWorkflowService;

final class OrderShipController
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly OrderWorkflowService $wf) {}

    public function __invoke(int $id): JsonResponse
    {
        $order = $this->em->find(Order::class, $id);
        if (!$order) return new JsonResponse(['message'=>'Order not found'], 404);
        $this->wf->ship($order);
        return new JsonResponse(['status'=>$order->getStatus()->value]);
    }
}
