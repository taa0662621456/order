<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Tx\TransactionMiddleware;
use OrderComponent\Service\Outbox\OutboxProcessor;
use OrderComponent\Service\Outbox\IdempotencyGuard;
use OrderComponent\Command\OutboxReplayCommand;

return static function (ContainerConfigurator $config): void {
    $s = $config->services()->defaults()->autowire()->autoconfigure();

    $s->set(TransactionMiddleware::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'));

    $s->set(IdempotencyGuard::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'));

    $s->set(OutboxProcessor::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference(IdempotencyGuard::class))
        ->arg(2, new Reference('logger', null));

    $s->set(OutboxReplayCommand::class)
        ->arg(0, new Reference(OutboxProcessor::class))
        ->tag('console.command');
};
