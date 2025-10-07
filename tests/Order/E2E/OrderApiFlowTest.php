<?php
declare(strict_types=1);

namespace Tests\Order\E2E;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderApiFlowTest extends WebTestCase
{
    public function test_place_pay_ship_flow(): void
    {
        $c = static::createClient();

        // Place
        $payload = [
            'customerId' => 'CUST-1',
            'vendorId' => 'VEND-1',
            'currency' => 'USD',
            'items' => [['sku' => 'SKU-001', 'qty' => 1, 'price' => '10.00']],
        ];
        $c->request('POST', '/orders', server: ['CONTENT_TYPE'=>'application/json'], content: json_encode($payload));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,201,202], true), 'place');
        $data = json_decode($c->getResponse()->getContent(), true) ?: [];
        $id = $data['id'] ?? null;
        $this->assertNotEmpty($id, 'order id');

        // Pay
        $c->request('PATCH', "/orders/{$id}", server: ['CONTENT_TYPE'=>'application/merge-patch+json'], content: json_encode(['payAmount' => '10.00']));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,202], true), 'pay');

        // Ship
        $c->request('PATCH', "/orders/{$id}", server: ['CONTENT_TYPE'=>'application/merge-patch+json'], content: json_encode(['shipCarrier' => 'UPS']));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,202], true), 'ship');
    }
}
