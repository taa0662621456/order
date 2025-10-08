<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Analytics\AnalyticsRecord;
use OrderComponent\Event\Order\OrderPaidEvent;

final readonly class AnalyticsSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em) {}
    public static function getSubscribedEvents(): array { return [OrderPaidEvent::class => 'onPaid']; }
    public function onPaid(OrderPaidEvent $e): void { $this->em->persist(new AnalyticsRecord('paid', $e->orderId)); $this->em->flush(); }
}
