<?php

namespace App\Service\Payment;
use App\Entity\Payment\Payment;

use App\EntityInterface\Payment\PaymentInterface;

final readonly class CompositeMethodResolver implements PaymentMethodsResolverInterface
{
    public function __construct(private PrioritizedServiceRegistryInterface $resolversRegistry)
    {
    }

    public function getSupportedMethods(PaymentInterface $subject): array
    {
        /** @var PaymentMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($subject)) {
                return $resolver->getSupportedMethods($subject);
            }
        }

        return [];
    }

    public function supports(PaymentInterface $subject): bool
    {
        /** @var PaymentMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($subject)) {
                return true;
            }
        }

        return false;
    }
}
