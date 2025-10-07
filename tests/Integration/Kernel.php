<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Integration;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Migrations\DoctrineMigrationsBundle;
use OrderComponent\OrderComponentBundle;

final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineMigrationsBundle(),
            new OrderComponentBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['secret' => 'test', 'test' => true, 'http_method_override' => false]);
        $container->extension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.project_dir%/var/data.db'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => [
                    'OrderComponent' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/src/Entity',
                        'prefix' => 'OrderComponent\Entity',
                    ]
                ]
            ]
        ]);
        $container->extension('doctrine_migrations', [
            'migrations_paths' => ['DoctrineMigrations' => '%kernel.project_dir%/migrations']
        ]);
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 3);
    }
}
