<?php

namespace App\Service\Payment;
use App\Entity\Payment\Payment;
use App\Interface\RepositoryInterface;
use App\EntityInterface\Payment\PaymentMethodInterface;

use App\EntityInterface\Payment\PaymentInterface;
use App\RepositoryInterface\Payment\PaymentMethodRepositoryInterface;

final readonly class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    public function __construct(private PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
    }

    /**
     * @throws UnresolvedDefaultPaymentMethodException
     */
    public function getDefaultPaymentMethod(PaymentInterface $payment): PaymentMethodInterface
    {
        $paymentMethods = $this->paymentMethodRepository->findBy(['enabled' => true]);
        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        return $paymentMethods[0];
    }
}
