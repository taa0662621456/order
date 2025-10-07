<?php

namespace App\Service\DiscountSale\Rule;
use App\Service\Order\OrderDiscount\OrderDiscount;
use App\ServiceInterface\Order\OrderDiscount\OrderDiscountRule\PromotionSubjectInterface;
use App\ServiceInterface\Order\OrderDiscount\OrderDiscountRule\RuleCheckerInterface;

final class ItemTotalRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'item_total';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        return $subject->getPromotionSubjectTotal() >= $configuration['amount'];
    }
}