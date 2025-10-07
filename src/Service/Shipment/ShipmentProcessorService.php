<?php
declare(strict_types=1);
namespace OrderComponent\Service\Shipment;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderShipment;
final class ShipmentProcessorService
{
    public function __construct(private readonly CarrierInterface $carrier, private readonly EntityManagerInterface $em) {}
    public function ship(Order $order, string $carrierName='UPS'): OrderShipment
    { $tracking=$this->carrier->createShipment($carrierName, $order->getId()??0); $s=new OrderShipment($order,$carrierName,$tracking); $s->markShipped($tracking); $this->em->persist($s); return $s; }
}
