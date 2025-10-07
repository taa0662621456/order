<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Outbox\{OutboxPublisher, OutboxMessengerDispatcher};
use OrderComponent\Command\OutboxDispatchCommand;
use OrderComponent\MessageHandler\OrderEventMessageHandler;

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    $s->set(OutboxPublisher::class)->arg(0, new Reference('doctrine.orm.entity_manager'));
    $s->set(OutboxMessengerDispatcher::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference('messenger.default_bus'));

    $s->set(OutboxDispatchCommand::class)
        ->arg(0, new Reference(OutboxMessengerDispatcher::class))
        ->tag('console.command');

    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('doctrine.orm.entity_manager'))
        ->arg(2, new Reference(OutboxPublisher::class));

    $s->set(OrderEventMessageHandler::class)->tag('messenger.message_handler');
};
