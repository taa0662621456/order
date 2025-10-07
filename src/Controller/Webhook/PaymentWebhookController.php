<?php
declare(strict_types=1);

namespace OrderComponent\Controller\Webhook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OrderComponent\Service\Order\Billing\WebhookHandler;

final class PaymentWebhookController
{
    public function __construct(private readonly WebhookHandler $handler) {}

    #[Route(path: '/api/webhooks/payment', name: 'order_payment_webhook', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->handler->handlePayment($request);
        return new JsonResponse($data, 200);
    }

    #[Route(path: '/api/webhooks/refund', name: 'order_refund_webhook', methods: ['POST'])]
    public function refund(Request $request): JsonResponse
    {
        $data = $this->handler->handleRefund($request);
        return new JsonResponse($data, 200);
    }
}
