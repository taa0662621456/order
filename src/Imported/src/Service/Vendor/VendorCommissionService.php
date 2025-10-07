<?php
namespace App\Service\Vendor;
use App\Entity\Event\Event;
use App\Entity\Vendor\Vendor;
use App\Entity\Vendor\VendorCommission;
use App\Entity\Vendor\VendorCommissionHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Vendor\CommissionChangedEvent;
use App\DTO\Vendor\CommissionSetDTO;
class VendorCommissionService {
  public function __construct(private EntityManagerInterface $em, private EventDispatcherInterface $dispatcher) {}
  public function setCommission(int $vendorId, CommissionSetDTO $dto): VendorCommission {
    $vendor=$this->em->getRepository('App:Vendor')->find($vendorId);
    $com=new VendorCommission($vendorId,$dto->percent);
    $this->em->persist($com);
    // add history
    $history=new VendorCommissionHistory($vendor,$dto->percent);
    $this->em->persist($history);
    $this->dispatcher->dispatch(new CommissionChangedEvent($com,new \DateTimeImmutable()));
    return $com;
  }
}
