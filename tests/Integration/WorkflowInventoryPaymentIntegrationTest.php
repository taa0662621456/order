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
use OrderComponent\Service\Inventory\InMemoryInventoryService;
use OrderComponent\ValueObject\Order\OrderStatus;

final class WorkflowInventoryPaymentIntegrationTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testPlaceAndPayFlow(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $order = new Order();
        $order->setCurrency(new Currency('USD'));
        $em->persist($order);

        $item1 = new OrderItem($order, new Sku('SKU-1'), new Quantity(2), 1000); // $20
        $item2 = new OrderItem($order, new Sku('SKU-2'), new Quantity(1), 5000); // $50
        $em->persist($item1); $em->persist($item2); $em->flush();

        /** @var OrderWorkflowService $svc */
        $svc = $c->get(OrderWorkflowService::class);
        $svc->place($order, [$item1, $item2]);

        // inventory reserved
        /** @var InMemoryInventoryService $inv */
        $inv = $c->get(InMemoryInventoryService::class);
        $this->assertSame(2, $inv->getReserved('SKU-1'));
        $this->assertSame(1, $inv->getReserved('SKU-2'));

        // pay full amount (7000 cents)
        $svc->pay($order, 7000);
        $em->refresh($order);

        $this->assertSame(OrderStatus::Paid->value, $order->getStatus()->value);

        $paidCount = (int)$em->createQuery('SELECT COUNT(p.id) FROM OrderComponent\Entity\Order\OrderPayment p WHERE p.status = :s')
            ->setParameter('s','paid')->getSingleScalarResult();
        $this->assertSame(1, $paidCount);
    }
}
