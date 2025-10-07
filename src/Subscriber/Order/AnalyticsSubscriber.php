<?php
declare(strict_types=1);
namespace OrderComponent\Subscriber\Order;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Analytics\AnalyticsRecord;

final class AnalyticsSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}
    public static function getSubscribedEvents(): array
    {
        return [
            'OrderComponent\\Event\\Order\\OrderPaidEvent' => 'onPaid'
        ];
    }
    public function onPaid(object $event): void
    {
        $orderId = (int)($event->orderId ?? 0);
        $this->em->persist(new AnalyticsRecord('paid', $orderId));
        $this->em->flush();
    }
}
