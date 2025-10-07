<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class DomainEventPublisher
{
    /** @var array<array{string, Event}> */
    private array $events = [];

    public function __construct(private readonly EventDispatcherService $dispatcher, private readonly LoggerInterface $logger) {}

    public function record(string $eventName, Event $event): void
    {
        $this->events[] = [$eventName, $event];
    }

    public function flush(): void
    {
        foreach ($this->events as [$name, $event]) {
            $this->dispatcher->dispatch($name, $event);
        }
        $this->logger->info('Domain events flushed', ['count' => count($this->events)]);
        $this->events = [];
    }
}
