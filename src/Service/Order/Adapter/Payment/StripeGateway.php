<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Payment;

final class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(private ?string $apiKey = null) {}

    public function charge(string $orderId, string $amount, array $context = []): string
    {
        // Заглушка. Реальная интеграция через stripe-php SDK.
        return 'stripe_' . substr(hash('sha256', $orderId.$amount.microtime()), 0, 18);
    }
}
