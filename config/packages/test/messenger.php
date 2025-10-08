<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c): void {
    $c->extension('framework', [
        'messenger' => [
            'transports' => [
                'sync' => ['dsn' => 'sync://']
            ],
            'routing' => [
                'OrderComponent\Message\OrderMessage' => 'sync'
            ]
        ]
    ]);
};
