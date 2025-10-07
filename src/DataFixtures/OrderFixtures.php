<?php
declare(strict_types=1);

namespace OrderComponent\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $order1 = new Order();
        $order1->setStatus(OrderStatus::Draft);

        $order2 = new Order();
        $order2->setStatus(OrderStatus::Placed);

        $manager->persist($order1);
        $manager->persist($order2);
        $manager->flush();
    }
}
