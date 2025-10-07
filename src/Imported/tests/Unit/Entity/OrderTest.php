<?php
namespace App\Tests\Unit\Entity;

use App\Entity\Order\Order;
use App\Entity\Order\OrderItem;
use App\Enum\TaxMode;
use App\ValueObject\Money;
use Tests\Utils\FakeTaxCalculator;

function makeItem(string $price, int $qty = 1, ?string $discount = null): OrderItem {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal($price));
    if ($discount) {
        $item->setUnitDiscount(Money::fromDecimal($discount));
    }
    $item->setQuantity($qty);
    $item->recalculateTotals(TaxMode::EXCLUSIVE, new FakeTaxCalculator());
    return $item;
}

it('aggregates multiple items', function () {
    $order = new Order();
    $items = [
        makeItem('10.00', 2),
        makeItem('5.00', 1, '1.00')
    ];
    $order->recalculateTotalsFromItems($items);
    expect($order->getItemsSubtotal()->amount())->toBe(2500)
        ->and($order->getDiscountTotal()->amount())->toBe(100)
        ->and($order->getGrandTotal()->amount())->toBeGreaterThan(0);
});

it('handles empty order', function () {
    $order = new Order();
    $order->recalculateTotalsFromItems([]);
    expect($order->getGrandTotal()->amount())->toBe(0);
});

it('fails when items have different currencies', function () {
    $item1 = makeItem('10.00');
    $item2 = new OrderItem();
    $item2->setUnitPrice(Money::fromMinor(1000, 'EUR'));
    $item2->setQuantity(1);
    $order = new Order();
    $order->recalculateTotalsFromItems([$item1, $item2]);
})->throws(InvalidArgumentException::class);
