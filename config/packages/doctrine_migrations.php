<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $c): void {
    $c->extension('doctrine_migrations', [
        'migrations_paths' => [
            'OrderComponent\Migrations' => '%kernel.project_dir%/migrations'
        ]
    ]);
};
