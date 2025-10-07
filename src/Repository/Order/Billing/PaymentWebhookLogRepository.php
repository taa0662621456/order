<?php
declare(strict_types=1);

namespace OrderComponent\Repository\Order\Billing;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OrderComponent\Entity\Order\Billing\PaymentWebhookLog;

class PaymentWebhookLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentWebhookLog::class);
    }
}
