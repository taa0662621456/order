<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c): void {
    $c->extension('framework', [
        'messenger' => [
            'transports' => [
                'order_outbox' => [
                    'dsn' => 'doctrine://default?queue_name=order_outbox',
                    'retry_strategy' => ['max_retries' => 3]
                ]
            ],
            'routing' => [
                'OrderComponent\\Message\\OrderMessage' => 'order_outbox'
            ],
            'buses' => [
                'messenger.bus.default' => [
                    'default_middleware' => 'allow_no_handlers',
                    'middleware' => ['OrderComponent\\Middleware\\IdempotencyMiddleware']
                ]
            ]
        ]
    ]);
};
