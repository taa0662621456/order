<?php
declare(strict_types=1);

namespace OrderComponent\Interface\EntityInterface\Order;

use DateTimeImmutable;

interface OrderAuditLogInterface
{
    public function orderId(): string;
    public function action(): string;
    public function details(): ?string;
    public function createdAt(): DateTimeImmutable;
}
