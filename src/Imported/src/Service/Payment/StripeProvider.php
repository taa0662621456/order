<?php
declare(strict_types=1);
namespace App\Service\Payment;
use App\Entity\Payment\Payment;

use App\DTO\PaymentDTO;

final class StripeProvider implements PaymentProviderInterface
{
    public function authorize(PaymentDTO $dto): PaymentDTO { $dto->status='authorized'; $dto->transactionId='tx_'.uniqid(); return $dto; }
    public function capture(PaymentDTO $dto): PaymentDTO { $dto->status='captured'; return $dto; }
    public function refund(PaymentDTO $dto): PaymentDTO { $dto->status='refunded'; return $dto; }
    public function supportsCurrency(string $currency): bool { return in_array($currency, ['USD','EUR','GBP'], true); }
}
