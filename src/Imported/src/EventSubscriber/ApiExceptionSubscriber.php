<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Uid\Uuid;

readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private string          $env // передаём APP_ENV через DI
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        $req = $event->getRequest();
        if (!str_starts_with($req->getPathInfo(), '/api')) {
            return; // пропускаем не-API
        }

        $e = $event->getThrowable();
        $errorId = Uuid::v4()->toRfc4122();

        // Логируем
        $this->logger->error('API exception', [
            'error_id' => $errorId,
            'exception' => $e,
            'path' => $req->getPathInfo(),
        ]);

        $status = 500;
        $errorCode = 'INTERNAL_ERROR';
        $detail = null;

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $errorCode = match ($status) {
                404 => 'NOT_FOUND',
                403 => 'FORBIDDEN',
                401 => 'UNAUTHORIZED',
                default => 'HTTP_ERROR',
            };
            $detail = $e->getMessage();
        }

        $data = [
            'error' => $errorCode,
            'error_id' => $errorId,
        ];

        // В dev-режиме можно показать detail и trace
        if ($this->env === 'dev') {
            $data['detail'] = $e->getMessage();
            $data['trace'] = $e->getTraceAsString();
        } elseif ($detail) {
            $data['detail'] = $detail; // безопасные ошибки (404, 403)
        }

        $res = new JsonResponse($data, $status);
        $res->headers->set('Cache-Control', 'no-store, max-age=0');
        $event->setResponse($res);
    }
}
