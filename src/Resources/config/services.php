<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;
use OrderComponent\Service\Order\OrderPricing\Strategy\{FlatPromotionStrategy,FlatTaxationStrategy};
use OrderComponent\Service\Order\OrderWorkflowService;

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();

    $s->set(FlatPromotionStrategy::class);
    $s->set(FlatTaxationStrategy::class);

    $s->set(PriceCalculator::class)
        ->arg('$promotion', service(FlatPromotionStrategy::class))
        ->arg('$taxation', service(FlatTaxationStrategy::class));

    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference(PriceCalculator::class))
        ->arg(2, new Reference('doctrine.orm.entity_manager'));
};
