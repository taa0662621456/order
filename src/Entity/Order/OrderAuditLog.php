<?php
declare(strict_types=1);

namespace OrderComponent\Entity\Order;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_audit_log')]
#[ORM\Index(columns: ['order_id'])]
class OrderAuditLog
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private string $id;

    #[ORM\Column(name: 'order_id', type: 'guid')]
    private string $orderId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $action;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $id, string $orderId, string $action, ?string $details = null, ?DateTimeImmutable $createdAt = null)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->action = $action;
        $this->details = $details;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function id(): string { return $this->id; }
    public function orderId(): string { return $this->orderId; }
    public function action(): string { return $this->action; }
    public function details(): ?string { return $this->details; }
    public function createdAt(): DateTimeImmutable { return $this->createdAt; }
}
