<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Command\GenerateOrdersCommand;
use OrderComponent\Command\ListOrdersCommand;

return static function (ContainerConfigurator $config): void {
    $services = $config->services()->defaults()->autowire()->autoconfigure();

    $services->set(GenerateOrdersCommand::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->tag('console.command');

    $services->set(ListOrdersCommand::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->tag('console.command');
};
