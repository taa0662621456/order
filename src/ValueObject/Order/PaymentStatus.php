<?php
declare(strict_types=1);
namespace OrderComponent\ValueObject\Order;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Embeddable]
class PaymentStatus {
    #[ORM\Column(type: 'string', length: 16)]
    private string $value;
    private function __construct(string $v){ $this->value=$v; }
    public static function pending(): self { return new self('pending'); }
    public static function confirmed(): self { return new self('confirmed'); }
    public static function failed(): self { return new self('failed'); }
    public static function refunded(): self { return new self('refunded'); }
    public function __toString(): string { return $this->value; }
}