<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Controller\{OrderMetricsController, HealthCheckController};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();
    $s->set(OrderMetricsController::class)->public();
    $s->set(HealthCheckController::class)
        ->public()
        ->arg('$em', new Reference('doctrine.orm.entity_manager'))
        ->arg('$asyncTransport', new Reference('messenger.transport.async'));
};
