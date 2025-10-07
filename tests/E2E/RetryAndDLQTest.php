<?php
declare(strict_types=1);
namespace OrderComponent\Tests\E2E;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Worker;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Messenger\EventListener\StopWorkerOnMessageLimitListener;
use Psr\Log\NullLogger;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Outbox\{OutboxPublisher, OutboxMessengerDispatcher};
use OrderComponent\Event\Order\OrderShippedEvent;

final class RetryAndDLQTest extends TestCase
{
    private static KernelInterface $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TestKernel('test', true);
        self::$kernel->boot();
    }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testFailureGoesToDeadLetter(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $order = new Order(); $em->persist($order); $em->flush();
        /** @var OutboxPublisher $pub */ $pub = $c->get(OutboxPublisher::class);
        $pub->publish(OrderShippedEvent::class, ['orderId'=>$order->getId()]);
        $em->flush();

        /** @var OutboxMessengerDispatcher $disp */ $disp = $c->get(OutboxMessengerDispatcher::class);
        $this->assertSame(1, $disp->dispatchPending());

        /** @var InMemoryTransport $async */ $async = $c->get('messenger.transport.async');
        /** @var InMemoryTransport $failed */ $failed = $c->get('messenger.transport.failed');

        // Run worker to consume messages with retries; use container's event dispatcher so failure listener is active
        $bus = $c->get('messenger.default_bus');
        $worker = new Worker(['async' => $async], $bus, $c->get('event_dispatcher'), new NullLogger());
        $worker->run([new StopWorkerOnMessageLimitListener(4)]); // enough to attempt and move to failed

        $this->assertCount(0, $async->getSent(), 'Async queue should be drained');
        $this->assertGreaterThanOrEqual(1, count($failed->getSent()), 'Failed queue should contain the message');
    }
}
