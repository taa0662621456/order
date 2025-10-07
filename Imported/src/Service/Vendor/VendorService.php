<?php
declare(strict_types=1);
namespace App\Service\Vendor;

use App\DTO\VendorPayoutDTO;
use App\DTO\VendorDTO;
final class VendorService {
    public function save(VendorDTO $dto): string { return $dto->code ?? ('v_'.uniqid()); }
    public function requestPayout(VendorPayoutDTO $dto): string { return 'payout_'.uniqid(); }
}
