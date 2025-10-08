<?php
declare(strict_types=1);

namespace OrderComponent\Subscriber\Order;

use Cassandra\Uuid;
use DateTimeInterface;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order\OrderEventRecord;
use OrderComponent\Entity\Order\OrderAuditLog;
use OrderComponent\Interface\RepositoryInterface\Order\OrderEventRepositoryInterface;

final readonly class OrderAuditSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em, private OrderEventRepositoryInterface $repo) {}

    public static function getSubscribedEvents(): array
    {
        // Подпишемся на все основные события домена
        return [
            'order.placed' => 'onAny',
            'order.paid' => 'onAny',
            'order.shipped' => 'onAny',
            'order.cancelled' => 'onAny',
            'order.refunded' => 'onAny',
        ];
    }

    public function onAny(object $event): void
    {
        $orderId = $this->extract($event, ['orderId','getOrderId']);
        if (!$orderId) { return; }

        $eventId = $this->extract($event, ['eventId','getEventId']) ?: Uuid::v7()->toRfc4122();
        if ($this->repo->existsByEventId($eventId)) { return; } // идемпотентность

        $name = $event::class;
        $payload = $this->normalizeEvent($event);

        $record = new OrderEventRecord($eventId, $orderId, $name, $payload);
        $this->repo->save($record);

        $action = (new ReflectionClass($event))->getShortName();
        $audit = new OrderAuditLog(Uuid::v7()->toRfc4122(), $orderId, $action, json_encode($payload, JSON_UNESCAPED_SLASHES));
        $this->em->persist($audit);
        // Без flush здесь — внеший unit-of-work контролирует транзакцию
    }

    private function extract(object $event, array $methods): ?string
    {
        foreach ($methods as $m) {
            if (method_exists($event, $m)) {
                $v = $event->$m();
                if (is_string($v) && $v !== '') return $v;
            }
        }
        return null;
    }

    private function normalizeEvent(object $event): array
    {
        // Пытаемся аккуратно сериализовать объект события
        if (method_exists($event, 'toArray')) { return $event->toArray(); }
        $data = [];
        foreach (get_object_vars($event) as $k => $v) {
            $data[$k] = $this->normalizeValue($v);
        }
        return $data;
    }

    private function normalizeValue(mixed $v): mixed
    {
        return match(true) {
            $v instanceof DateTimeInterface => $v->format(DATE_ATOM),
            is_object($v) => (array)$v,
            default => $v
        };
    }
}
