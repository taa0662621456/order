<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\DataFixtures\OrderFixtures;
use Doctrine\Bundle\FixturesBundle\Executor\ORMExecutor;
use Doctrine\Bundle\FixturesBundle\Purger\ORMPurger;

final class MigrationsAndFixturesTest extends TestCase
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

    public function testMigrationsAndFixtures(): void
    {
        $container = self::$kernel->getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // create schema fresh
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        // load fixtures
        $executor = new ORMExecutor($em, new ORMPurger($em));
        $executor->execute([new OrderFixtures()]);

        $count = (int)$em->createQuery('SELECT COUNT(o.id) FROM OrderComponent\Entity\Order o')->getSingleScalarResult();
        self::assertSame(2, $count);
    }
}
