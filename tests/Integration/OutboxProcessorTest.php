<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Service\Outbox\OutboxProcessor;
use OrderComponent\Service\Outbox\IdempotencyGuard;
use OrderComponent\Entity\Outbox\OutboxMessage;

final class OutboxProcessorTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testIdempotencyAndDeadLetter(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        // Seed two identical messages (same event/payload) and one failing message
        $m1 = new OutboxMessage('OrderPaidEvent', '{"orderId":1}');
        $m2 = new OutboxMessage('OrderPaidEvent', '{"orderId":1}');
        $mf = new OutboxMessage('OrderShippedEvent', '{"orderId":2}');
        $em->persist($m1); $em->persist($m2); $em->persist($mf); $em->flush();

        /** @var OutboxProcessor $proc */
        $proc = $c->get(OutboxProcessor::class);

        $dispatchCount = 0;
        $processed = $proc->replay(50,
            function(OutboxMessage $m) use (&$dispatchCount) {
                if ($m->getEventName() === 'OrderShippedEvent') {
                    throw new \RuntimeException('simulate failure');
                }
                return (object)['name'=>$m->getEventName(), 'payload'=>$m->getPayload()];
            },
            function(object $e) use (&$dispatchCount) { $dispatchCount++; },
            2 // max retries
        );
        $this->assertSame(2, $processed, 'Two messages processed (duplicates collapsed to one + failure skipped)');

        // Re-run to cause retries and dead-letter
        $proc->replay(50,
            fn(OutboxMessage $m) => throw new \RuntimeException('fail again'),
            fn(object $e) => null,
            2
        );
        $dead = (int)$em->createQuery('SELECT COUNT(m.id) FROM OrderComponent\\Entity\\Outbox\\OutboxMessage m WHERE m.failedAt IS NOT NULL')->getSingleScalarResult();
        $this->assertSame(1, $dead, 'One message dead-lettered');

        $this->assertGreaterThanOrEqual(1, $dispatchCount, 'At least one dispatch occurred');
    }
}
