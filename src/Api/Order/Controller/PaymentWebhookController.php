<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use OrderComponent\Service\Order\WebhookIdempotencyService;
use OrderComponent\Message\Order\OrderPartialPaymentCommand;

final class PaymentWebhookController
{
    public function __construct(
        private WebhookIdempotencyService $idem,
        private MessageBusInterface $bus
    ) {}

    #[Route('/webhooks/payment', name: 'order_payment_webhook', methods: ['POST'])]
    public function __invoke(Request $req): JsonResponse
    {
        $idemKey = $req->headers->get('Idempotency-Key') ?? $req->get('event_id', '');
        if (!$idemKey) {
            return new JsonResponse(['error' => 'missing idempotency key'], 400);
        }
        if (!$this->idem->acceptOnce($idemKey)) {
            return new JsonResponse(['status' => 'duplicate'], 200);
        }

        $payload = json_decode($req->getContent() ?: '[]', true);
        $orderId = (string)($payload['orderId'] ?? '');
        $amount  = (string)($payload['amount'] ?? '0.00');
        $method  = (string)($payload['method'] ?? 'card');

        if ($orderId && $amount) {
            $this->bus->dispatch(new OrderPartialPaymentCommand($orderId, $amount, $method));
        }
        return new JsonResponse(['status' => 'ok'], 202);
    }
}
