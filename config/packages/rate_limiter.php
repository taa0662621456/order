<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $c): void {
    $c->extension('framework', [
        'rate_limiter' => [
            'api' => ['policy'=>'token_bucket', 'limit'=>60, 'rate'=>['interval'=>'1 minute', 'amount'=>60]]
        ]
    ]);
};
