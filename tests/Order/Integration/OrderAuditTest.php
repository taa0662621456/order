<?php
declare(strict_types=1);

namespace Tests\Order\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Repository\Order\OrderEventRepository;
use OrderComponent\Entity\Order\OrderEventRecord;
use Symfony\Component\Uid\Uuid;

final class OrderAuditTest extends KernelTestCase
{
    public function test_event_repository_persists_and_reads(): void
    {
        self::bootKernel();
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);
        /** @var OrderEventRepository $repo */
        $repo = self::getContainer()->get(OrderEventRepository::class);

        $orderId = Uuid::v7()->toRfc4122();
        $eventId = Uuid::v7()->toRfc4122();

        $record = new OrderEventRecord($eventId, $orderId, 'OrderPlacedEvent', ['number' => 'ORD-1']);
        $repo->save($record);
        $em->flush();
        $em->clear();

        $last = $repo->findLastByOrder($orderId);
        self::assertNotNull($last);
        self::assertSame('OrderPlacedEvent', $last->eventName());
    }
}
