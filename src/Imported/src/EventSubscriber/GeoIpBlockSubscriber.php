<?php

namespace App\EventSubscriber;

use App\Service\GeoIpService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GeoIpBlockSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly GeoIpService $geoIpService,
        private readonly array $allowedCountries = ['US', 'CA', 'UA'] // можно вынести в config
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 200], // срабатывает раньше контроллера
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();

        // проверяем только API-запросы
        if (!str_starts_with($req->getPathInfo(), '/api')) {
            return;
        }

        $ip = $req->getClientIp();

        if (!$this->geoIpService->isAllowedCountry($ip, $this->allowedCountries)) {
            $geo = $this->geoIpService->getLocation($ip);

            $res = new JsonResponse([
                'error'   => 'ACCESS_DENIED',
                'message' => 'Your country is not allowed to access this API',
                'geo'     => $geo,
            ], 403);

            $event->setResponse($res);
        }
    }
}
