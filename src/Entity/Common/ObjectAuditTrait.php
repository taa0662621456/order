<?php
declare(strict_types=1);
namespace OrderComponent\Entity\Common;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
trait ObjectAuditTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    public function initAudit(): void { if (!isset($this->createdAt)) $this->createdAt = new DateTimeImmutable('now'); }
    public function touch(): void { $this->updatedAt = new DateTimeImmutable('now'); }
}
