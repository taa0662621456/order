<?php
declare(strict_types=1);
namespace OrderComponent\Api\DTO;
use OrderComponent\Entity\Order;
final class OrderOutput
{
    public int $id;
    public string $status;
    public int $grandTotal;
    public static function fromEntity(Order $o): self
    {
        $d = new self();
        $d->id = $o->getId() ?? 0;
        $d->status = $o->getStatus()->value;
        $d->grandTotal = $o->getGrandTotal();
        return $d;
    }
}
