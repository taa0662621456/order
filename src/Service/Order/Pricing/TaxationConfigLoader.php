<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use Symfony\Component\Yaml\Yaml;
use OrderComponent\ValueObject\Order\TaxRate;

final class TaxationConfigLoader
{
    private array $config;

    public function __construct(string $path)
    {
        $this->config = Yaml::parseFile($path)['taxation'] ?? [];
    }

    public function rounding(): int
    {
        return (int)($this->config['rounding'] ?? 2);
    }

    public function defaultCurrency(): string
    {
        return (string)($this->config['default_currency'] ?? 'USD');
    }

    public function rateFor(string $region, ?string $subregion = null): TaxRate
    {
        $rates = $this->config['rates'] ?? [];
        if (isset($rates[$region])) {
            if ($subregion && isset($rates[$region][$subregion])) {
                return new TaxRate((string)$rates[$region][$subregion]);
            }
            if (isset($rates[$region]['default'])) {
                return new TaxRate((string)$rates[$region]['default']);
            }
        }
        return new TaxRate('0');
    }
}
