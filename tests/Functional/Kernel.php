<?php
declare(strict_types=1);
namespace OrderComponent\Tests\Functional;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OrderComponent\OrderComponentBundle;
use ApiPlatform\Symfony\Bundle\ApiPlatformBundle;
use Nelmio\CorsBundle\NelmioCorsBundle;

final class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    { return [ new FrameworkBundle(), new DoctrineBundle(), new ApiPlatformBundle(), new NelmioCorsBundle(), new OrderComponentBundle() ]; }

    protected function configureContainer(ContainerConfigurator $c): void
    {
        $c->import('%kernel.project_dir%/config/packages/api_platform.php');
        $c->extension('framework', ['secret'=>'test', 'test'=>true, 'http_method_override'=>false]);
        $c->extension('doctrine', [
            'dbal' => ['url' => 'sqlite:///%kernel.cache_dir%/test.db'],
            'orm' => [
                'auto_mapping' => true,
                'mappings' => {
                    'OrderComponent': {
                        'is_bundle': False,
                        'type': 'attribute',
                        'dir': '%kernel.project_dir%/src/Entity',
                        'prefix': 'OrderComponent\\Entity'
                    }
                }
            ]
        ]);
        // Workflow
        $c->extension('framework', [
            'workflows' => [
                'order' => [
                    'type'=>'state_machine',
                    'supports'=>['OrderComponent\Entity\Order'],
                    'initial_marking'=>'draft',
                    'places'=>['draft','placed','paid','shipped','completed','cancelled','refunded'],
                    'transitions'=>[
                        'pay'=>['from'=>'placed','to'=>'paid'],
                        'ship'=>['from'=>'paid','to'=>'shipped'],
                        'complete'=>['from'=>'shipped','to'=>'completed']
                    ]
                ]
            ]
        ]);
    }
    public function getProjectDir(): string { return \dirname(__DIR__, 3); }
}
