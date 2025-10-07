<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Embeddable]
class PaymentMethod {
    #[ORM\Column(type: 'string', length: 32)]
    private string $value;
    private function __construct(string $v){ $this->value=$v; }
    public static function of(string $v): self { return new self($v); }
    public function __toString(): string { return $this->value; }
}