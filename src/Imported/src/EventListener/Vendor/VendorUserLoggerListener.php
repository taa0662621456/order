<?php

namespace App\EventListener\Vendor;

use Psr\Log\LoggerInterface;

class VendorUserLoggerListener
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function __invoke(object $event): void
    {
        $this->logger->info('VendorUser event received', ['event' => get_class($event)]);
    }
}
