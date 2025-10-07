<?php
declare(strict_types=1);

namespace App\Application\MessageHandler;

use App\Application\Message\RefundPayment;
use App\Service\Payment\RefundService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RefundPaymentHandler
{
    public function __construct(private readonly RefundService $service) {}
    public function __invoke(RefundPayment $msg): bool
    {
        return $this->service->refund($msg->orderId, $msg->amount);
    }
}
