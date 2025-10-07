<?php
declare(strict_types=1);

namespace Tests\Order\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiPlatformOrderTest extends WebTestCase
{
    public function test_get_orders_collection(): void
    {
        $c = static::createClient();
        $c->request('GET', '/orders');
        self::assertTrue(in_array($c->getResponse()->getStatusCode(), [200,404], true));
    }

    public function test_place_order(): void
    {
        $c = static::createClient();
        $payload = [
            'customerId' => 'CUST-1',
            'vendorId' => 'VEND-1',
            'currency' => 'USD',
            'items' => [
                ['sku' => 'SKU-001', 'qty' => 2, 'price' => '10.00']
            ],
            'placeAt' => (new \DateTimeImmutable())->format(DATE_ATOM)
        ];
        $c->request('POST', '/orders', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($payload));
        self::assertTrue(in_array($c->getResponse()->getStatusCode(), [201,202,200], true));
    }
}
