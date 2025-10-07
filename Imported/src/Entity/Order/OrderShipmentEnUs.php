<?php

namespace App\Entity\Order;

use ApiPlatform\Metadata\ApiResource;
use App\EntityInterface\Order\OrderShipmentEnUsInterface;
use App\EntityTrait\ObjectAuditTrait;
use App\EntityTrait\ObjectTrait;
use App\Repository\Order\OrderShipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'order_shipment_en')]
#[ORM\Index(columns: ['slug'], name: 'order_shipment_idx')]
#[ORM\Entity(repositoryClass: OrderShipmentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#
#[ApiResource]


class OrderShipmentEnUs implements OrderShipmentEnUsInterface
{
    use ObjectAuditTrait;
    use ObjectTrait;

}
