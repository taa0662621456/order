<?php
namespace App\Service\Vendor;
use App\Entity\Vendor\Vendor;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Vendor\VendorLog;
class AuditLoggerService {
  public function __construct(private EntityManagerInterface $em) {}
  public function log(string $eventType,array $payload=[],?int $userId=null,?int $vendorId=null,?string $ip=null,?string $ua=null): void {
    $payload['ip']=$ip; $payload['ua']=$ua;
    $log=new VendorLog($eventType,$payload,$userId,$vendorId);
    $this->em->persist($log);
    $this->em->flush();
  }
}
