<?php
declare(strict_types=1);

namespace OrderComponent\Tests\Order\Entity;

use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\Entity\Order\OrderPayment;
use PHPUnit\Framework\TestCase;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\OrderItem\OrderItem;
use OrderComponent\Entity\OrderItem\OrderPayment;
use OrderComponent\Entity\OrderItem\OrderShipment;

final class OrderSmokeTest extends TestCase
{
    public function testEntitiesExist(): void
    {
        self::assertTrue(class_exists(Order::class));
        self::assertTrue(class_exists(OrderItem::class));
        self::assertTrue(class_exists(OrderPayment::class));
        self::assertTrue(class_exists(OrderShipment::class));
    }
}
