<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;
use OrderComponent\Service\Order\OrderPricing\Strategy\{FlatPromotionStrategy,FlatTaxationStrategy};
use OrderComponent\Service\Inventory\{InventoryServiceInterface, InMemoryInventoryService};
use OrderComponent\Service\Payment\{PaymentGatewayInterface, StripeGateway, PaymentProcessorService};
use OrderComponent\Service\Shipment\{CarrierInterface, UPSCarrier, ShipmentProcessorService};
use OrderComponent\Service\Outbox\{OutboxPublisher, OutboxMessengerDispatcher};
use OrderComponent\Api\State\OrderDataPersister;
use OrderComponent\Api\Controller\{OrderPayController, OrderShipController};
use OrderComponent\MessageHandler\OrderEventMessageHandler;

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    // Pricing
    $s->set(FlatPromotionStrategy::class);
    $s->set(FlatTaxationStrategy::class);
    $s->set(PriceCalculator::class)
        ->arg('$promotion', service(FlatPromotionStrategy::class))
        ->arg('$taxation', service(FlatTaxationStrategy::class));

    // Inventory
    $s->set(InMemoryInventoryService::class);
    $s->alias(InventoryServiceInterface::class, InMemoryInventoryService::class);

    // Payment
    $s->set(StripeGateway::class);
    $s->alias(PaymentGatewayInterface::class, StripeGateway::class);
    $s->set(PaymentProcessorService::class)
        ->arg(0, service(PaymentGatewayInterface::class))
        ->arg(1, new Reference('doctrine.orm.entity_manager'));

    // Shipment
    $s->set(UPSCarrier::class);
    $s->alias(CarrierInterface::class, UPSCarrier::class);
    $s->set(ShipmentProcessorService::class)
        ->arg(0, service(CarrierInterface::class))
        ->arg(1, new Reference('doctrine.orm.entity_manager'));

    // Outbox + Messenger
    $s->set(OutboxPublisher::class)->arg(0, new Reference('doctrine.orm.entity_manager'));
    $s->set(OutboxMessengerDispatcher::class)
        ->arg(0, new Reference('doctrine.orm.entity_manager'))
        ->arg(1, new Reference('messenger.default_bus'));

    // Workflow + API
    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('doctrine.orm.entity_manager'))
        ->arg(2, service(ShipmentProcessorService::class))
        ->arg(3, service(PaymentProcessorService::class))
        ->arg(4, service(PriceCalculator::class))
        ->arg(5, service(InventoryServiceInterface::class))
        ->arg(6, service(OutboxPublisher::class));

    $s->set(OrderDataPersister::class)->tag('api_platform.state_processor');
    $s->set(OrderPayController::class)->public();
    $s->set(OrderShipController::class)->public();

    // Messenger handler
    $s->set(OrderEventMessageHandler::class)->tag('messenger.message_handler');
};
