<?php
declare(strict_types=1);
namespace App\Service\Customer;
use App\Entity\Vendor\Customer;
use App\DTO\CustomerDTO;
final class CustomerService {
    public function register(CustomerDTO $dto): string { return 'cust_'.uniqid(); }
}