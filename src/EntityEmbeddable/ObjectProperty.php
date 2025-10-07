<?php

namespace App\EntityEmbeddable;
use App\Entity\Embeddable\ObjectPropertyInterface;
use App\Entity\Vendor\Vendor;
use App\EntityTrait\ObjectAuditTrait;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ObjectProperty implements ObjectPropertyInterface
{
    use ObjectAuditTrait;

    #[ORM\Column(name: 'current_state', type: 'string', length: 32, options: ['default' => 'submitted'])]
    private string $currentState = 'submitted';

    #[ORM\Column(name: 'last_request_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastRequestDate = null;

    // === Helpers ===

    public function markCreated(int $userId): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->createdBy = $userId;
    }

    public function markModified(int $userId): void
    {
        $this->modifiedAt = new DateTimeImmutable();
        $this->modifiedBy = $userId;
    }

    public function markLocked(int $userId): void
    {
        $this->lockedAt = new DateTimeImmutable();
        $this->lockedBy = $userId;
    }

    public function markLastRequestNow(): void
    {
        $this->lastRequestDate = new DateTimeImmutable();
    }

    public function isInState(string $state): bool
    {
        return $this->currentState === $state;
    }

    public function isAuthor(Vendor $vendor = null): bool
    {
        return $vendor && $vendor->getId() === $this->createdBy;
    }

    // === Getters & setters ===

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }


    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): void
    {
        $this->currentState = $currentState ?: 'submitted';
    }

       public function getLastRequestDate(): ?DateTimeImmutable
    {
        return $this->lastRequestDate;
    }

    public function setLastRequestDate(?DateTimeImmutable $lastRequestDate): void
    {
        $this->lastRequestDate = $lastRequestDate;
    }
}
