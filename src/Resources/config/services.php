<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\Service\Shipment\{CarrierInterface, UPSCarrier, ShipmentProcessorService};
use OrderComponent\Service\Payment\{PaymentGatewayInterface, StripeGateway, PaymentProcessorService};
use OrderComponent\Api\State\OrderDataPersister;
use OrderComponent\Api\Controller\{OrderPayController, OrderShipController};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    // Shipment
    $s->set(UPSCarrier::class);
    $s->alias(CarrierInterface::class, UPSCarrier::class);
    $s->set(ShipmentProcessorService::class)
        ->arg(0, service(CarrierInterface::class))
        ->arg(1, new Reference('doctrine.orm.entity_manager'));

    // Payment
    $s->set(StripeGateway::class);
    $s->alias(PaymentGatewayInterface::class, StripeGateway::class);
    $s->set(PaymentProcessorService::class)
        ->arg(0, service(PaymentGatewayInterface::class))
        ->arg(1, new Reference('doctrine.orm.entity_manager'));

    // Workflow
    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('doctrine.orm.entity_manager'))
        ->arg(2, service(ShipmentProcessorService::class))
        ->arg(3, service(PaymentProcessorService::class));

    // API Platform
    $s->set(OrderDataPersister::class)->tag('api_platform.state_processor');
    $s->set(OrderPayController::class)->public();
    $s->set(OrderShipController::class)->public();
};
