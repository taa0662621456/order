<?php
declare(strict_types=1);
namespace OrderComponent\Factory;
use Zenstruck\Foundry\ModelFactory;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderFactory extends ModelFactory
{
    protected function getDefaults(): array
    {
        $statuses = OrderStatus::cases();
        return ['status' => $statuses[array_rand($statuses)]];
    }
    protected function initialize(): self
    {
        return $this->afterInstantiate(function(Order $order): void {
            $order->initAudit();
        });
    }
    protected static function getClass(): string { return Order::class; }
}
