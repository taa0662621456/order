<?php
declare(strict_types=1);
namespace App\Service\Payment;
use App\Entity\Payment\Payment;

final class ProviderRegistry
{
    /** @param array<string, PaymentProviderInterface> $providers */
    public function __construct(private readonly array $providers = []) {}

    public function get(string $name): ?PaymentProviderInterface
    {
        return $this->providers[$name] ?? null;
    }
}
