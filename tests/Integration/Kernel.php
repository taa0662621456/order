<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OrderComponent\OrderComponentBundle;

final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [ new FrameworkBundle(), new DoctrineBundle(), new OrderComponentBundle() ];
    }
    protected function configureContainer(ContainerConfigurator $c): void
    {
        $c->import('%kernel.project_dir%/config/packages/messenger.php');
        $c->import('%kernel.project_dir%/config/packages/test/messenger.php');
        $c->extension('framework', [
            'secret' => 'test', 'test' => true,
            'workflows' => [
                'order' => [
                    'type' => 'state_machine',
                    'supports' => ['OrderComponent\Entity\Order'],
                    'initial_marking' => 'draft',
                    'places' => ['draft','placed','paid','shipped','completed','cancelled','refunded'],
                    'transitions' => [
                        'place' => ['from' => 'draft', 'to' => 'placed'],
                        'pay' => ['from' => 'placed', 'to' => 'paid'],
                        'ship' => ['from' => 'paid', 'to' => 'shipped'],
                        'complete' => ['from' => 'shipped', 'to' => 'completed'],
                        'cancel' => ['from' => ['draft','placed'], 'to' => 'cancelled'],
                        'refund' => ['from' => 'paid', 'to' => 'refunded'],
                    ]
                ]
            ]
        ]);
        $c->extension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/test.db'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => {
                    'OrderComponent': {
                        'is_bundle': False,
                        'type': 'attribute',
                        'dir': '%kernel.project_dir%/src/Entity',
                        'prefix': 'OrderComponent\\Entity'
                    }
                }
            ]
        ]);
    }
    public function getProjectDir(): string { return \dirname(__DIR__, 3); }
}
