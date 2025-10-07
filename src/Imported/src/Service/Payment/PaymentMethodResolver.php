<?php

namespace App\Service\Payment;
use App\Entity\Payment\Payment;

use App\EntityInterface\Payment\PaymentInterface;
use Composer\Repository\RepositoryInterface;

final readonly class PaymentMethodResolver implements PaymentMethodsResolverInterface
{
    public function __construct(private RepositoryInterface $paymentMethodRepository)
    {
    }

    public function getSupportedMethods(PaymentInterface $subject): array
    {
        return $this->paymentMethodRepository->findBy(['enabled' => true]);
    }

    public function supports(PaymentInterface $subject): bool
    {
        return true;
    }
}
