<?php

namespace App\Entity\Order;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Api\Filter\RelationFilterTrait;
use App\Api\Filter\ShipmentCoreFilterTrait;
use App\Api\Filter\TimestampFilterTrait;
use App\Controller\ObjectCRUDsController;
use App\EntityInterface\Order\OrderPaymentEnUsInterface;
use App\EntityTrait\ObjectAuditTrait;
use App\EntityTrait\ObjectTrait;
use App\Repository\Order\OrderPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'order_payment')]
#[ORM\Index(columns: ['slug'], name: 'order_payment_idx')]
#[ORM\Entity(repositoryClass: OrderPaymentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#
#[ApiResource(
    operations: [
        new GetCollection(
            paginationEnabled: false,
            order: ['createdAt' => 'DESC'],
            normalizationContext: ['groups' => ['read','list']],
            denormalizationContext: ['groups' => ['write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['read','item']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['write']]
        ),
        new Put(
            denormalizationContext: ['groups' => ['write']]
        ),
        new Delete(),
        new Get(
            uriTemplate: '/{_entity}/show/{slug}',
            controller: ObjectCRUDsController::class,
            normalizationContext: ['groups' => ['read','item']],
            name: 'get_by_slug'
        )
    ]
)]
class OrderPaymentEnUs implements OrderPaymentEnUsInterface
{
    use ObjectAuditTrait; // Indexing and audition properties/columns
    use ObjectTrait; // Titling properties/columns
    # API Filters
    use TimestampFilterTrait;
    use ShipmentCoreFilterTrait;
    use RelationFilterTrait;

}
