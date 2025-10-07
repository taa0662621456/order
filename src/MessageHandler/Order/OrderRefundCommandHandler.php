<?php
declare(strict_types=1);

namespace OrderComponent\MessageHandler\Order;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use OrderComponent\Message\Order\OrderRefundCommand;
use OrderComponent\Service\Order\RefundService;
use OrderComponent\Service\Order\TransactionalEventPublisher;

#[AsMessageHandler]
final class OrderRefundCommandHandler
{
    public function __construct(private RefundService $service, private TransactionalEventPublisher $publisher) {}

    public function __invoke(OrderRefundCommand $cmd): void
    {
        $tx = $this->service->refund($cmd->orderId, $cmd->amount, $cmd->reason);
        $this->publisher->publish('order.refunded', ['orderId'=>$cmd->orderId,'amount'=>$cmd->amount,'refundId'=>$tx->refundId()]);
    }
}
