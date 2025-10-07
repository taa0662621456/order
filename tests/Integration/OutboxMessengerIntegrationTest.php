<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Outbox\OutboxMessengerDispatcher;

final class OutboxMessengerIntegrationTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testWorkflowToQueueToHandler(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $o = new Order();
        $em->persist($o); $em->flush();

        /** @var OrderWorkflowService $wf */
        $wf = $c->get(OrderWorkflowService::class);
        $wf->place($o);
        $wf->pay($o); // writes to outbox

        /** @var OutboxMessengerDispatcher $disp */
        $disp = $c->get(OutboxMessengerDispatcher::class);
        $dispatched = $disp->dispatchPending();
        $this->assertGreaterThanOrEqual(2, $dispatched);

        /** @var InMemoryTransport $transport */
        $transport = $c->get('messenger.transport.async');
        $this->assertGreaterThanOrEqual(2, count($transport->getSent())); // messages in queue
    }
}
