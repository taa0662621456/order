<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use OrderComponent\Service\Order\OrderPricing\PriceCalculator;
use OrderComponent\Service\Order\OrderPricing\Strategy\{PromotionStrategyInterface,TaxationStrategyInterface,FlatPromotionStrategy,FlatTaxationStrategy};

return static function (ContainerConfigurator $c): void {
    $s = $c->services()->defaults()->autowire()->autoconfigure();
    $s->set(FlatPromotionStrategy::class);
    $s->set(FlatTaxationStrategy::class);
    $s->set(PriceCalculator::class)
        ->arg('$promotion', service(FlatPromotionStrategy::class))
        ->arg('$taxation', service(FlatTaxationStrategy::class));
};
