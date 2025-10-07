<?php
declare(strict_types=1);

namespace Tests;

use Symfony\\Component\\HttpKernel\\Kernel as BaseKernel;
use Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle;
use Doctrine\\Bundle\\DoctrineBundle\\DoctrineBundle;
use Symfony\\Bundle\\MessengerBundle\\MessengerBundle;
use Symfony\\Component\\Config\\Loader\\LoaderInterface;
use Symfony\\Component\\DependencyInjection\\ContainerBuilder;
use Symfony\\Component\\DependencyInjection\\Loader\\YamlFileLoader;
use Symfony\\Component\\Config\\FileLocator;

final class Kernel extends BaseKernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new MessengerBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $confDir = __DIR__ . '/../config/packages';
        $loader->load(function (ContainerBuilder $container) use ($confDir) {
            $yaml = new YamlFileLoader($container, new FileLocator($confDir));
            $yaml->load('framework.yaml');
            $yaml->load('doctrine.yaml');
            $yaml->load('messenger.yaml');
            $container->setParameter('kernel.project_dir', dirname(__DIR__));
        });
    }
}
