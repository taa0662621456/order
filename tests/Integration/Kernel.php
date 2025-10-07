<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Zenstruck\Foundry\ZenstruckFoundryBundle;
use OrderComponent\OrderComponentBundle;

final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new ZenstruckFoundryBundle(),
            new OrderComponentBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['secret' => 'test', 'test' => true]);
        $container->extension('doctrine', [
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
                ]
            ]
        ]);
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 3);
    }
}
