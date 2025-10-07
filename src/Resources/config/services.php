<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Outbox\{OutboxPublisher, OutboxMessengerDispatcher};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();
    $s->set(OutboxPublisher::class)->arg(0, new Reference('doctrine.orm.entity_manager'));
    $s->set(OutboxMessengerDispatcher::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference('messenger.default_bus'));
};
