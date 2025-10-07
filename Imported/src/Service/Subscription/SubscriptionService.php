<?php
declare(strict_types=1);
namespace App\Service\Subscription;
use App\DTO\SubscriptionPlanDTO;
use App\DTO\SubscriptionDTO;
final class SubscriptionService {
    public function start(SubscriptionDTO $dto, SubscriptionPlanDTO $plan): string {
        // calc next billing date
        $next = new \DateTimeImmutable('now');
        $next = $plan->periodicity === 'yearly' ? $next->modify('+1 year') : $next->modify('+1 month');
        if ($plan->trialDays > 0) $next = (new \DateTimeImmutable('now'))->modify('+' . $plan->trialDays . ' days');
        return 'sub_'.uniqid();
    }
    public function cancel(string $subscriptionId, bool $atPeriodEnd=true): bool {
        return true;
    }
}
