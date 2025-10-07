<?php
declare(strict_types=1);

namespace OrderComponent\Api\Order\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\ReadModel\Entity\OrderView;
use OrderComponent\Api\Order\Resource\OrderResource;

final class OrderProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $repo = $this->em->getRepository(OrderView::class);
        if (isset($uriVariables['id'])) {
            $view = $repo->find($uriVariables['id']);
            return $view ? $this->map($view) : null;
        }
        $list = $repo->findBy([], ['createdAt' => 'DESC'], 100);
        return array_map(fn($v) => $this->map($v), $list);
    }

    private function map(OrderView $v): OrderResource
    {
        $r = new OrderResource();
        $r->id = $v->getId();
        $r->number = $v->getNumber();
        $r->status = $v->getStatus();
        $r->currency = $v->getCurrency();
        $r->grandTotal = $v->getGrandTotal();
        $r->paidTotal = $v->getPaidTotal();
        $r->refundedTotal = $v->getRefundedTotal();
        $r->customerId = $v->getCustomerId();
        $r->vendorId = $v->getVendorId();
        return $r;
    }
}
