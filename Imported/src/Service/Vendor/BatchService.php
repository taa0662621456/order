<?php
namespace App\Service\Vendor;
use App\Entity\Vendor\Vendor;
use Doctrine\ORM\EntityManagerInterface;
class BatchService {
  public function __construct(private EntityManagerInterface $em) {}
  public function persistAndFlushAll(array $entities,int $batchSize=20): void {
    $i=0;
    foreach($entities as $entity){
      $this->em->persist($entity);
      if((++$i % $batchSize)===0){$this->em->flush();$this->em->clear();}
    }
    $this->em->flush();
    $this->em->clear();
  }
}
