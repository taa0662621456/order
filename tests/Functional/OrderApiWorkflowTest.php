<?php
declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderApiWorkflowTest extends WebTestCase
{
    public function test_full_flow_create_pay_ship_refund(): void
    {
        $client = static::createClient();

        // 1) Create order
        $client->request(
            'POST',
            '/order',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['currency' => 'USD', 'grandTotal' => '100.00'], JSON_THROW_ON_ERROR)
        );
        self::assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $orderId = $data['id'] ?? null;
        self::assertNotEmpty($orderId);

        // 2) Partial pay 40
        $client->request(
            'POST',
            '/order/'.$orderId.'/pay',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['amount' => '40.00', 'externalRef' => 'r1'], JSON_THROW_ON_ERROR)
        );
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('partially_paid', $data['status']);

        // 3) Pay remaining 60
        $client->request(
            'POST',
            '/order/'.$orderId.'/pay',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['amount' => '60.00', 'externalRef' => 'r2'], JSON_THROW_ON_ERROR)
        );
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('paid', $data['status']);
        self::assertSame('100.00', $data['paidTotal']);

        // 4) Ship 1 item (transitions to partially_shipped)
        $client->request(
            'POST',
            '/order/'.$orderId.'/ship',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['count' => 1, 'note' => 'box-1'], JSON_THROW_ON_ERROR)
        );
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('partially_shipped', $data['status']);

        // 5) Partial refund 25
        $client->request(
            'POST',
            '/order/'.$orderId.'/refund',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['amount' => '25.00', 'reason' => 'damage'], JSON_THROW_ON_ERROR)
        );
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('partially_refunded', $data['status']);

        // 6) Get order
        $client->request('GET', '/order/'.$orderId);
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('USD', $data['currency']);
    }
}
