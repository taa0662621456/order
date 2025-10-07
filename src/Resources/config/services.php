<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Subscriber\Order\InventorySubscriber;
use OrderComponent\Subscriber\Order\EmailSubscriber;
use OrderComponent\Subscriber\Order\AnalyticsSubscriber;
use OrderComponent\Service\Outbox\OutboxProcessor;
use OrderComponent\Command\WorkflowTestCommand;

return static function (ContainerConfigurator $config): void {
    $services = $config->services()->defaults()->autowire()->autoconfigure();

    $services->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order')) # WorkflowInterface
        ->arg(1, new Reference('event_dispatcher'))
        ->arg(2, new Reference('doctrine.orm.entity_manager'));

    $services->set(InventorySubscriber::class)->tag('kernel.event_subscriber');
    $services->set(EmailSubscriber::class)->tag('kernel.event_subscriber');
    $services->set(AnalyticsSubscriber::class)->tag('kernel.event_subscriber');

    $services->set(OutboxProcessor::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference('event_dispatcher'));

    $services->set(WorkflowTestCommand::class)
        ->arg(0, new Reference(OrderWorkflowService::class))
        ->tag('console.command');
};
