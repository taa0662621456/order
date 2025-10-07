<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Command\GenerateOrdersCommand;
use OrderComponent\Command\ListOrdersCommand;

final class ListOrdersCommandTest extends TestCase
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

    public function testListAfterGenerate(): void
    {
        $container = self::$kernel->getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // reset schema
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());

        $app = new Application();
        $app->add($container->get(GenerateOrdersCommand::class));
        $app->add($container->get(ListOrdersCommand::class));

        (new CommandTester($app->find('order:generate')))->execute(['count'=>3, '--status'=>'paid', '--with-payment'=>true]);
        $tester = new CommandTester($app->find('order:list'));
        $tester->execute([]);
        $out = $tester->getDisplay();
        $this->assertStringContainsString('Orders: 3', $out);
        $this->assertStringContainsString('Payments: 3, Total: $3000', $out);
    }
}
