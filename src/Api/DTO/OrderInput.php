<?php
declare(strict_types=1);
namespace OrderComponent\Api\DTO;
final class OrderInput
{
    /** @var array<int,array{sku:string,quantity:int,unitPrice:int}> */
    public array $items = [];
    public string $currency = 'USD';
}
