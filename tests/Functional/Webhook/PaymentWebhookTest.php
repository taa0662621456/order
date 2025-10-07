<?php
declare(strict_types=1);

namespace Tests\Functional\Webhook;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PaymentWebhookTest extends WebTestCase
{
    public function test_payment_webhook_happy_path(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/webhooks/payment',
            server: [
                'HTTP_CONTENT_TYPE' => 'application/json',
                'HTTP_X_PROVIDER' => 'mock',
                'HTTP_X_EVENT_ID' => 'evt_test_1',
            ],
            content: json_encode([
                'intent' => 'pi_abcdef123456',
                'status' => 'succeeded',
                'currency' => 'USD',
            ], JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('ok', $json['status'] ?? null);
    }

    public function test_payment_webhook_duplicate_is_ignored(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'intent' => 'pi_dup_123',
            'status' => 'succeeded',
            'currency' => 'USD',
        ], JSON_THROW_ON_ERROR);

        // first
        $client->request('POST', '/api/webhooks/payment', server: [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_PROVIDER' => 'mock',
            'HTTP_X_EVENT_ID' => 'evt_dup_1',
        ], content: $payload);
        self::assertResponseIsSuccessful();

        // second (same event id)
        $client->request('POST', '/api/webhooks/payment', server: [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_PROVIDER' => 'mock',
            'HTTP_X_EVENT_ID' => 'evt_dup_1',
        ], content: $payload);
        self::assertResponseIsSuccessful();
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('ignored', $json['status'] ?? null);
    }
}
