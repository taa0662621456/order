<?php
namespace App\Service;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SitemapGenerator
{
    public function __construct(
        private readonly RouterInterface $router
    ) {}

    public function generate(): string
    {
        $routes = $this->router->getRouteCollection();
        $host = $_ENV['APP_URL'] ?? 'http://localhost';

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($routes as $name => $route) {
            if ($this->isRoutePublic($route)) {
                $url = $xml->addChild('url');
                $url->addChild('loc', $host . $this->router->generate($name));
                $url->addChild('changefreq', 'daily');
                $url->addChild('priority', '0.5');
            }
        }

        return $xml->asXML();
    }

    private function isRoutePublic(Route $route): bool
    {
        $path = $route->getPath();

        // Игнорируем служебные маршруты
        if (str_starts_with($path, '/_') || str_starts_with($path, '/admin')) {
            return false;
        }

        return true;
    }
}
