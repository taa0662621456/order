<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Command\GenerateOrdersCommand;
use OrderComponent\Command\StatsOrdersCommand;
use OrderComponent\Command\ClearOrdersCommand;

final class OrderStatsCommandTest extends TestCase
{
    private static KernelInterface $kernel;

    public static function setUpBeforeClass(): void
    {
        self::$kernel = new TestKernel('test', true);
        self::$kernel->boot();
    }
    public static function tearDownAfterClass(): void { self::$kernel->shutdown(); }

    public function testStats(): void
    {
        $c = self::$kernel->getContainer();
        $em = $c->get(EntityManagerInterface::class);
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $app = new Application();
        $app->add($c->get(ClearOrdersCommand::class));
        $app->add($c->get(GenerateOrdersCommand::class));
        $app->add($c->get(StatsOrdersCommand::class));

        (new CommandTester($app->find('order:clear')))->execute([]);
        (new CommandTester($app->find('order:generate')))->execute([
            'count' => 3, '--status' => 'paid', '--with-payment' => true, '--amount' => 1000
        ]);
        (new CommandTester($app->find('order:generate')))->execute([
            'count' => 2, '--status' => 'draft'
        ]);

        $tester = new CommandTester($app->find('order:stats'));
        $tester->execute([]);
        $out = $tester->getDisplay();

        $this->assertStringContainsString('By status', $out);
        $this->assertStringContainsString('paid', $out);
        $this->assertStringContainsString('draft', $out);
        $this->assertStringContainsString('Total revenue: $3000', $out);
        $this->assertStringContainsString('Average amount per order: $600', $out);
    }
}
