<?php
declare(strict_types=1);
namespace App\Service\Payment;
use App\Entity\Payment\Payment;

use App\DTO\PaymentDTO;

final class PaymentService
{
    public function __construct(private readonly ProviderRegistry $registry) {}

    public function authorize(PaymentDTO $dto): PaymentDTO
    {
        $provider = $this->registry->get((string)$dto->provider);
        return $provider?->authorize($dto) ?? $dto;
    }
    public function capture(PaymentDTO $dto): PaymentDTO
    {
        $provider = $this->registry->get((string)$dto->provider);
        return $provider?->capture($dto) ?? $dto;
    }
    public function refund(PaymentDTO $dto): PaymentDTO
    {
        $provider = $this->registry->get((string)$dto->provider);
        return $provider?->refund($dto) ?? $dto;
    }
}
