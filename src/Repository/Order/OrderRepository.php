<?php
declare(strict_types=1);
namespace OrderComponent\Repository\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OrderComponent\Entity\Order;
use OrderComponent\ValueObject\Order\OrderStatus;
use OrderComponent\ValueObject\Order\VendorId;
use OrderComponent\RepositoryInterface\Order\OrderRepositoryInterface;

final class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry){ parent::__construct($registry, Order::class); }
    public function save(Order $order, bool $flush = true): void { $this->_em->persist($order); if ($flush) $this->_em->flush(); }
    public function findByStatus(OrderStatus $status): ?Order { return $this->findOneBy(['status'=>$status]); }
    public function findRecentByVendor(VendorId $vendorId): ?Order { return $this->createQueryBuilder('o')->orderBy('o.id','DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult(); }
}
