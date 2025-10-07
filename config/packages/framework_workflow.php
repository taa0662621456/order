<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $c): void {
    $c->extension('framework', [
        'workflows' => [
            'order' => [
                'type'=>'state_machine',
                'supports'=>['OrderComponent\Entity\Order'],
                'initial_marking'=>'draft',
                'places'=>['draft','placed','paid','shipped','completed','cancelled','refunded'],
                'transitions'=>[
                    'place'=>['from'=>'draft','to'=>'placed'],
                    'pay'=>['from'=>'placed','to'=>'paid'],
                    'ship'=>['from'=>'paid','to'=>'shipped'],
                    'complete'=>['from'=>'shipped','to'=>'completed']
                ]
            ]
        ]
    ]);
};
