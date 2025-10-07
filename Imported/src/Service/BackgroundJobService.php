<?php
declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class BackgroundJobService
{
    public function __construct(private readonly MessageBusInterface $bus, private readonly LoggerInterface $logger) {}

    public function dispatch(object $message): void
    {
        try {
            $this->bus->dispatch($message);
        } catch (\Throwable $e) {
            $this->logger->error('Job dispatch failed', ['message' => get_class($message), 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
