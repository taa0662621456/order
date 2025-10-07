<?php
declare(strict_types=1);
namespace App\Service\Rma;

use App\DTO\ReturnRequestDTO;
use App\DTO\ExchangeRequestDTO;

final class RmaService
{
    public function requestReturn(ReturnRequestDTO $dto): string
    {
        return 'rma_' . uniqid();
    }
    public function requestExchange(ExchangeRequestDTO $dto): string
    {
        return 'ex_' . uniqid();
    }
}
