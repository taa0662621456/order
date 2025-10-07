<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

interface ExchangeRateProviderInterface
{
    public function getRate(string $from, string $to): float;
    public function getBase(): string;
}
