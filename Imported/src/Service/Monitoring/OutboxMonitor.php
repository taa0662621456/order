<?php
declare(strict_types=1);

namespace App\Service\Monitoring;
use App\Entity\Message\Message;

use App\Entity\Message\OutboxMessage;
use Doctrine\ORM\EntityManagerInterface;

final class OutboxMonitor
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function stats(): array
    {
        $repo = $this->em->getRepository(OutboxMessage::class);
        $pending = $repo->count(['status' => 'pending']);
        $dispatched = $repo->count(['status' => 'dispatched']);
        return ['pending' => $pending, 'dispatched' => $dispatched];
    }
}
