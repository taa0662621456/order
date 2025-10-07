<?php
declare(strict_types=1);
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c): void {
    $c->extension('api_platform', [
        'mapping' => [
            'paths' => ['%kernel.project_dir%/src/Entity', '%kernel.project_dir%/src/Api']
        ],
        'formats' => [
            'jsonld' => ['mime_types' => ['application/ld+json']],
            'json' => ['mime_types' => ['application/json']]
        ]
    ]);
};
