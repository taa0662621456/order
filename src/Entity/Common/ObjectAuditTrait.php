<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Common;
use Doctrine\ORM\Mapping as ORM;
trait ObjectAuditTrait
{
    #[ORM\Column(type: 'guid', unique: true)]
    private string $uuid;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
    public function initAudit(): void
    {
        if (!isset($this->uuid)) $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        if (!isset($this->createdAt)) $this->createdAt = new \DateTimeImmutable('now');
    }
    public function touch(): void { $this->updatedAt = new \DateTimeImmutable('now'); }
    public function getUuid(): string { return $this->uuid; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}
