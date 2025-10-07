<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Psr\Log\LoggerInterface;

final class TraceContextSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => 'onRequest'];
    }

    public function onRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        $traceId = $req->headers->get('x-trace-id') ?? bin2hex(random_bytes(8));
        $req->attributes->set('trace_id', $traceId);
        $this->logger->info('[trace.ctx]', ['trace_id' => $traceId, 'path' => $req->getPathInfo()]);
    }
}
