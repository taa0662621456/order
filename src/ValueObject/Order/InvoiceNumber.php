<?php
declare(strict_types=1);

namespace OrderComponent\ValueObject\Order;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class InvoiceNumber
{
    #[ORM\Column(type: 'string', length: 64)]
    private string $value;

    private function __construct(string $value) { $this->value = $value; }

    public static function of(string $value): self { return new self($value); }

    public function __toString(): string { return $this->value; }
}
