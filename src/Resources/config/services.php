<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Inventory\{InventoryServiceInterface, InMemoryInventoryService};
use OrderComponent\Service\Payment\{PaymentGatewayInterface, StripeGateway, PaymentProcessorService};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    // Inventory
    $s->set(InMemoryInventoryService::class);
    $s->alias(InventoryServiceInterface::class, InMemoryInventoryService::class);

    // Payment
    $s->set(StripeGateway::class);
    $s->alias(PaymentGatewayInterface::class, StripeGateway::class);
    $s->set(PaymentProcessorService::class)
        ->arg(0, service(PaymentGatewayInterface::class))
        ->arg(1, new Reference('doctrine.orm.entity_manager'));

    // Workflow Service
    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('doctrine.orm.entity_manager'))
        ->arg(2, service(InventoryServiceInterface::class))
        ->arg(3, service(PaymentProcessorService::class));
};
