<?php
declare(strict_types=1);

namespace Tests\Order\E2E;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PartialPaymentRefundFlowTest extends WebTestCase
{
    public function test_partial_payments_and_refund(): void
    {
        $c = static::createClient();

        // Place
        $payload = [
            'customerId' => 'CUST-2',
            'vendorId' => 'VEND-2',
            'currency' => 'USD',
            'items' => [['sku' => 'SKU-010', 'qty' => 1, 'price' => '30.00']],
        ];
        $c->request('POST', '/orders', server: ['CONTENT_TYPE'=>'application/json'], content: json_encode($payload));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,201,202], true), 'place');
        $data = json_decode($c->getResponse()->getContent(), true) ?: [];
        $id = $data['id'] ?? null;
        $this->assertNotEmpty($id, 'order id');

        // Partial pay #1
        $c->request('PATCH', "/orders/{$id}", server: ['CONTENT_TYPE'=>'application/merge-patch+json'], content: json_encode(['payAmount' => '10.00']));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,202], true), 'partial pay 1');

        // Partial pay #2
        $c->request('PATCH', "/orders/{$id}", server: ['CONTENT_TYPE'=>'application/merge-patch+json'], content: json_encode(['payAmount' => '15.00']));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,202], true), 'partial pay 2');

        // Refund 5.00
        $refund = ['amount' => '5.00', 'reason' => 'customer_request'];
        $c->request('POST', "/orders/{$id}/refund", server: ['CONTENT_TYPE'=>'application/json'], content: json_encode($refund));
        $this->assertTrue(in_array($c->getResponse()->getStatusCode(), [200,202,201], true), 'refund');
    }
}
