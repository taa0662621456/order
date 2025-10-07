<?php
declare(strict_types=1);

namespace App\Service\Address;

final class AddressFormatStrategyFactory
{
    /** @var array<string, AddressFormatStrategy> */
    private array $map = [];

    /**
     * @param iterable<AddressFormatStrategy> $strategies
     */
    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $short = (new \ReflectionClass($strategy))->getShortName();
            $this->map[$short] = $strategy;
        }
    }

    public function forCountry(string $countryCode): AddressFormatStrategy
    {
        return match (strtoupper($countryCode)) {
            'UA' => $this->map['UaAddressFormat'] ?? throw new \RuntimeException('UA formatter not registered'),
            'CA' => $this->map['CaAddressFormat'] ?? throw new \RuntimeException('CA formatter not registered'),
            'AU' => $this->map['AuAddressFormat'] ?? throw new \RuntimeException('AU formatter not registered'),
            default => $this->map['CaAddressFormat'] 
                ?? throw new \InvalidArgumentException('No formatter for ' . $countryCode),
        };
    }
}
