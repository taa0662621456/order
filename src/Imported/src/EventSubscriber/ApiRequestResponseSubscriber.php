<?php

namespace App\EventSubscriber;

use App\Entity\Vendor\VendorSecurity;
use App\Service\GeoIpService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiRequestResponseSubscriber implements EventSubscriberInterface
{
    private array $requestStartTimes = [];

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Security $security,
        private readonly GeoIpService $geoIpService,
        private readonly int $maxLogLength = 500,
        private readonly array $sensitiveKeys = ['password','token','access_token','refresh_token','authorization'],
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST  => ['onRequest', 100],
            KernelEvents::RESPONSE => ['onResponse', -100],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        if (!str_starts_with($req->getPathInfo(), '/api')) {
            return;
        }

        $requestId = uniqid('api_', true);
        $req->attributes->set('request_id', $requestId);
        $this->requestStartTimes[$requestId] = microtime(true);

        // trace_id / span_id
        $traceId = $req->headers->get('x-trace-id') ?? bin2hex(random_bytes(8));
        $spanId  = $req->headers->get('x-span-id') ?? bin2hex(random_bytes(4));
        $req->attributes->set('trace_id', $traceId);
        $req->attributes->set('span_id', $spanId);

        $ip  = $req->getClientIp();
        $geo = $this->geoIpService->getLocation($ip);

        $this->logger->info('API request', [
            'request_id' => $requestId,
            'trace_id'   => $traceId,
            'span_id'    => $spanId,
            'user'       => $this->getUserInfo(),
            'ip'         => $ip,
            'geo'        => $geo,
            'user_agent' => $req->headers->get('User-Agent', 'unknown'),
            'method'     => $req->getMethod(),
            'path'       => $req->getPathInfo(),
            'query'      => $req->query->all(),
            'headers'    => $this->maskSensitiveHeaders($req->headers->all()),
            'body'       => $this->truncate($this->maskSensitiveBody($req->getContent())),
        ]);
    }

    public function onResponse(ResponseEvent $event): void
    {
        $req = $event->getRequest();
        if (!str_starts_with($req->getPathInfo(), '/api')) {
            return;
        }

        $res = $event->getResponse();
        $requestId = $req->attributes->get('request_id', 'unknown');
        $traceId   = $req->attributes->get('trace_id', null);
        $spanId    = $req->attributes->get('span_id', null);

        $duration = null;
        if (isset($this->requestStartTimes[$requestId])) {
            $duration = round((microtime(true) - $this->requestStartTimes[$requestId]) * 1000, 2);
            unset($this->requestStartTimes[$requestId]);
        }

        $ip  = $req->getClientIp();
        $geo = $this->geoIpService->getLocation($ip);

        // добавляем заголовки в ответ
        if ($traceId) {
            $res->headers->set('X-Trace-Id', $traceId);
        }
        if ($spanId) {
            $res->headers->set('X-Span-Id', $spanId);
        }

        $this->logger->info('API response', [
            'request_id' => $requestId,
            'trace_id'   => $traceId,
            'span_id'    => $spanId,
            'user'       => $this->getUserInfo(),
            'ip'         => $ip,
            'geo'        => $geo,
            'user_agent' => $req->headers->get('User-Agent', 'unknown'),
            'status'     => $res->getStatusCode(),
            'headers'    => $this->maskSensitiveHeaders($res->headers->all()),
            'content'    => $this->truncate($this->maskSensitiveBody($res->getContent())),
            'duration_ms'=> $duration,
        ]);
    }

    private function getUserInfo(): string
    {
        $user = $this->security->getUser();
        if ($user instanceof VendorSecurity) {
            return sprintf('vendor:%s', $user->getSlug());
        }
        return $user ? $user->getUserIdentifier() : 'anon';
    }

    private function maskSensitiveHeaders(array $headers): array
    {
        $sensitive = ['authorization', 'cookie', 'set-cookie', 'x-api-key'];
        foreach ($sensitive as $key) {
            if (isset($headers[$key])) {
                $headers[$key] = ['***'];
            }
        }
        return $headers;
    }

    private function maskSensitiveBody(string $body): string
    {
        if (empty($body)) {
            return $body;
        }

        $decoded = json_decode($body, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $keys = array_map('strtolower', $this->sensitiveKeys);
            array_walk_recursive($decoded, function (&$value, $key) use ($keys) {
                if (in_array(strtolower($key), $keys, true)) {
                    $value = '***';
                }
            });
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        return $body;
    }

    private function truncate(?string $data): ?string
    {
        if ($data === null) {
            return null;
        }
        if (mb_strlen($data) > $this->maxLogLength) {
            return mb_substr($data, 0, $this->maxLogLength) . '... [truncated]';
        }
        return $data;
    }
}
