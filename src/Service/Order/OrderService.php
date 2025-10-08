<?php
declare(strict_types=1);
namespace OrderComponent\Service\Order;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Repository\Order\OrderRepository;
use OrderComponent\ServiceInterface\Order\OrderServiceInterface;
use OrderComponent\ValueObject\Order\OrderStatus;
use Throwable;

final readonly class OrderService implements OrderServiceInterface
{
    public function __construct(private EntityManagerInterface $em, private OrderRepository $orders) {}

    /**
     * @throws \Throwable
     */
    public function create(Order $order): Order { $this->em->beginTransaction(); try { $this->orders->save($order, false); $this->em->flush(); $this->em->commit(); return $order; } catch (Throwable $e) { $this->em->rollback(); throw $e; } }

    /**
     * @throws \Throwable
     */
    public function update(Order $order): Order { $this->em->beginTransaction(); try { $this->orders->save($order, false); $order->touch(); $this->em->flush(); $this->em->commit(); return $order; } catch (Throwable $e) { $this->em->rollback(); throw $e; } }

    /**
     * @throws \Throwable
     */
    public function delete(Order $order): void { $this->em->beginTransaction(); try { $this->em->remove($order); $this->em->flush(); $this->em->commit(); } catch (Throwable $e) { $this->em->rollback(); throw $e; } }
    public function find(int $id): ?Order { return $this->orders->find($id); }

    /**
     * @throws \Throwable
     */
    public function transitionStatus(Order $order, OrderStatus $to): Order { $order->setStatus($to); return $this->update($order); }
}
