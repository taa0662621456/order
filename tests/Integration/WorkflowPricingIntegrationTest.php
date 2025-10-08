<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderItem;
use OrderComponent\ValueObject\Money\Currency;
use OrderComponent\ValueObject\Order\Sku;
use OrderComponent\ValueObject\Order\Quantity;
use OrderComponent\Service\Order\OrderWorkflowService;

final class WorkflowPricingIntegrationTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testPlaceWithPricing(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $order = new Order();
        $order->setCurrency(new Currency('USD'));
        $em->persist($order);

        $item1 = new OrderItem($order, new Sku('SKU-1'), new Quantity(2), 1000); // $10 x2 = $20 => 2000 cents
        $item2 = new OrderItem($order, new Sku('SKU-2'), new Quantity(1), 5000); // $50 => 5000 cents
        $em->persist($item1); $em->persist($item2); $em->flush();

        /** @var OrderWorkflowService $svc */
        $svc = $c->get(OrderWorkflowService::class);
        $svc->place($order, [$item1, $item2]);
        $em->refresh($order);

        // subtotal = 7000; discount 10% = 700; after = 6300; tax 20% = 1260; grand = 7560
        $this->assertSame(7000, $order->getSubtotal());
        $this->assertSame(700, $order->getDiscountTotal());
        $this->assertSame(1260, $order->getTaxTotal());
        $this->assertSame(7560, $order->getGrandTotal());
    }
}
