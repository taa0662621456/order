<?php
declare(strict_types=1);

namespace Tests\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\Order;

final class OrderFactory
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function create(float $total = 100.00): Order
    {
        $order = new Order();
        if (method_exists($order, 'setTotal')) {
            $order->setTotal($total);
        }
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}
