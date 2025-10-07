<?php
namespace App\Tests\Unit\Entity;

use App\Entity\Order\OrderItem;
use App\Enum\TaxMode;
use App\Service\Taxation\TaxationCalculator;
use App\ValueObject\Money;
use Tests\Utils\FakeTaxCalculator;

it('calculates row totals without discount or tax', function () {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal('10.00'));
    $item->setQuantity(2);
    $item->recalculateTotals(TaxMode::EXCLUSIVE, new TaxationCalculator());
    expect($item->getRowNet()->getAmount())->toBe(2000)
        ->and($item->getRowGross()->getAmount())->toBe(2000);
});

it('applies discount correctly', function () {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal('10.00'));
    $item->setUnitDiscount(Money::fromDecimal('1.00'));
    $item->setQuantity(2);
    $item->recalculateTotals(TaxMode::EXCLUSIVE, new TaxationCalculator());
    expect($item->getRowNet()->getAmount())->toBe(1800)
        ->and($item->getRowGross()->getAmount())->toBe(1800);
});

it('applies tax correctly', function () {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal('10.00'));
    $item->setQuantity(1);
    $item->recalculateTotals(TaxMode::EXCLUSIVE, new FakeTaxCalculator());
    expect($item->getRowTax()->getAmount())->toBe(200)
        ->and($item->getRowGross()->getAmount())->toBe(1200);
});

it('applies discount and tax together', function () {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal('10.00'));
    $item->setUnitDiscount(Money::fromDecimal('1.00'));
    $item->setQuantity(1);
    $item->recalculateTotals(TaxMode::EXCLUSIVE, new FakeTaxCalculator());
    expect($item->getRowNet()->getAmount())->toBe(900)
        ->and($item->getRowTax()->getAmount())->toBe(180)
        ->and($item->getRowGross()->getAmount())->toBe(1080);
});

it('throws exception for invalid qty', function () {
    $item = new OrderItem();
    $item->setUnitPrice(Money::fromDecimal('5.00'));
    $item->setQuantity(0);
})->throws(InvalidArgumentException::class);
