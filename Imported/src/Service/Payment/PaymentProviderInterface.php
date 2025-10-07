<?php
declare(strict_types=1);
namespace App\Service\Payment;
use App\Entity\Payment\Payment;

use App\DTO\PaymentDTO;

interface PaymentProviderInterface
{
    public function authorize(PaymentDTO $dto): PaymentDTO;
    public function capture(PaymentDTO $dto): PaymentDTO;
    public function refund(PaymentDTO $dto): PaymentDTO;
    public function supportsCurrency(string $currency): bool;
}
