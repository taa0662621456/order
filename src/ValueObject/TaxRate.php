<?php
namespace App\ValueObject;
use App\Entity\Tax\Enums\TaxType;
use App\EntityTrait\ObjectAuditTrait;

class TaxRate implements TaxRateInterface
{
    use ObjectAuditTrait;
    public function __construct(
        public readonly string $regionCode,     // e.g. US-CA, CA-ON, AU-NSW, UA, EU
        public readonly TaxType $type,
        public readonly float $rate,            // percentage, e.g. 10.0 for 10%
        public readonly ?string $province = null, // optional subregion
        public readonly ?\DateTimeImmutable $effectiveFrom = null,
        public readonly ?\DateTimeImmutable $effectiveTo = null,
        public readonly array $meta = []        // extensible (e.g. thresholds, notes)
    ) {}
}
