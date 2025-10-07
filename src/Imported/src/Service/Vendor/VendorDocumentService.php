<?php
namespace App\Service\Vendor;
use App\Entity\Event\Event;
use App\Entity\Vendor\Vendor;
use App\Entity\Vendor\VendorDocument;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\Vendor\DocumentUploadedEvent;
use App\DTO\Vendor\DocumentUploadDTO;
class VendorDocumentService {
  public function __construct(private EntityManagerInterface $em, private EventDispatcherInterface $dispatcher) {}
  public function upload(int $vendorId, DocumentUploadDTO $dto): VendorDocument {
    $doc=new VendorDocument($vendorId,$dto->path);
    $this->em->persist($doc);
    $this->dispatcher->dispatch(new DocumentUploadedEvent($doc, $vendorId, new \DateTimeImmutable()));
    return $doc;
  }
}
