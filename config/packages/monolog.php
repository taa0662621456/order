<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c): void {
    $c->extension('monolog', [
        'handlers' => [
            'main' => [
                'type' => 'stream',
                'path' => '%kernel.project_dir%/var/log/app.log',
                'level' => 'info'
            ],
            'order' => [
                'type' => 'stream',
                'path' => '%kernel.project_dir%/var/log/order.log',
                'level' => 'info',
                'channels' => ['order']
            ]
        ]
    ]);
};
