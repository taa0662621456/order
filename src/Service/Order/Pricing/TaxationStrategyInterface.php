<?php
declare(strict_types=1);

namespace OrderComponent\Service\Order\Pricing;

use OrderComponent\ValueObject\Order\Money;
use OrderComponent\ValueObject\Order\TaxRate;

interface TaxationStrategyInterface
{
    public function tax(Money $taxBase, TaxRate $rate): Money;
}
