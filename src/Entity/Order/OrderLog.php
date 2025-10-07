<?php

namespace App\Entity\Order;

use App\EntityInterface\Order\OrderLogInterface;
use App\EntityTrait\ObjectAuditTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class OrderLog implements OrderLogInterface
{
    use ObjectAuditTrait;

    #[ORM\Column(type: 'string', length: 20)]
    private string $orderStatusCode;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment;

    #[ORM\Column(type: 'boolean')]
    private bool $customerNotified;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $oHash;

    public function __construct(string $statusCode, ?string $comment = null, bool $notified = false)
    {
        $this->orderStatusCode = $statusCode;
        $this->comment = $comment;
        $this->customerNotified = $notified;
    }

    public function getId(): ?int { return $this->id; }
    public function getOrderStatusCode(): string { return $this->orderStatusCode; }
    public function getComment(): ?string { return $this->comment; }
    public function isCustomerNotified(): bool { return $this->customerNotified; }
    public function getHash(): ?string { return $this->oHash; }
}
