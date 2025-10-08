<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Service\Tx\TransactionMiddleware;
use OrderComponent\Entity\Order;

final class TransactionMiddlewareTest extends TestCase
{
    private static KernelInterface $kernel;
    public static function setUpBeforeClass(): void { self::$kernel = new TestKernel('test', true); self::$kernel->boot(); }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    /**
     * @throws \Throwable
     */
    public function testRollbackOnException(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $tool = new SchemaTool($em);
        $tool->dropDatabase();
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());

        /** @var TransactionMiddleware $tx */
        $tx = $c->get(TransactionMiddleware::class);
        try {
            $tx->run(function(EntityManagerInterface $em){
                $o = new Order();
                $em->persist($o);
                throw new RuntimeException('boom');
            });
            $this->fail('Exception expected');
        } catch (RuntimeException $e) { /* ok */ }

        $count = (int)$em->createQuery('SELECT COUNT(o.id) FROM OrderComponent\Entity\Order o')->getSingleScalarResult();
        $this->assertSame(0, $count, 'Order must not be persisted after rollback');
    }
}
