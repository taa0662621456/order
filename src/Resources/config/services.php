<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use OrderComponent\Service\Order\OrderWorkflowService;
use OrderComponent\MessageHandler\OrderMessageHandler;
use OrderComponent\Middleware\IdempotencyMiddleware;

return static function (ContainerConfigurator $config): void {
    $s = $config->services()->defaults()->autowire()->autoconfigure();

    $s->set(OrderWorkflowService::class)
        ->arg(0, new Reference('workflow.order'))
        ->arg(1, new Reference('messenger.default_bus'))
        ->arg(2, new Reference('doctrine.orm.entity_manager'));

    $s->set(OrderMessageHandler::class)->tag('messenger.message_handler');

    $s->set(IdempotencyMiddleware::class)->tag('messenger.middleware');
};
