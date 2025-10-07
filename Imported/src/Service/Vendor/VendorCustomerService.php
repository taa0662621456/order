<?php
namespace App\Service\Vendor;
use App\Entity\Event\Event;
use App\Entity\Vendor\Vendor;

use App\Entity\Vendor\VendorCustomer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Vendor\CustomerCreatedEvent;
use App\DTO\Vendor\CustomerCreateDTO;
use Throwable;

final class VendorCustomerService 
{
    public function __construct(
        private readonly EntityManagerInterface $em, 
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function create(int $vendorId, CustomerCreateDTO $dto): VendorCustomer 
    {
        $vendor = $this->em->getRepository('App:Vendor')->find($vendorId);
        if (!$vendor) {
            throw new \InvalidArgumentException('Vendor not found: '.$vendorId);
        }

        $customer = new VendorCustomer($vendor, $dto->name);
        if (!empty($dto->email)) { $customer->email = $dto->email; }
        if (!empty($dto->phone)) { $customer->phone = $dto->phone; }

        $this->em->persist($customer);
        $this->em->flush();

        try {
            $this->dispatcher->dispatch(new CustomerCreatedEvent($customer, new \DateTimeImmutable()));
        } catch (Throwable) {
            // ignore missing listeners
        }
        return $customer;
    }
}
