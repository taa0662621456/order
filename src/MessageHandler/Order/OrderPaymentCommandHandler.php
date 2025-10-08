<?php
declare(strict_types=1);

namespace OrderComponent\MessageHandler\Order;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;
use OrderComponent\Message\Order\OrderPaymentCommand;
use OrderComponent\Service\Order\Adapter\Payment\PaymentGatewayInterface;
use OrderComponent\Service\Order\PaymentService;
use OrderComponent\Service\Order\TransactionalEventPublisher;

#[AsMessageHandler]
final readonly class OrderPaymentCommandHandler
{
    public function __construct(
        private PaymentService              $service,
        private TransactionalEventPublisher $publisher,
        private PaymentGatewayInterface     $gateway
    ) {}

    public function __invoke(OrderPaymentCommand $cmd): void
    {
        $txId = $this->gateway->charge($cmd->orderId, $cmd->amount, ['source' => 'api']);
        $this->service->applyPayment($cmd->orderId, $cmd->amount, $txId);
        $this->publisher->publish('order.paid', ['orderId' => $cmd->orderId, 'amount' => $cmd->amount, 'txId' => $txId]);
    }
}
