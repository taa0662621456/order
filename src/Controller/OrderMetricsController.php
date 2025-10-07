<?php
declare(strict_types=1);
namespace OrderComponent\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderMetricsController
{
    #[Route('/metrics', name: 'metrics', methods: ['GET'])]
    public function __invoke(): Response
    {
        $content = "# HELP order_component_build_info Build info\n# TYPE order_component_build_info gauge\norder_component_build_info{version=\"0.1.0-alpha\"} 1\n";
        return new Response($content, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
    }
}
