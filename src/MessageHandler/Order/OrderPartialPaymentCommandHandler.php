<?php
declare(strict_types=1);

namespace OrderComponent\MessageHandler\Order;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use OrderComponent\Message\Order\OrderPartialPaymentCommand;
use OrderComponent\Service\Order\PartialPaymentService;
use OrderComponent\Service\Order\Adapter\Payment\PaymentGatewayInterface;
use OrderComponent\Service\Order\TransactionalEventPublisher;

#[AsMessageHandler]
final class OrderPartialPaymentCommandHandler
{
    public function __construct(
        private PartialPaymentService $service,
        private PaymentGatewayInterface $gateway,
        private TransactionalEventPublisher $publisher
    ) {}

    public function __invoke(OrderPartialPaymentCommand $cmd): void
    {
        $txId = $this->gateway->charge($cmd->orderId, $cmd->amount);
        $this->service->applyPartial($cmd->orderId, $cmd->amount, $cmd->method, $txId);
        $this->publisher->publish('order.paid.partial', ['orderId' => $cmd->orderId, 'amount' => $cmd->amount, 'txId' => $txId]);
    }
}
