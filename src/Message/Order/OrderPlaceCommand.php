<?php
declare(strict_types=1);

namespace OrderComponent\Message\Order;

final class OrderPlaceCommand
{
    /** @param array{orderId:string,customerId $payload $payload :?string,vendorId:?string,currency:string,items:array<array{sku:string,qty:int,price:string}>,placeAt:string} $payload */
    public function __construct(public array $payload) {}
}
