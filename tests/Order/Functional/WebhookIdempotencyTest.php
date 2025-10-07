<?php
declare(strict_types=1);

namespace Tests\Order\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class WebhookIdempotencyTest extends WebTestCase
{
    public function test_payment_webhook_idempotent(): void
    {
        $c = static::createClient();
        $payload = ['orderId' => 'ORD-100', 'amount' => '5.00', 'method' => 'card'];
        $headers = ['HTTP_Idempotency-Key' => 'evt-123'];
        $c->request('POST', '/webhooks/payment', $payload, server: $headers, content: json_encode($payload));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [202,200], true));
        // repeat
        $c->request('POST', '/webhooks/payment', $payload, server: $headers, content: json_encode($payload));
        $this->assertSame(200, $c->getResponse()->getStatusCode(), 'duplicate accepted as no-op');
    }
}
