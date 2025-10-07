<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Unit;
use PHPUnit\Framework\TestCase;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\ValueObject\Order\Sku;
use OrderComponent\ValueObject\Order\Quantity;
use OrderComponent\ValueObject\Money\Currency;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;
use OrderComponent\Service\Order\OrderPricing\Strategy\{FlatPromotionStrategy,FlatTaxationStrategy};

final class PriceCalculatorTest extends TestCase
{
    public function testRecalcTotals(): void
    {
        $order = new Order();
        $order->setCurrency(new Currency('USD'));

        $orderItem1 = new OrderItem($order, new Sku('SKU-1'), new Quantity(2), 1000); // $10 x2 = $20
        $orderItem2 = new OrderItem($order, new Sku('SKU-2'), new Quantity(1), 5000); // $50

        $calc = new PriceCalculator(new FlatPromotionStrategy(10), new FlatTaxationStrategy(0.2));
        $calc->recalc($order, [$orderItem1, $orderItem2]);

        // Base subtotal = 20000 + 5000 = 7000? (Oops amounts are cents: 1000 = $10)
        // Let's recompute: item1 base=1000*2=2000; item2 base=5000*1=5000; subtotal=7000 cents ($70)
        $this->assertSame(7000, $order->getSubtotal());

        // Discount 10%: item1=200; item2=500 -> 700
        $this->assertSame(700, $order->getDiscountTotal());

        // After discount: 6300; tax 20% => 1260
        $this->assertSame(1260, $order->getTaxTotal());

        // grandTotal = 7560 cents ($75.60)
        $this->assertSame(7560, $order->getGrandTotal());

        // Per item checks
        $this->assertSame(200, $orderItem1->getDiscount());
        $this->assertSame(360, $orderItem1->getTax()); // (2000-200)=1800 *20%
        $this->assertSame(2000-200+360, $orderItem1->getFinalPrice());

        $this->assertSame(500, $orderItem2->getDiscount());
        $this->assertSame(900, $orderItem2->getTax()); // (5000-500)=4500 *20%
        $this->assertSame(5000-500+900, $orderItem2->getFinalPrice());
    }
}
