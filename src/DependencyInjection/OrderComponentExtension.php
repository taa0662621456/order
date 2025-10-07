<?php
declare(strict_types=1);

namespace OrderComponent\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class OrderComponentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        // Base services for this bundle
        $loader->load('services.yaml');

        // Import any files we copied from the host project (kept under config/imported)
        $importedPath = \dirname(__DIR__, 2) . '/config/imported';
        if (is_dir($importedPath)) {
            // Load any *.yaml files inside imported config
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($importedPath));
            foreach ($iterator as $file) {
                if ($file->isFile() && preg_match('/\.(ya?ml)$/i', $file->getFilename())) {
                    $loader->load($file->getPathname());
                }
            }
        }
    }
}
