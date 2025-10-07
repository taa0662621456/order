<?php
declare(strict_types=1);

namespace OrderComponent\Infrastructure\Monitoring;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\Detectors\SdkProvided;
use OpenTelemetry\SemConv\ResourceAttributes;
use Symfony\Component\HttpFoundation\Response;

final class TraceContextSubscriber implements EventSubscriberInterface
{
    private TracerInterface $tracer;

    public function __construct(string $serviceName = 'order-component', string $otlpEndpoint = 'http://otel-collector:4318/v1/traces')
    {
        $resource = ResourceInfo::merge(ResourceInfo::create([ResourceAttributes::SERVICE_NAME => $serviceName]), (new SdkProvided())->getResource());
        $exporter = new SpanExporter($otlpEndpoint, null, ['Content-Type' => 'application/x-protobuf']);
        $provider = TracerProvider::builder()->addSpanProcessor(new SimpleSpanProcessor($exporter))->setResource($resource)->build();
        $this->tracer = $provider->getTracer('order-component');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => ['onRequest', 256],
            ResponseEvent::class => ['onResponse', -256],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $req = $event->getRequest();
        $span = $this->tracer->spanBuilder($req->getMethod().' '.$req->getPathInfo())->setSpanKind(SpanKind::KIND_SERVER)->startSpan();
        $scope = $span->activate();
        $req->attributes->set('_otel_span', $span);
        $req->attributes->set('_otel_scope', $scope);
    }

    public function onResponse(ResponseEvent $event): void
    {
        $req = $event->getRequest();
        $span = $req->attributes->get('_otel_span');
        $scope = $req->attributes->get('_otel_scope');
        if ($span && $scope) {
            $res = $event->getResponse();
            if ($res instanceof Response) {
                $span->setAttribute('http.status_code', $res->getStatusCode());
            }
            $scope->detach();
            $span->end();
        }
    }
}
