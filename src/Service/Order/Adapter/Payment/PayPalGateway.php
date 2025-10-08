<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Adapter\Payment;

final readonly class PayPalGateway implements PaymentGatewayInterface
{
    public function __construct(private ?string $clientId = null, private ?string $secret = null) {}

    public function charge(string $orderId, string $amount, array $context = []): string
    {
        return 'paypal_' . substr(hash('sha256', $orderId.$amount.microtime()), 0, 18);
    }

    public function refund(string $orderId, string $amount, array $context = []): string
    {
        return 're_' . substr(hash('sha256', $orderId.$amount.microtime()), 0, 18);
    }
}
