<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Entity\Outbox\IdempotencyKey;
use OrderComponent\Message\OrderMessage;

final class MessengerIntegrationTest extends TestCase
{
    private static KernelInterface $kernel;

    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testPublishAndIdempotency(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        /** @var OrderWorkflowService $svc */
        $svc = $c->get(OrderWorkflowService::class);

        $o = new Order();
        $em->persist($o); $em->flush();

        $svc->place($o); // should publish OrderPlaced → handled synchronously in test via sync transport
        // publish duplicate
        $bus = $c->get('messenger.default_bus');
        $bus->dispatch(new OrderMessage('OrderComponent\\Event\\Order\\OrderPlacedEvent', $o->getId()));

        $count = (int)$em->createQuery('SELECT COUNT(k.key) FROM OrderComponent\\Entity\\Outbox\\IdempotencyKey k')->getSingleScalarResult();
        $this->assertSame(1, $count, 'Idempotency stored only once');
    }
}
