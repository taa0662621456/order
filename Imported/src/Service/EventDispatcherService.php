<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class EventDispatcherService
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly LoggerInterface $logger
    ) {}

    public function dispatch(string $eventName, Event $event): void
    {
        try {
            $this->dispatcher->dispatch($event, $eventName);
        } catch (\Throwable $e) {
            $this->logger->error('Event dispatch failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
