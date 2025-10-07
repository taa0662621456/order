<?php
declare(strict_types=1);

namespace Tests\E2E;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Tests\Fixtures\OrderFactory;

final class OrderE2EFlowTest extends WebTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    public function test_full_order_payment_refund_flow(): void
    {
        $client = static::createClient();

        // 1) Create Order via factory
        $factory = new OrderFactory($this->em);
        $order = $factory->create(149.90);

        // 2) Generate invoice (API): POST /api/orders/{id}/invoice
        $client->request('POST', sprintf('/api/orders/%d/invoice', $order->getId()));
        self::assertResponseIsSuccessful();

        // 3) Create payment intent (API): POST /api/orders/{id}/payments
        $client->request('POST', sprintf('/api/orders/%d/payments', $order->getId()));
        self::assertResponseIsSuccessful();
        $intentData = json_decode($client->getResponse()->getContent() ?: '{}', true);
        $intent = $intentData['intentId'] ?? 'pi_test_' . bin2hex(random_bytes(4));

        // 4) Simulate payment webhook
        $client->request('POST', '/api/webhooks/payment', server: [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_PROVIDER'   => 'mock',
            'HTTP_X_EVENT_ID'   => 'evt_e2e_1',
        ], content: json_encode([
            'intent'   => $intent,
            'status'   => 'succeeded',
            'currency' => 'USD',
        ], JSON_THROW_ON_ERROR));
        self::assertResponseIsSuccessful();

        // 5) Simulate refund webhook
        $client->request('POST', '/api/webhooks/refund', server: [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_X_PROVIDER'   => 'mock',
            'HTTP_X_EVENT_ID'   => 'evt_e2e_ref_1',
        ], content: json_encode([
            'intent'   => $intent,
            'status'   => 'succeeded',
            'currency' => 'USD',
        ], JSON_THROW_ON_ERROR));
        self::assertResponseIsSuccessful();
    }
}
