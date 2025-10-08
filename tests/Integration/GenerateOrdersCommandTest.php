<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Doctrine\ORM\Tools\SchemaTool;
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

    public function testGenerateWithStatusSeedAndPayments(): void
    {
        $container = self::$kernel->getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // reset schema
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $app = new Application();
        $app->add($container->get(GenerateOrdersCommand::class));

        $tester = new CommandTester($app->find('order:generate'));
        $tester->execute(['count' => 5, '--status' => 'paid', '--seed' => 123, '--with-payment' => true]);
        $out = $tester->getDisplay();

        $this->assertStringContainsString('Created 5 orders with payments', $out);
        $this->assertStringContainsString('Status: paid', $out);
        $this->assertStringContainsString('Payment amount: $1000', $out);
        $this->assertStringContainsString('Payment total: $5000 (Общий платёж: $5000)', $out);

        // verify counts
        $orders = (int)$em->createQuery('SELECT COUNT(o.id) FROM OrderComponent\Entity\Order o')->getSingleScalarResult();
        $payments = (int)$em->createQuery('SELECT COUNT(p.id) FROM OrderComponent\Entity\Order\OrderPayment p')->getSingleScalarResult();
        $sum = (int)$em->createQuery('SELECT COALESCE(SUM(p.amount),0) FROM OrderComponent\Entity\Order\OrderPayment p')->getSingleScalarResult();

        $this->assertSame(5, $orders);
        $this->assertSame(5, $payments);
        $this->assertSame(5000, $sum);
    }
}
