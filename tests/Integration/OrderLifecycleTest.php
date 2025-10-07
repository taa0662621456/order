<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Order\OrderService;
use OrderComponent\ValueObject\Order\OrderStatus;

final class OrderLifecycleTest extends TestCase
{
    private static KernelInterface $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TestKernel('test', true);
        self::$kernel->boot();
    }

    public static function tearDownAfterClass(): void
    {
        self::$kernel->shutdown();
    }

    public function testCreateAndTransition(): void
    {
        $container = self::$kernel->getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Ensure schema exists
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        $service = $container->get(OrderService::class);
        $order = new Order();
        $service->create($order);

        self::assertNotNull($order->getId());

        $service->transitionStatus($order, OrderStatus::Placed);
        self::assertSame(OrderStatus::Placed, $order->getStatus());
    }
}
