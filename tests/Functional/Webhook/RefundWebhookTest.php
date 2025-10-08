<?php
declare(strict_types=1);

namespace Tests\Functional\Webhook;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RefundWebhookTest extends WebTestCase
{
    /**
     * @throws \JsonException
     */
    public function test_refund_webhook_happy_path(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/webhooks/refund',
            server: [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_X_PROVIDER' => 'mock',
                'HTTP_X_EVENT_ID' => 'evt_refund_1',
            ],
            content: json_encode([
                'intent' => 'pi_refund_123',
                'status' => 'succeeded',
                'currency' => 'USD',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
    }
}
