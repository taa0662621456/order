<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use Symfony\Component\Yaml\Yaml;
use OrderComponent\ValueObject\Order\{Money, Currency};

final class CurrencyConversionService implements ExchangeRateProviderInterface
{
    private string $base;
    private array $rates;

    public function __construct(string $path)
    {
        $data = Yaml::parseFile($path)['exchange_rates'] ?? [];
        $this->base = (string)($data['base'] ?? 'USD');
        $this->rates = $data['rates'] ?? ['USD' => 1.0];
    }

    public function getBase(): string { return $this->base; }

    public function getRate(string $from, string $to): float
    {
        $from = strtoupper($from); $to = strtoupper($to);
        if ($from === $to) return 1.0;
        if (!isset($this->rates[$from]) || !isset($this->rates[$to])) {
            throw new \RuntimeException("Rate not found for $from or $to");
        }
        $usdAmount = 1.0 / (float)$this->rates[$from];
        return $usdAmount * (float)$this->rates[$to];
    }

    public function convert(Money $money, Currency $target, int $rounding = 2): Money
    {
        if ($money->getCurrency()->equals($target)) return $money;
        $rate = $this->getRate($money->getCurrency()->getCode(), $target->getCode());
        $converted = bcmul($money->getAmount(), (string)$rate, 6);
        return (new Money($converted, $target))->round($rounding);
    }
}
