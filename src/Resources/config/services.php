<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Outbox\{OutboxPublisher, OutboxProcessor};
use OrderComponent\Command\OutboxProcessCommand;
use OrderComponent\Subscriber\Order\{InventorySubscriber, EmailSubscriber, AnalyticsSubscriber};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    $s->set(OutboxPublisher::class)->arg(0, new Reference('doctrine.orm.entity_manager'));
    $s->set(OutboxProcessor::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference('event_dispatcher'));

    $s->set(OutboxProcessCommand::class)
        ->arg(0, new Reference(OutboxProcessor::class))
        ->tag('console.command');

    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('doctrine.orm.entity_manager'))
        ->arg(2, new Reference(OutboxPublisher::class));

    // Subscribers
    $s->set(InventorySubscriber::class)->tag('kernel.event_subscriber');
    $s->set(EmailSubscriber::class)->tag('kernel.event_subscriber');
    $s->set(AnalyticsSubscriber::class)->tag('kernel.event_subscriber');
};
