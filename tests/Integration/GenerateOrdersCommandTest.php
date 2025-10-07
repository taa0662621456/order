<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Command\GenerateOrdersCommand;

final class GenerateOrdersCommandTest extends TestCase
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

    public function testGenerateOrders(): void
    {
        $container = self::$kernel->getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // reset schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $app = new Application();
        $app->add(new GenerateOrdersCommand());
        $tester = new CommandTester($app->find('order:generate'));
        $tester->execute(['count' => 3]);
        $display = $tester->getDisplay();

        $this->assertStringContainsString('Created 3 orders', $display);

        $count = (int)$em->createQuery('SELECT COUNT(o.id) FROM OrderComponent\\Entity\\Order o')->getSingleScalarResult();
        $this->assertSame(3, $count);
    }
}
