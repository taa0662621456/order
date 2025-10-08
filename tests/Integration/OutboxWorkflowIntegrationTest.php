<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Outbox\OutboxProcessor;

final class OutboxWorkflowIntegrationTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    /**
     * @throws \JsonException
     */
    public function testOutboxToAnalytics(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $o = new Order();
        $em->persist($o); $em->flush();

        /** @var OrderWorkflowService $wf */
        $wf = $c->get(OrderWorkflowService::class);
        $wf->place($o);
        $wf->pay($o); // writes OrderPaidEvent into outbox

        /** @var OutboxProcessor $proc */
        $proc = $c->get(OutboxProcessor::class);
        $processed = $proc->process();
        $this->assertGreaterThanOrEqual(2, $processed, 'At least 2 events processed (placed + paid)');

        $count = (int)$em->createQuery('SELECT COUNT(a) FROM OrderComponent\Entity\Analytics\AnalyticsRecord a')->getSingleScalarResult();
        $this->assertSame(1, $count, 'Analytics record must be created on OrderPaidEvent');
    }
}
