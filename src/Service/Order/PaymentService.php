<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order;

final class PaymentService
{
    public function applyPayment(string $orderId, string $amount, string $txId): void
    {
        // Тут привязка к WriteModel + перерасчёт paid_total (опущено для краткости)
        // Событие отправляется через TransactionalEventPublisher в хендлере
    }
}
